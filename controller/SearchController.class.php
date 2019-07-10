<?php

ini_set('display_errors', 1);

require_once 'PageController.class.php';
require_once '../model/UserModel.class.php';
require_once '../model/TweetModel.class.php';

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
    }

    public function display_view()
    {
        echo $this->twig->render('search.htm.twig'/*, [
            'hashtags' => $tweet->hashtag('something'),
            'members' => $user->by_pattern('something')
        ]*/);
    }

    public function handle_request() {}
}

?>