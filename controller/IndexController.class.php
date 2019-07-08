<?php

require_once 'PageController.class.php';
require_once 'model/UserModel.class.php';
require_once 'FormSignUpController.class.php';
require_once 'FormSignInController.class.php';
require_once 'model/TweetModel.class.php';

/*
** Controller for index.php
*/
final class IndexController extends PageController
{
    /*
    ** The user model
    */
    private $user;

    /*
    ** The tweet model
    */
    private $tweet;

    /*
    ** The sign-up form controller
    */
    private $signup;

    /*
    ** The signin form controller
    */
    private $signin;

    public function __construct()
    {
        parent::__construct();

        $this->user = new UserModel();
        $this->signup = new FormSignUpController();
        $this->signin = new FormSignInController();
        $this->tweet = new TweetModel();
    }

    /*
    ** Displays the appropriate view for the current context
    **      @see PageController::display_view()
    */
    public function display_view()
    {
        if ($this->user->is_connected())
        {
            $this->display_feed();
        }
        else
        {
            $this->display_homepage();
        }
    }

    /*
    ** Decides which action should be done
    **      @see PageController::handle_request()
    */
    public function handle_request()
    {
        if (! $this->user->is_connected())
        {
            if ($this->signed_in() && $this->signin->is_valid())
            {
                if ($this->valid_login_credentials())
                {
                    $this->user->login();
                }
            }
            else if ($this->signed_up() && $this->signup->is_valid())
            {
                if ($this->valid_signup_credentials())
                {
                    $this->user->register();
                    $this->user->login();
                }
            }
        }
    }

    /*
    ** Checks whether or not login credentials are valid, ie account exists
    ** and password is correct
    **
    ** @return bool: true if informations were correct, false otherwise
    */
    private function valid_login_credentials()
    {
        return (
            $this->user->mail_exists($_POST['signin-mail']) && 
            $this->user->password_match($_POST['signin-mail'], $_POST['signin-pwd'])
        );
    }

    /*
    ** Checks whether or not signup credentials are valid, ie account and mail
    ** don't already exist
    **
    ** @return bool: true if informations were correct, false otherwise
    */
    private function valid_signup_credentials()
    {
        return (
            $this->user->mail_is_available($_POST['signup-mail']) && 
            $this->user->account_is_available($_POST['signup-accname'])
        );
    }

    /*
    ** Displays the feed page where tweets are visible for connected users
    */
    private function display_feed()
    {
        echo $this->twig->render(
            'feed.htm.twig', [
                'account_name' => $this->user->get_account_name(),
                'tweets' => $this->tweet->for_user(
                    array_merge(
                        $this->user->get_followings_id(),
                        [['user_id' => $this->user->get_account_id()]]
                    )
                )
        ]);
    }

    /*
    ** Displas the homepage for non-connected users, containing signin/signup forms
    */
    private function display_homepage()
    {
        echo $this->twig->render(
            'index.htm.twig'
        );
    }

    /*
    ** Checks if the user submited the sign-up form
    **
    ** @return bool: true if user submited sign-up form, false otherwise
    */
    private function signed_up()
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
    private function signed_in()
    {
        return (
            $this->form_matches(
                'signin'
            )
        );
    }
}

?>