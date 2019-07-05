<?php

require_once 'PageController.class.php';

/*
** Controller for the page profile.php
*/
final class ProfileController extends PageController
{
    // If no account name has been specified, redirect to homepage
    public function __construct()
    {
        parent::__construct();

        if (! isset($_GET['account']))
        {
            header('location: index.php');
        }
    }

    /*
    ** Checks if the user submited the info update form
    **
    ** @return bool: true if user submited the info update form, false otherwise
    */
    public function info_updated()
    {
        return (
            $this->form_matches(
                'info'
            )
        );
    }
}

?>