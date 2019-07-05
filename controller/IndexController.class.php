<?php

require_once 'PageController.class.php';

/*
** Controller for the index
*/
final class IndexController extends PageController
{
    /*
    ** Checks if the user submited the sign-up form
    **
    ** @return bool: true if user submited sign-up form, false otherwise
    */
    public function signed_up()
    {
        return (
            $this->form_matches(
                'signup'
            )
        );
    }

    /*
    ** Checks if the user submited the sign-in form
    **
    ** @return bool: true if user submited sign-in form, false otherwise
    */
    public function signed_in()
    {
        return (
            $this->form_matches(
                'signin'
            )
        );
    }
}

?>