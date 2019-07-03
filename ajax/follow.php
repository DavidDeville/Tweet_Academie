<?php

/*
** Ajax target for follow button
*/

ini_set('display_errors', 1);

session_start();

require_once '../model/UserModel.class.php';

$user = new UserModel();
$user->follow($_POST['target']);

?>
