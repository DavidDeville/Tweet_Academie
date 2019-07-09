<?php

require_once '../model/MessageModel.class.php';

$newmessages = new MessageModel();
var_dump($newmessages->new_content_conv($_GET['id'], $_POST['time']));

?>
