<?php

ini_set('display_errors', 1);

require_once 'FormController.class.php';

/*
** Controller for the sign-up form
*/
final class FormSignUpController extends FormController
{
    /*
    ** Checks if the whole form is valid
    **
    ** @return bool: true if the form is valid, false otherwise
    */
    public function is_valid()
    {
        $this->check_pseudo();
        $this->check_account_name();
        $this->check_city();
        $this->check_birthdate();
        $this->check_mail();
        $this->check_password();
        $this->check_password_conf();
        
        return (
            parent::is_valid()
        );
    }

    /*
    ** Checks if the pseudo field is filled (display_name field in user table)
    **
    ** @return bool: true if the field is filled, false otherwhise
    */
    private function check_pseudo() 
    {
        return (
            $this->field_is_filled(
                'signup-username', 
                'Please enter your username'
            )
        );
    }

    /*
    ** Checks if the account name field is filled (username field in user table)
    **
    ** @return bool: true if the field is filled, false otherwhise
    */
    private function check_account_name()
    {   return (
            $this->field_is_filled(
                'signup-accname', 
                'Please enter your account name'
            )
        );
    }

    /*
    ** Checks if the birth-date field is filled (birth_date field in user table)
    **
    ** @return bool: true if the user meets the age requirements, false otherwise
    */
    private function check_birthdate()
    {
        return (
            $this->date_is_valid(
                'signup-dob', 
                'You must be at least 18 years old to sign-up'
            )
        );
    }

    /*
    ** Checks if the city field is filled (city field in user table)
    **
    ** @return bool: true if the field is filled, false otherwhise
    */
    private function check_city()
    {
        return (
            $this->field_is_filled(
                'signup-city', 
                'Please enter your city'
            )
        );
    }
    
    /*
    ** Checks if the mail field is filled with a valid address (email field in user table)
    **
    ** @return bool: true if the field is filled and the mail is valid, false otherwhise
    */
    private function check_mail()
    {
        return (
            $this->mail_is_valid(
                'signup-mail', 
                'Please enter a valid mail address'
            )
        );
    }

    /*
    ** Checks if the password field is filled with a strong password (password field in user table)
    **
    ** @return bool: true if the field is filled and password is strong enough, false otherwhise
    */
    private function check_password()
    {
        return (
            $this->password_is_strong_enough(
                'signup-pwd',
                'Your password is too weak, it must contain lowercase, uppercase, ' . 
                'digits and symbols (!@#$%^&amp;)'
            )
        );
    }

    /*
    ** Checks if the password confirmation field is filled (username field in user table)
    **
    ** @return bool: true if the field is filled and matches the password field, false otherwhise
    */
    private function check_password_conf()
    {
        return (
            $this->fields_match(
                'signup-pwd',
                'signup-pwdcheck',
                'Passwords don\'t match'
            )
        );
    }
}

?>