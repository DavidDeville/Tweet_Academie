<?php

ini_set('display_errors', 1);

require_once 'vendor/autoload.php';
require_once 'model/UserModel.class.php';
require_once 'controller/IndexController.class.php';

$loader = new Twig_Loader_Filesystem(__DIR__ . '/view');
$twig = new Twig_Environment($loader);

$user = new UserModel();
$controller = new IndexController();

echo $twig->render('index.htm.twig');

if ($controller->trololo())
{
    //echo '<h1>Hello world !</h1>';  
}

?>
