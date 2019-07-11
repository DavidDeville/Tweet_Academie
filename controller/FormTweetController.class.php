<?php

ini_set('display_errors', 1);

require_once 'FormController.class.php';

/*
** Form Validation Base Controller
*/
final class FormTweetController extends FormController
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
    ** @return bool: true if the form is valid, otherwise, returns false
    */
    public function check_content()
    {
        return(
            $this->field_is_filled(
                'tweet-content',
                'Tweet is either empty or too long'
            )
        );
    }
}
?>