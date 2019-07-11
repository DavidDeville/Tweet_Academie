<?php

ini_set('display_errors', 1);

session_start();

require_once 'controller/SearchController.class.php';

$page = new SearchController();
$page->display_view();
$page->handle_request();

?>