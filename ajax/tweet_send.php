<?php

/*
** Ajax target for tweet form
** Assumes the tweet content is valid 
** Returns a JSON Object where attribute is
** an error message in case something went wrong, 'Valid' otherwise
*/ 

ini_set('display_errors', 1);

require_once '../controller/FormTweetController.class.php';
require_once '../model/TweetModel.class.php';

session_start();

$form = new FormTweetController();
$tweet = new TweetModel();

$form->is_valid();

/*
** ID of field in the form
*/
$content = 'tweet-content';


/*
** Verification of the tweet's length
** If the tweet's length is greater than 0
** The tweet is sent and returns true
** If empty, display an error message 
** and returns false
*/
if(strlen($form->field_is_valid($content)) > 0)
{
    $tweet->post($_SESSION['account-id'], $_POST['tweet-content']);
}
else
{
    $form->set_state($content, 'Your Tweet is empty');
}

echo json_encode(
    $form->status()
);
?>