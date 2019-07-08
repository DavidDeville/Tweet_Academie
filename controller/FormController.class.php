<?php

require_once 'Controller.class.php';

/*
** Form-validation base controller
*/
abstract class FormController extends Controller
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

    /*
    ** Checks if the server response contains error messages
    **
    ** @return bool: true is the form is valid, false if it contains errors
    */
    public function is_valid()
    {
        foreach ($this->status as $status)
        {
            if ($status !== $this->valid_message)
            {
                return (false);
            }
        }
        return (true);
    }

    /*
    ** Returns the status of the form
    **
    ** @return Array: the status of the form
    **      @see $this->status
    */
    public function status()
    {
        return (
            $this->status
        );
    }

    /*
    ** Checks if the given field is valid
    **
    ** @param string $field_name: the field to check
    **
    ** @return bool: true if the field is valid, false otherwise
    */
    public function field_is_valid(string $field_name)
    {
        return (
            $this->status[$field_name] === $this->valid_message
        );
    }

    /*
    ** Stores a state, should be called if given field depends of external
    ** information, ie a model
    **
    ** @param $field_name: the field to set status for
    ** @param string status: the status of the field
    */
    public function set_state(string $field_name, string $status)
    {
        $this->write_status($field_name, $status);
    }

    /*
    ** Checks if the given field is filled in $_POST
    **
    ** @param string $field_name: the field to check
    ** @param string $error_message: the error message to write in status
    **
    ** @return bool: true is the field was filled, false otherwise
    */
    protected function field_is_filled(string $field_name, string $error_message)
    {
        if (strlen($_POST[$field_name]) >= 1)
        {
            $this->write_status($field_name, $this->valid_message);
            return (true);
        }
        else
        {
            $this->write_status($field_name, $error_message);
            return (false);
        }
    }

    /*
    ** Checks if the given field is a valid birth-date, ie user meets the age requirements
    **
    ** @param string $field_name: the field to check
    ** @param string $error_message: the error message to write in status
    **
    ** @return bool: true is the date is valid, false otherwise
    */
    protected function date_is_valid(string $field_name, string $error_message)
    {
        $age = date_diff(
            new DateTime(),
            new DateTime($_POST[$field_name])
        )->y;

        if ($age >= 18)
        {
            $this->write_status($field_name, $this->valid_message);
            return (true);
        }
        else
        {
            $this->write_status($field_name, $error_message);
            return (false);
        }
    }

    /*
    ** Checks if the given field is a valid mail address
    **
    ** @param string $field_name: the field to check
    ** @param string $error_message: the error message to write in status
    **
    ** @return bool: true is the mail is valid, false otherwise
    */
    protected function mail_is_valid(string $field_name, string $error_message)
    {
        $valid_mail = preg_match(
            '/[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+.[a-zA-Z]{2,4}/',
            $_POST[$field_name]
        );
        if ($valid_mail)
        {
            $this->write_status($field_name, $this->valid_message);
            return (true);
        }
        else
        {
            $this->write_status($field_name, $error_message);
            return (false);
        }
    }

    /*
    ** Checks if the given field is filled with a strong enough password, ie contains:
    **      - lowercase,
    **      - uppercase,
    **      - digits,
    **      - symbols (!@#$%^&)
    **
    ** @param string $field_name: the field to check
    ** @param string $error_message: the error message to write in status
    **
    ** @return bool: true is the password is strong enough, false otherwise
    */
    protected function password_is_strong_enough(string $field_name, string $error_message)
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
                $strong_enough = false;
            }
        }
        if ($strong_enough)
        {
            $this->write_status($field_name, $this->valid_message);
            return (true);
        }
        else
        {
            $this->write_status($field_name, $error_message);
            return (false);
        }
    }

    /*
    ** Checks if 2 fields have the same value
    **
    ** @param string $first_field: the first field name to compare
    ** @param string $second_field: the second field name to compare with
    ** @param string $error_message: the error message to write in status
    **
    ** @return bool: true fields match, false otherwise
    */
    protected function fields_match(string $first_field, string $second_field, string $error_message)
    {
        if ($_POST[$first_field] === $_POST[$second_field])
        {
            $this->write_status($second_field, $this->valid_message);
            return (true);
        }
        else
        {
            $this->write_status($second_field, $error_message);
            return (false);
        }
    }

    /*
    ** Writes a message in the server response if not null
    **
    ** @param string $field_name: the name of the field to write status for
    ** @param string $status: the message to write in status for the selected field
    */
    private function write_status(string $field_name, string $status)
    {
        $this->status[$field_name] = $status;
    }
}

?>
