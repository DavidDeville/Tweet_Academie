<?php

ini_set('display_errors', 1);

require_once '../vendor/autoload.php';
require_once '../model/TweetModel.class.php';
require_once '../model/UserModel.class.php';

session_start();

$tweet = new TweetModel();
$user = new UserModel();
$twig = new Twig_Environment(
    new Twig_Loader_Filesystem(__DIR__ . '/../view')
  );

$latest_tweets = $tweet->for_user_by_time(
    $user->get_followings_id(),
    $_POST['timestamp']
);

foreach($latest_tweets as $tweet)
{
    /*echo $twig->render('tweet.htm.twig', [
        'username' => $_POST['account-name'],
        'content' => $tweet['tweet-content']
    ]);*/

    //var_dump($tweet);
}

var_dump($latest_tweets);

?>