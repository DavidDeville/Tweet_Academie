<?php

ini_set('display_errors', 1);

require_once 'model/UserModel.class.php';
require_once 'view/index.htm';
require_once 'controller/IndexController.class.php';

$user = new UserModel();
$controller = new IndexController();

if ($controller->trololo())
{
    echo '<h1>Hello world !</h1>';  
}

?>