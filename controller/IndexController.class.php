<?php

require_once 'FormSignUpController.class.php';
require_once 'FormSignInController.class.php';
require_once 'FormTweetController.class.php';
require_once 'PageController.class.php';

require_once 'model/TweetModel.class.php';
require_once 'model/UserModel.class.php';
require_once 'model/UploadModel.class.php';

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
    ** The tweet controller
    */
    private $tweetForm;

    /*
    ** The upload model
    */
    private $upload;

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
        $this->upload = new UploadModel();
        $this->tweetForm = new FormTweetController();
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
        else
        {
            if ($this->tweet_liked())
            {
                $this->like_tweet();
            }
            else if ($this->tweet_reposted())
            {
                $this->repost_tweet();
            }
            else if ($this->tweet_submited() && $this->tweetForm->is_valid())
            {
                if ($this->tweet_is_reply())
                {
                    if ($this->tweetForm->contains_upload())
                    {
                        $this->upload_image();
                    }
                    $this->create_new_reply();    
                }
                else
                {
                    if ($this->tweetForm->contains_upload())
                    {
                        $this->upload_image();
                    }
                    $this->create_new_tweet();
                }
            }
        }
    }

    /*
    ** Checks whether or not a tweet has been sent
    **
    ** @return bool: true if a tweet has been uploaded, false otherwise
    */
    private function tweet_submited()
    {
        return (
            $this->form_matches('tweet')
        );
    }

    /*
    ** Moves the uploaded image to /uploads/tweets/
    ** It has to be posted in 'tweet-upload-content'
    */
    private function upload_image()
    {
        $image_data = explode(',', $_POST['tweet-upload-content']);
        $image_format = $image_data[0];
        $image_extension = substr($image_format, strpos($image_format, '/') + 1);
        $image_extension = substr($image_extension, 0, strpos($image_extension, ';'));
        $image_content = $image_data[1];
        $image_path = 'uploads/tweets/' . $this->user->get_account_name() . '.' . $image_extension;
        
        $current_profile = $this->upload->user_profile($this->user->get_account_id());
        if ($current_profile !== false)
        {
            unlink($current_profile);
        }
        $image_stream = fopen(
            $image_path,
            'wb'
        );
        fwrite($image_stream, base64_decode($image_content));
        fclose($image_stream);

        $this->upload->new(
            $this->user->get_account_id(),
            $image_path,
            UploadModel::TWEET
        );
    }

    /*
    ** Checks if the tweet sent is a reply to another tweet,
    ** if it is, $_POST['tweet-target'] must be the id of the targeted tweet
    */
    private function tweet_is_reply() 
    {
        return (
            $_POST['tweet-target'] !== 'false'
        );
    }

    /*
    ** Adds a tweet to the database
    */
    private function create_new_tweet() 
    {
        $this->tweet->post(
            $this->user->get_account_id(), 
            $_POST['tweet-content']
        );
    }

    /*
    ** Reply to a tweet and store it in database
    */
    private function create_new_reply() 
    {
        $this->tweet->reply(
            $this->user->get_account_id(),
            $_POST['tweet-content'],
            $_POST['tweet-target']
        );
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

    /*
    ** Checks if a tweet has been liked
    ** Based on $_POST['tweet-action']
    **
    ** @return bool: true a tweet has been liked, false otherwise
    */
    private function tweet_liked()
    {
        return (
            isset($_POST['tweet-action']) &&
            $_POST['tweet-action'] === 'tweet-like'
        );
    }

    /*
    ** Makes the connected user like the tweet
    */
    private function like_tweet() 
    {
        $author = $this->user->get_infos($_POST['tweet-author'])['id'];
        $tweet = $this->tweet->find(
            $_POST['tweet-content'], 
            $_POST['tweet-submited'], 
            $author
        );
        $this->tweet->like(
            $this->user->get_account_id(),
            $tweet
        );
    }

    /*
    ** Checks if a tweet has been reposted
    ** Based on $_POST['tweet-action']
    **
    ** @return bool: true a tweet has been reposted, false otherwise
    */
    private function tweet_reposted() 
    {
        return (
            isset($_POST['tweet-action']) &&
            $_POST['tweet-action'] === 'tweet-repost'
        );
    }

    /*
    ** Reposts the tweet
    ** Posts a copy of the tweet in database with connected user as sender
    **
    */
    private function repost_tweet() 
    {
        $author = $this->user->get_infos($_POST['tweet-author'])['id'];
        $tweet = $this->tweet->find(
            $_POST['tweet-content'], 
            $_POST['tweet-submited'], 
            $author
        );
        $this->tweet->repost(
            $this->user->get_account_id(),
            $tweet
        );
    }
}

?>