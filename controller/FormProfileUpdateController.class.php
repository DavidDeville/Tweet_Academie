<?php

ini_set('display_errors', 1);

require_once 'FormController.class.php';

/*
** Controller for the profile-update form
*/
final class FormProfileUpdateController extends FormController
{
    /*
    ** Checks if the whole form is valid
    **
    ** @return bool: true if the form is valid, false otherwise
    */
    public function is_valid()
    {   
        $this->check_mail();
        $this->check_pseudo();
        $this->check_city();
        $this->check_birthdate();
        $this->check_password();
        $this->check_new_password();
        $this->check_new_password_conf();

        return (
            parent::is_valid()
        );
    }

    /*
    ** Checks if the mail field is filled with a valid address
    **
    ** @return bool: true if the field is filled and the mail is valid, false otherwise
    */
    private function check_mail()
    {
        return (
            $this->mail_is_valid(
                'info-email', 
                'Please enter a valid mail address'
            )
        );
    }

    /*
    ** Checks if the pseudo field is filled 
    **
    ** @return bool: true if the field is filled, false otherwise
    */
    private function check_pseudo()
    {
        return (
            $this->field_is_filled(
                'info-name',
                'Please enter your pseudo'
            )
        );
    }

    /*
    ** Checks if the city field is filled 
    **
    ** @return bool: true if the field is filled, false otherwise
    */
    private function check_city()
    {
        return (
            $this->field_is_filled(
                'info-city',
                'Please enter your city'
            )
        );
    }

    /*
    ** Checks if the birth-date field is filled
    **
    ** @return bool: true if the field is filled and the user meets the are requirements, false otherwise
    */
    private function check_birthdate()
    {
        return (
            $this->date_is_valid(
                'info-dob',
                'You must be at least 18 years old'
            )
        );
    }

    /*
    ** Checks if the password field is filled
    **
    ** @return bool: true if the field is filled, false otherwise
    */
    private function check_password()
    {
        return (
            $this->field_is_filled(
                'info-oldpwd',
                'Please enter your password'
            )
        );
    }

    /*
    ** Checks if the new password field is filled with a strong enough password
    **
    ** @return bool: true if the field is filled with a strong enough password, false otherwise
    */
    private function check_new_password()
    {
        if ($_POST['info-pwd'] === '')
        {
            return (true);
        }
        else
        {
            return (
                $this->password_is_strong_enough(
                    'info-pwd',
                    'Password is took weak, it should contain lower case, upper case, digits and symbols'
                )
            );
        }
    }

    /*
    ** Checks if the password confirmation field is filled and matches the new password field
    **
    ** @return bool: true if the field is filled and matches the new password field, false otherwise
    */
    private function check_new_password_conf()
    {
        return (
            $this->fields_match(
                'info-pwd',
                'info-checkpwd',
                'Passwords don\'t match'
            )
        );
    }
}

?>