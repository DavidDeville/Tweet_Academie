<?php

ini_set('display_errors', 1);

require_once '../controller/FormMessengerController.class.php';

$form = new FormMessengerController();

$form->is_valid();

echo json_encode($form->status());

?>
