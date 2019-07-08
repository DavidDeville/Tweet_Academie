<?php

ini_set('display_errors', 1);

session_start();

require_once 'controller/MessengerController.class.php';

$page = new MessengerController();
$page->display_view();
$page->handle_request();

?>
