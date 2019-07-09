<?php

ini_set('display_errors', 1);

require_once '../model/UploadModel.class.php';
require_once '../controller/FormProfileUploadController.class.php';

$upload = new UploadModel();
$form = new FormProfileUploadController();

/*

$test = fopen('dick pic.png', 'wb');

// $data = 'data:image/png;base64,[....content...]'
$data = explode(',', $_POST['upload-content']);

var_dump($data[0]);

$extension = substr($data[0], strpos($data[0], '/') + 1); 
$extension = substr($extension, 0, strpos($extension, ';'));
var_dump($extension);

fwrite($test, base64_decode($data[1]));
//var_dump($data[1]);
fclose($test);
*/

//var_dump($_POST['upload-name']);

$form->is_valid();

echo json_encode(
    $form->status()
);

?>