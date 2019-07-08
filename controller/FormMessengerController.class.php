<?php

require_once 'FormController.class.php';

/*
** Form-validation base controller
*/
final class FormMessengerController extends FormController
{
  /*
  ** The message to display in AJAX response when fields are valid
  */
  protected $valid_message;

  /*
  ** The status of the form, an associative array where keys are
  ** field names and values are error message, if any
  */
  protected $status;

  public function __construct()
  {
      $this->valid_message = 'valid';
      $this->status = [];
  }
}

?>
