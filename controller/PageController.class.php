<?php

require_once 'vendor/autoload.php';
require_once 'Controller.class.php';
require_once 'FormSearchController.class.php';

/*
** Base controller for pages
** Decides which view to display and which actions to do
*/
abstract class PageController extends Controller
{    
    /*
    ** The list of pages non-connected users can see
    */
    private $public_pages;

    /*
    ** The view renderer
    */
    protected $twig;

    /*
    ** Displays the appropriate view, depending on models and sub-controllers
    ** Every sub-class has to define this
    */ 
    abstract public function display_view();

    /*
    ** Core of the page logic, every sub-class has to define this
    ** Decides of which actions should be done
    */
    abstract public function handle_request();

    public function __construct()
    {
        $this->twig = new Twig_Environment(
            new Twig_Loader_Filesystem(__DIR__ . '/../view')
        );
        $this->add_custom_filters();

        $this->public_pages = ['index.php'];
        $this->redirect();
    }
    
    /*
    ** Redirects the user if he's not connected and trying to visit something else than homepage
    */
    private function redirect()
    {
        $url = $_SERVER['SCRIPT_NAME'];
        $slash_pos = strrpos($url, '/');
        $url = substr($url, $slash_pos + 1);

        if ((count($_SESSION) < 1) && (! in_array($url, $this->public_pages)))
        {
            header('location: index.php');
        }
    } 

    /*
    ** Checks whether a form has been submited or not
    **
    ** @return bool: true if a form has been submited, false otherwise
    */
    protected function form_submited()
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
            $match = (strpos($key, $pattern) === 0);
            return ($match);
        }
        else
        {
            return (false);
        }   
    }

    /*
    ** Applies custom filter to twig environment
    */
    protected function add_custom_filters()
    {
        $this->twig->addFilter(new Twig_SimpleFilter('link_hashtags', function ($input) 
        {
            return (
                preg_replace(
                    '/#(\w+)/', 
                    "<a href='search.php?search=%23$1'>#$1</a>", 
                    $input
                )
            );
        }));
    }
}

?>