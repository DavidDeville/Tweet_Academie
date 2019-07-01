<?php

require_once 'Controller.class.php';

/*
** Form-validation controller
*/
class FormController extends Controller
{
    /*
    ** The message to display in AJAX response when fields are valid
    */
    private $valid_message;

    public function __construct()
    {
        $this->valid_message = 'valid';
    }

    /*
    ** Checks if the given field is filled in $_POST
    **
    ** @param Array &$response: the AJAX response
    ** @param string $field_name: the field to check
    ** @param string $error_message: the error message to put in $response
    **
    ** @return bool: true is the field was filled, false otherwise
    */
    public function check_field_is_filled(Array &$response, string $field_name, string $error_message)
    {
        if (isset($_POST[$field_name]) && strlen($_POST[$field_name]) > 1)
        {
            $response[$field_name] = $this->valid_message;
            return (true);
        }
        else
        {
            $response[$field_name] = $error_message;
            return (false);
        }
    }

    /*
    ** Checks if the given field is a valid birth-date, ie user meets the age requirements
    **
    ** @param Array &$response: the AJAX response
    ** @param string $field_name: the field to check
    ** @param string $error_message: the error message to put in $response
    **
    ** @return bool: true is the date is valid, false otherwise
    */
    public function check_birthdate_is_valid(Array &$reponse, string $field_name, string $error_message)
    {
        $age = date_diff(
            new DateTime(),
            new DateTime($_POST[$field_name])
        )->y;

        if ($age >= 18)
        {
            $response[$field_name] = $this->valid_message;
            return (true);
        }
        else
        {
            $response[$field_name] = $error_message;
            return (false);
        }
    }

    /*
    ** Checks if the given field is a valid mail address
    **
    ** @param Array &$response: the AJAX response
    ** @param string $field_name: the field to check
    ** @param string $error_message: the error message to put in $response
    **
    ** @return bool: true is the mail is valid, false otherwise
    */
    public function check_valid_mail(Array &$reponse, string $field_name, string $error_message)
    {
        $valid_mail = preg_match(
            '/[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+.[a-zA-Z]{2,4}/',
            $_POST[$field_name]
        );
        if ($valid_mail)
        {
            $response[$field_name] = $this->valid_message;
            return (true);
        }
        else
        {
            $response[$field_name] = $error_message;
            return (false);
        }
    }

    /*
    ** Checks if the given field is filled with a strong enough password, ie contains:
    **      - lowercase,
    **      - uppercase,
    **      - digits,
    **      - symbols ()
    **
    ** @param Array &$response: the AJAX response
    ** @param string $field_name: the field to check
    ** @param string $error_message: the error message to put in $response
    **
    ** @return bool: true is the password is strong enough, false otherwise
    */
    public function check_password_strength(Array &$response, string $field_name, string $error_message)
    {
        $regex_patterns = [
            '/[a-z]/',
            '/[A-Z]/',
            '/[0-9]/',
            '/[!@#\$%\^&]/'
        ];
        $strong_enough = true;
        foreach ($regex_patterns as $pattern)
        {
            if (! preg_match($pattern, $_POST[$field_name]))
            {
                $strong_enough = $false;
            }
        }
        if ($strong_enough)
        {
            $response[$field_name] = $this->valid_message;
            return (true);
        }
        else
        {
            $response[$field_name] = $error_message;
            return (false);
        }
    }

    /*
    ** Checks if the given field in filled in $_POST
    **
    ** @param Array &$response: the AJAX response
    ** @param string $field_name: the field to check
    ** @param string $error_message: the error message to put in $response
    **
    ** @return bool: true is the field was filled, false otherwise
    */
    public function check_passwords_match(Array &$response, string $pwd_field, string $pwd_conf_field, string $error_message)
    {
        if ($_POST[$pwd_field] === $_POST[$pwd_conf_field])
        {
            $response[$field_name] = $this->valid_message;
            return (true);
        }
        else
        {
            $response[$field_name] = $error_message;
            return (false);
        }
    }
}

?>