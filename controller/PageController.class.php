<?php

require_once 'Controller.class.php';

/*
** Base controller for pages
*/
abstract class PageController extends Controller
{
    /*
    ** Redirects user if he's not connected and trying to visit something else than homepage
    */
    public function __construct()
    {
        $url = $_SERVER['SCRIPT_NAME'];
        $slash_pos = strrpos($url, '/');
        $url = substr($url, $slash_pos + 1);

        if ((count($_SESSION) < 1) && ($url != 'index.php'))
        {
            header('location: index.php');
        }
    }

    /*
    ** Checks whether a form has been submited or not
    **
    ** @return bool: true if a form has been submited, false otherwise
    */
    public function form_submited()
    {
        return (
            count($_POST) > 0
        );
    }

    /*
    ** Checks if post-form keys contain the given pattern
    ** Please note that all keys of the form should have the same prefix
    **
    ** @param string $pattern: true is the pattern could be found in form 
    **      fields id, false otherwise
    **
    ** @return bool: true is the pattern could be found in $_POST keys
    */
    protected function form_matches(string $pattern)
    {
        if ($this->form_submited())
        {
            $key = array_keys($_POST)[0];
            return (
                strpos(
                    $key, 
                    $pattern
                ) !== false
            );
        }
        else
        {
            return (
                false
            );
        }
        
    }
}

?>