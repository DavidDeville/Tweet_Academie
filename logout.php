<?php

require_once 'model/UserModel.class.php';

session_start();

$user = new UserModel();

if ($user->is_connected())
{
    $user->logout();
    header('location: index.php');
}

?>