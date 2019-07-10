<?php

ini_set('display_errors', 1);

require_once 'FormController.class.php';

/*
** Controller for the serch form on the navbar
** Used to search member (using @) and hashtags (using #)
*/
final class FormSearchController extends FormController
{
    /*
    ** Checks if the whole form is valid
    **
    ** @return bool: true if the form is valid, false otherwise
    */
    public function is_valid()
    {
        $this->check_request();
        $this->check_format();

        return (
            parent::is_valid()
        );
    }

    /*
    ** Ensures thefield is filled
    **
    ** @return bool: returns true if the field is filled, false otherwise
    */ 
    private function check_request()
    {
        return (
            $this->field_is_field(
                'FIELD_NAME_HERE',
                'Please enter something to search'
            )
        );
    }

    /*
    ** Ensures the search-bar contains valid queries, ie #hashtag or @username
    **
    ** @return bool: true if field contains '#' or '@', false otherwise
    */ 
    private function check_format()
    {
        if ($this->field_is_valid('FIELD_NAME'))
        {
            if (strpos($_POST['FIELD_NAME'], '#') !== false)
            {
                $this->set_state('FIELD_NAME', $this->valid_message);
            }
            else if (strpos($_POST['FIELD_NAME'], '@') !== false)
            {
                $this->set_state('FIELD_NAME', $this->valid_message);
            }
            else
            {
                $this->set_state('FIELD_NAME', 'Please enter a #hashtag or an @username');
            }
        }
    }
}

?>