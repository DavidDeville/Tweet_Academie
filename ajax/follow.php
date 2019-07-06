<?php

/*
** Ajax target for follow button
*/

ini_set('display_errors', 1);

session_start();

require_once '../model/UserModel.class.php';

$response = [];
$user = new UserModel();

$response['state'] = (
    $user->follow($_POST['target'])
);
$response['target'] = $_POST['target'];

echo json_encode(
    $response
);

?>
