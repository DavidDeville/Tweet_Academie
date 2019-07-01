<?php

require_once 'PageController.class.php';

/*
** Controller for the index
*/
class IndexController extends PageController
{
    /*
    ** Checks if the user submited the sign-up form
    **
    ** @return bool: true if user submited sign-up form, false otherwise
    */
    public function signed_up()
    {
        if ($this->form_submited())
        {
            $key = array_keys($_POST)[0];
            if (strpos($key, 'signup') !== false)
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