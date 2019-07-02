<?php

ini_set('display_errors', 1);

session_start();

require_once 'vendor/autoload.php';
require_once 'model/UserModel.class.php';
require_once 'controller/ProfileController.class.php';

$loader = new Twig_Loader_Filesystem(__DIR__ . '/view');
$twig = new Twig_Environment($loader);

$user = new UserModel();
$controller = new ProfileController();

echo $twig->render('profile.htm.twig', [
    'account_name' => $user->get_account_name(),
    'email' => $user->get_mail(),
    'name' => $user->get_pseudo(),
    'city' => $user->get_city(),
    'dob' => $user->get_birth_date()
]);

if ($controller->form_submited())
{
    if ($controller->info_updated())
    {
        $user->update();
    }
    else
    {
        // uploader l'image
    }
}

?>
