<?php

ini_set('display_errors', 1);

require_once 'PageController.class.php';
require_once 'model/UserModel.class.php';
require_once 'model/TweetModel.class.php';

/*
** Controller for the search page
*/
final class SearchController extends PageController
{
    /*
    ** The user model
    */
    private $user;

    /*
    ** The tweet model
    */
    private $tweet;

    public function __construct()
    {
        parent::__construct();

        $this->user = new UserModel();
        $this->tweet = new TweetModel();

        $this->redirect_on_empty_query();
    }

    public function display_view()
    {
        $hashtags = [];
        $usernames = [];
        $this->init_terms($hashtags, $usernames);
        $this->redirect_on_empty_queries($hashtags, $usernames);

        //$this->myvar_dump($this->tweet->hashtags($hashtags)[0]);

        echo $this->twig->render('search.htm.twig', [
            'account_name' => $this->user->get_account_name(),
            'hashtags' => $this->tweet->hashtags($hashtags),
            'members' => $this->user->by_patterns($usernames)
        ]);
    }

    public function handle_request() {}

    /*
    ** Splits the search string in $_GET into a list of hashtags and members
    **
    ** @param Array &$hashtags: array to store hashtags
    ** @param Array &$usernames: array to store usernames to search
    */
    private function init_terms(Array &$hashtags, Array &$usernames)
    {
        $tokens = explode(' ', $_GET['search']);
        foreach ($tokens as $token)
        {
            if (strpos($token, '#') === 0)
            {
                array_push($hashtags, substr($token, 1));
            }
            else if (strpos($token, '@') === 0)
            {
                array_push($usernames, substr($token, 1));
            }
        }
    }

    /*
    ** Redirects the user if the search string is empty
    */
    private function redirect_on_empty_query()
    {
        if (count($_GET) < 1)
        {
            header('location: index.php');
        }
    }

    /*
    ** Redirects the user if the search format was invalid, ie
    ** search string didn't contain #hashtag or @member
    **
    ** @param Array $hashtags: the list of requested hashtags
    ** @param Array $usernames: the list of requested profiles
    */
    private function redirect_on_empty_queries(Array $hashtags, Array $usernames)
    {
        if ((count($hashtags) < 1) && (count($usernames) < 1))
        {
            header('location: index.php');
        }
    }




    // debug 
    private function myvar_dump(Array $arr)
    {
        echo '<pre>';
        var_dump($arr);
        echo '</pre>';
    }
}

?>