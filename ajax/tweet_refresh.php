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
$twig->addFilter(new Twig_SimpleFilter('link_hashtags', function ($input) 
{
    return (
        preg_replace(
            '/#(\w+)/', 
            "<a href='search.php?search=%23$1'>#$1</a>", 
            $input
        )
    );
}));

$followings_id = $user->get_followings_id();
array_push(
    $followings_id,
    ['user_id' => $user->get_account_id()]
);

$latest_tweets = $tweet->for_user_by_time(
    $followings_id,
    $_POST['timestamp']
);

foreach($latest_tweets as $tweet)
{
    echo $twig->render('tweet.htm.twig', [
        'author' => $tweet['author'],
        'submit_time' => $tweet['submit_time'],
        'author_account' => $tweet['author_account'],
        'content' => $tweet['content']
    ]);
}

?>