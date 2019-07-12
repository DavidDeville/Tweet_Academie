<?php

/*
** AJAX target for talk button on profile
*/

ini_set('display_errors', 1);

session_start();

require_once '../model/UserModel.class.php';
require_once '../model/MessageModel.class.php';

$user = new UserModel();
$messenger = new MessageModel();

$target = $user->get_infos($_POST['target']);

/*
** Members of the conversation
*/
$members = [
    $user->get_account_id(),
    $target['id']
];

/*
** The conversation to send messages in
*/
$conv = $messenger->find_conversation($members);

if (null === $conv)
{
    $conv = $messenger->create_conversation(
        $target['username'],
        $members
    );
}

echo $conv;

?>