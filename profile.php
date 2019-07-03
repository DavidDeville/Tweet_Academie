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

if ($_GET['account'] === $user->get_account_name())
{
    echo $twig->render('self_profile.htm.twig', [
        'followers' => $user->get_followers(),
        'followings' => $user->get_followings(),
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
}

else
{
    $target = new UserModel();
    $infos = $target->get_infos($_GET['account']);
    echo $twig->render('profile.htm.twig', [
        'account_name' => $user->get_account_name(),
        'target_name' => $infos['display_name'],
        'email' => $infos['email'],
        'name' => $infos['display_name'],
        'city' => $infos['city'],
        'dob' => $infos['birth_date']
    ]);
}



?>
