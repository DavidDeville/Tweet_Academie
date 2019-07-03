<?php

ini_set('display_errors', 1);

session_start();

require_once 'vendor/autoload.php';
require_once 'model/UserModel.class.php';
require_once 'controller/IndexController.class.php';

$loader = new Twig_Loader_Filesystem(__DIR__ . '/view');
$twig = new Twig_Environment($loader);

$user = new UserModel();
$controller = new IndexController();

if ($user->is_connected())
{
    echo $twig->render('mail_histories.htm.twig',
    ['account_name' => $user->get_account_name()]);
}
else
{
    echo $twig->render('index.htm.twig');

    if ($controller->form_submited())
    {
        if ($controller->signed_up())
        {
            $user->register();
        }
        $user->login();
    }
}



?>
