<?php

require_once '../model/MessageModel.class.php';

$newmessages = new MessageModel();

$form = new FormMessengerController();

$form->is_valid();

var_dump($newmessages->new_content_conv($_GET['id'], $_POST['id_msg']));

?>
