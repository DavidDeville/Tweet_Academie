<?php

ini_set('display_errors', 1);

require_once '../model/MessageModel.class.php';

$newmessages = new MessageModel();

//var_dump($_POST);
var_dump(
  $newmessages->new_content_conv(
    (int) $_POST['id_conv'],
    (int) $_POST['id_msg']
  )
);

?>
