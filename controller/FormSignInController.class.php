<?php

ini_set('display_errors', 1);

require_once 'FormController.class.php';

/*
** Controller for the sign-in form
*/
final class FormSignInController extends FormController
{
    /*
    ** Checks if the whole form is valid
    **
    ** @return bool: true if the form is valid, false otherwise
    */
    public function is_valid()
    { 
        $this->check_mail();
        $this->check_password();

        return (
            parent::is_valid()
        );
    }

    /*
    ** Checks if the mail field is filled with a valid address
    **
    ** @return bool: true if the field is filled and the mail is valid, false otherwhise
    */
    protected function check_mail()
    {
        return (
            $this->mail_is_valid(
                'signin-mail', 
                'Please enter a valid mail address'
            )
        );
    }

    /*
    ** Checks if the password field is filled
    **
    ** @return bool: true if the field is filled, false otherwhise
    */
    protected function check_password()
    {
        return (
            $this->field_is_filled(            
                'signin-pwd',
                'Please enter your password'
            )
        );
    }
}

?>