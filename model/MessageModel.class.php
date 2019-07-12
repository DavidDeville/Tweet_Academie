<?php

require_once 'Model.class.php';

/*
** Model for messages, contains everything to read / write messagaes
*/
class MessageModel extends Model
{
  public function find_conversation(Array $id_participants)
  {
    $all_conv = $this->get_all_convs($id_participants[0]);
    $conv_id = $this->get_conv($all_conv, $id_participants);

    return ($conv_id);
  }

  /*
  ** Gets all the user's conversations
  **
  ** @param int $id_user: current user ID
  **
  ** @return Array $user_convs: contains all of the user's convs
  */
  public function get_all_convs(int $id_user)
  {
    $user_convs = $this->link->prepare(
      'SELECT
        chat_participant.chat_conversation_id AS "conv_id"
      FROM chat_participant
      INNER JOIN user
      ON user.id = chat_participant.user_id
      WHERE user_id = :id_user
      GROUP BY chat_participant.chat_conversation_id
      '
    );
    $user_convs->execute([
      ':id_user' => $id_user
    ]);

    $all_convs  = $user_convs->fetchAll(PDO::FETCH_ASSOC);
    $this->add_participants($all_convs);

    return ($all_convs);
  }

  private function add_participants(Array &$all_convs)
  {
      $partic_query = $this->link->prepare(
        'SELECT GROUP_CONCAT(" ", username) AS participants
        FROM user
        INNER JOIN chat_participant
        ON chat_participant.user_id = user.id
        WHERE chat_participant.chat_conversation_id = :conv'
      );
      foreach ($all_convs as &$conv) // /!\ parcourt avec une réf sur la valeur pour modifier le sous-tableau
      {
        $partic_query->execute([
          ':conv' => $conv['conv_id']
        ]);
        $conv['participants'] = $partic_query->fetch(PDO::FETCH_ASSOC)['participants'];
      }
  }

  /*
  ** Filters the convs to find the correct one
  **
  ** @param Array $user_convs: The convs where the user is present
  ** @param Array $id_participants: The people present in the wanted conv
  **
  ** @return the conv's ID or NULL if the conv doesn't exists
  */
  private function get_conv(Array $all_conv, Array $id_participants)
  {
    sort($id_participants);

    $user = $id_participants[0];

    foreach ($all_conv as $user_conv)
    {
      $conv_members = $this->get_conversation_members($user_conv['conv_id']);
      if ($conv_members === $id_participants)
      {
        return (
          $user_conv['conv_id']
        );
      }
    }
    return (
        NULL
    );
  }

  /*
  ** Gets the ID of the last conversation row, plus one
  **
  ** @return int: the ID for the next conv to use
  */
  private function get_next_id_conv()
  {
    $nbr_id = $this->link->prepare(
      'SELECT MAX(id) AS id FROM chat_conversation'
    );
    $nbr_id->execute();
    return (
      $nbr_id->fetch(PDO::FETCH_ASSOC)['id'] + 1
    );
  }

  /*
  ** Creates all the rows in DB to create the conversation and assigns participants
  **
  ** @param array $id_participants: everyone else in the conversation
  ** @param string $conv_name: the name of the conversation
  */
  public function create_conversation(string $conv_name, array $id_participants)
  {
    $id_conv = $this->get_next_id_conv();
    $conversation_query = $this->link->prepare(
        'INSERT INTO chat_conversation (
          id,
          name)
        VALUES (
          :id_conv,
          :conv_name)'
    );
    $conversation_query->execute([
        ':id_conv' => $id_conv,
        ':conv_name' => 'Test' // $conv_name
    ]);
    foreach($id_participants as $person)
    {
      $this->add_participant($id_conv, $person);
    }
    return (
      $id_conv
    );
  }

  /*
  ** Adds someone in an existing conv
  **
  ** @param int $conv_id: The conv where yu will add the new guy
  ** @param int $user_id: The user to add in the conv
  **
  ** @return bool: True if added, False if not
  */
  public function add_participant(int $conv_id, int $user_id)
  {
    $add_user = $this->link->prepare(
      'INSERT INTO chat_participant (
        chat_conversation_id,
        user_id)
      VALUES (
        :conv_id,
        :user_id)'
    );
    $add_user->execute([
      ':conv_id' => $conv_id,
      ':user_id' => $user_id
    ]);
  }

  /*
  ** Sends a message to someone storing it in DBB
  **
  ** @param int $sender_id: The user sending the message
  ** @param int $conv_id: The conv where the message goes
  ** @param string $message: The message to send
  **
  ** @return
  */
  public function send_message(string $message, int $sender_id, int $conv_id)
  {
    $send = $this->link->prepare(
      'INSERT INTO chat_message (
        conversation_id,
        sender_id,
        content,
        submit_time)
        VALUES (
          :conv_id,
          :sender_id,
          :message,
          NOW()
        )'
    );
    $send->execute([
      ':conv_id' => $conv_id,
      ':sender_id' => $sender_id,
      ':message' => $message
    ]);
  }

  /*
  ** Gets all members of a conversation
  **
  ** @param int $conv_id: the conversation ID to get members from
  **
  ** @return Array: the list of members ID of the conversation
  */
  public function get_conversation_members(int $conv_id)
  {
    $members = [];
    $members_query = $this->link->prepare(
      'SELECT DISTINCT user_id
      FROM chat_participant
      WHERE chat_conversation_id = :conv_id
      ORDER BY user_id'
    );
    $members_query->execute([
      ':conv_id' => $conv_id
    ]);
    while ($member = $members_query->fetch(PDO::FETCH_ASSOC))
    {
      array_push($members, $member['user_id']);
    }
    return (
      $members
    );
  }


  /*
  ** Gets all the conv's messages
  **
  ** @param int $id_conv: The id where we search the messages
  **
  ** @return Array: The senders' id and content of the messages
  */
  public function content_conv(int $id_conv)
  {
    $messages = $this->link->prepare(
      'SELECT user.username,
      user.display_name,
      chat_message.content,
      chat_message.id
      FROM user
      INNER JOIN chat_message
      ON user.id = chat_message.sender_id
      WHERE conversation_id = :id_conv
      ORDER BY submit_time'
    );
    $messages->execute([
      ':id_conv' => $id_conv
    ]);
    return(
      $messages->fetchAll(
        PDO::FETCH_ASSOC
      )
    );
  }

  /*
  ** Gets the new messages from ajax request
  **
  ** @param int $id_conv: The id where we search the messages
  ** @param int $id_msg: the last ID where the search begins
  **
  ** @return Array: The senders' id and content of the messages
  */
  public function new_content_conv(int $id_conv, int $id_msg)
  {
    $newmessages = $this->link->prepare(
      'SELECT user.username,
      user.display_name,
      chat_message.content,
      chat_message.sender_id,
      chat_message.id
      FROM user
      INNER JOIN chat_message
      ON user.id = chat_message.sender_id
      WHERE conversation_id = :id_conv
      AND chat_message.id > :id_msg
      ORDER BY submit_time'
    );
    $newmessages->execute([
      ':id_conv' => $id_conv,
      ':id_msg' => $id_msg
    ]);
    return(
      $newmessages->fetchAll(
        PDO::FETCH_ASSOC
      )
    );
  }

  /*
  ** Gets all messages sent after a given timestamp
  **
  ** @param int $conv: the ID of the conv
  ** @param int $timestamp: the timestamp to compare messages with
  **
  ** @êeturn Array: all messages sent after the timestamp
  */
  public function fetch_messages(int $conv, int $timestamp)
  {
    $latest_messages = $this->link->prepare(
      'SELECT user.username,
      user.display_name,
      chat_message.content,
      chat_message.sender_id,
      chat_message.id
      FROM user
      INNER JOIN chat_message
      ON user.id = chat_message.sender_id
      WHERE
        conversation_id = :id_conv &&
        submit_time > FROM_UNIXTIME(:stamp)
      ORDER BY submit_time'
    );
    $latest_messages->execute([
      ':id_conv' => $conv,
      ':stamp' => $timestamp / 1000
    ]);
    return(
      $latest_messages->fetchAll(
        PDO::FETCH_ASSOC
      )
    );
  }
}

?>
