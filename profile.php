<?php

ini_set('display_errors', 1);

session_start();

require_once 'controller/ProfileController.class.php';

$page = new ProfileController();
$page->display_view();
$page->handle_request();

?>