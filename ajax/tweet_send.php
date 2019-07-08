<?php

/*
** Ajax target for tweet form
** Assumes the tweet content is valid 
** Returns a JSON Object where attribute is
** an error message in case something went wrong, 'Valid' otherwise
*/ 

ini_set('display_errors', 1);

require_once '../controller/FormController.class.php';

$form = new FormController();

$form->is_valid();

echo json_encode(
    $form->status()
);

?>