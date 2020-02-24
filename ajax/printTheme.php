<?php

session_start();

require_once '../model/UserModel.class.php';

$user = new UserModel();
echo $user->get_theme($user->get_account_id())['theme'];


?>
