<?php

ini_set('display_errors', 1);

require_once 'FormController.class.php';

/*
** Form Validation Base Controller
*/

abstract class FormTweetController extends FormController
{
    /*
    ** Checks if the whole form is valid
    ** 
    ** @return bool: true if the form is valid, otherwise, returns false
    */
    public function is_valid()
    {
        $this->check_content();

        return(
            parent::is_valid()
        );
    }

    /* 
    ** Function to check the tweet content
    ** 
    ** @param string $field_name: name of the field inside the form
    **
    ** @param string $error_message: message to display in case it's invalid
    ** 
    ** @return bool: true if the form is valid, otherwise, returns false
    */
    public function check_content()
    {

        if(strlen($_POST['tweet-content']) > 0 && strlen($_POST['tweet-content']) < 141)
        {
            $this->write_status('tweet-content', $this->valid_message);
            return(true);
        }
        else
        {
            $this->write_status('tweet-content', $error_message);
            return(false);
        }
    }
}
?>