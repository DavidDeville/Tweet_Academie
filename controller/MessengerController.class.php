<?php

require_once 'PageController.class.php';
require_once 'FormController.class.php';
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
    // $this->form = new FormController();
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
      $this->send_message();
    }
  }

  private function conv_selected()
  {
    return(
      isset($_GET['id']) && $_GET['id'] !== ""
    );
  }

  private function display_conv()
  {
    echo $this->twig->render('mail_history.htm.twig', [
      'messages' => $this->message->content_conv($_GET['id']),
      'account_name' => $this->user->get_account_name(),
      'members' => implode(', ',
      $this->message->get_conversation_members($_GET['id']))
    ]);
  }

  private function display_list()
  {
    // vue
  }

  private function form_submitted()
  {
    return(
      isset($_POST['message'])
    );
  }

  private function send_messsage()
  {
    $this->message->send_message(
      $_POST['message'],
      $this->user->get_account_id(),
      $_GET['id']
    );
  }
}

?>
