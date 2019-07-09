<?php

require_once 'FormController.class.php';

/*
** Form-validation base controller
*/
final class FormMessengerController extends FormController
{
  /*
  ** Checks if the whole form is valid
  **
  ** @return bool: true if the form is valid, false otherwise
  */
  public function is_valid()
  {
      $this->check_message();

      return (
          parent::is_valid()
      );
  }

  /*
  ** Checks if the message field is filled
  **
  ** @return bool: true if the field is filled, false otherwhise
  */
  protected function check_message()
  {
      return (
          $this->field_is_filled(
              'messenger-input',
              'You can not send an empty message'
          )
      );
  }

  /*
  ** Check is the message is not empty
  */
  private function field_is_set()
  {
    return(
      isset($_POST['message'])
    );
  }
}

?>
