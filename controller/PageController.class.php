<?php

require_once 'Controller.class.php';

/*
** Base controller for pages
*/
abstract class PageController extends Controller
{
    public function __construct()
    {
        // Redirection si pas connecté
    }

    public function form_submited()
    {
        return (
            count($_POST) > 0
        );
    }
}

?>