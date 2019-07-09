<?php

ini_set('display_errors', 1);

require_once '../model/UserModel.class.php';
require_once '../model/TweetModel.class.php';

$tweet = new TweetModel();
$user = new UserModel();

$sender_id = $user->get_infos($_POST['author'])['id'];

$tweet_id = $tweet->find(
    $_POST['content'],
    $_POST['date'],
    $sender_id
);
$tweet->like($sender_id, $tweet_id);

?>