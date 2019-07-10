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

        die('FIELD NAME HAS NOT BEEN SET');

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
                'search-input',
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
        if ($this->field_is_valid('search-input'))
        {
            if (strpos($_POST['search-input'], '#') !== false)
            {
                $this->set_state('search-input', $this->valid_message);
            }
            else if (strpos($_POST['search-input'], '@') !== false)
            {
                $this->set_state('search-input', $this->valid_message);
            }
            else
            {
                $this->set_state('search-input', 'Please enter a #hashtag or an @username');
            }
        }
    }
}

?>