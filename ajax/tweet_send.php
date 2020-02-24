<?php

/*
** Ajax target for tweet form
** Assumes the tweet content is valid 
** Returns a JSON Object where attribute is
** an error message in case something went wrong, 'Valid' otherwise
*/ 

ini_set('display_errors', 1);

session_start();

require_once '../controller/FormTweetController.class.php';

$form = new FormTweetController();

$form->is_valid();

echo json_encode(
    $form->status()
);

?>