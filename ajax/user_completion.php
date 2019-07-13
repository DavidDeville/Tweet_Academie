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

/*
** Able to search for a member with and without an @
*/
$keyword = $_POST['keyword'];

if($keyword[0] == "@")
{
    $keyword = substr($keyword, 1);
}

$result = $user->by_pattern($keyword);
echo json_encode($result);

?>