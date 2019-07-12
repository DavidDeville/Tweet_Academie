<?php

require_once 'PageController.class.php';
require_once 'FormMessengerController.class.php';
require_once 'model/UserModel.class.php';
require_once 'model/MessageModel.class.php';

/*
** Controller for messenger.php
*/
final class MessengerController extends PageController
{
  /*
  ** The user model
  */
  private $user;

  /*
  ** The message model
  */
  private $message;

  /*
  ** The messenger form controller
  */
  private $form;

  public function __construct()
  {
    parent::__construct();

    $this->user = new UserModel();
    $this->message = new MessageModel();
    $this->form = new FormMessengerController();
  }

  /*
  ** Displays the appropriate view for the current context
  **      @see PageController::display_view()
  */
  public function display_view()
  {
    if ($this->conv_selected())
    {
      $this->display_conv();
    }
    else
    {
      $this->display_list();
    }
  }

  /*
  ** Decides which action should be done
  **      @see PageController::handle_request()
  */
  public function handle_request()
  {
    if ($this->form_submited())
    {
      if ($this->form_matches('messenger'))
      {
        if ($this->form->is_valid())
        {
          $this->send_message();
        }
      }
    }
  }

  /*
  ** Checks if a conv is selected in URL
  */
  private function conv_selected()
  {
    return(
      isset($_GET['id']) && $_GET['id'] !== ""
    );
  }

  /*
  ** Prints the conversation from the URL's ID
  */
  private function display_conv()
  {
    echo $this->twig->render('mail_history.htm.twig', [
      'messages' => $this->message->content_conv($_GET['id']),
      'account_name' => $this->user->get_account_name(),
      'members' => implode(', ',
      $this->message->get_conversation_members($_GET['id']))
    ]);
  }

  /*
  ** Prints the list of all the user's conversation
  */
  private function display_list()
  {
    echo $this->twig->render('mail_histories.htm.twig', [
      'account_name' => $this->user->get_account_name(),
      'conversations' => $this->message->get_all_convs(
        $this->user->get_account_id()
      )
    ]);
  }

  /*
  ** Ask the model to send a message
  **
  ** @param message from the input
  ** @param User's ID
  ** @param Conversation's ID
  */
  private function send_message()
  {
    $this->message->send_message(
      $_POST['messenger-input'],
      $this->user->get_account_id(),
      $_GET['id']
    );
  }
}

?>
