<?php

ini_set('display_errors', 1);

session_start();

require_once 'vendor/autoload.php';
require_once 'model/UserModel.class.php';
require_once 'controller/IndexController.class.php';
require_once 'controller/FormSignUpController.class.php';
require_once 'controller/FormSignInController.class.php';

$loader = new Twig_Loader_Filesystem(__DIR__ . '/view');
$twig = new Twig_Environment($loader);

$user = new UserModel();
$controller = new IndexController();
$sign_up = new FormSignUpController();
$sign_in = new FormSignInController();

if ($user->is_connected())
{
    echo $twig->render('memberbase.htm.twig', [
        'account_name' => $user->get_account_name()
    ]);
}
else    
{
    echo $twig->render('index.htm.twig');

    if ($controller->signed_up() && $sign_up->is_valid())
    {
        if (
            $user->mail_is_available($_POST['signup-mail']) && 
            $user->account_is_available($_POST['signup-accname'])
        )
        {
            $user->register();
            $user->login();    
        }
    }
    else if ($controller->signed_in() && $sign_in->is_valid())
    {
        if (
            $user->mail_exists($_POST['signin-mail']) && 
            $user->password_match($_POST['signin-mail'], $_POST['signin-pwd'])
        )
        {
            $user->login();
        }
    }
}

?>
