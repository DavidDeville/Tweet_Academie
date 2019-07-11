<?php

ini_set('display_errors', 1);

require_once '../vendor/autoload.php';
require_once '../model/MessageModel.class.php';

/*$newmessages = new MessageModel();

//var_dump($_POST);
/*var_dump(
  $newmessages->new_content_conv(
    (int) $_POST['id_conv'],
    (int) $_POST['id_msg']
  )
);*/

//var_dump($_POST);

$messenger = new MessageModel();
$twig = new Twig_Environment(
  new Twig_Loader_Filesystem(__DIR__ . '/../view')
);

$latest_messages = $messenger->fetch_messages(
  $_POST['conv-id'],
  $_POST['timestamp']
);
foreach ($latest_messages as $message)
{
  echo $twig->render('messageBubble.htm.twig', [
    'username' => $message['username'],
    'content' => $message['content'],
    'id' => $message['id'],
    'account_name' => $_POST['account_name']
  ]);
}

?>
