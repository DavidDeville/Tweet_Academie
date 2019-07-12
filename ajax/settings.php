<?php

session_start();

require_once '../model/UserModel.class.php';

$user = new UserModel();
$user->change_theme($user->get_account_id());
echo $user->get_theme($user->get_account_id())['theme'];

?>
