<?php

require_once 'PageController.class.php';

/*
** Controller for the page profile.php
*/
class ProfileController extends PageController
{
    /*
    ** Checks if the user submited the info update form
    **
    ** @return bool: true if user submited the info update form, false otherwise
    */
    public function info_updated()
    {
        if ($this->form_submited())
        {
            $key = array_keys($_POST)[0];
            if (strpos($key, 'info') !== false)
            {
                return (true);
            }
            else
            {
                return (false);
            }
        }
        else
        {
            return (false);
        }
    }
}

?>