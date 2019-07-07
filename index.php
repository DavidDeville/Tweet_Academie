<?php

ini_set('display_errors', 1);

session_start();

require_once 'controller/IndexController.class.php';

$page = new IndexController();
$page->display_view();
$page->handle_request();

?>
