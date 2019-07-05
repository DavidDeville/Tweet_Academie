<?php

ini_set('display_errors', 1);

session_start();

require_once 'vendor/autoload.php';
require_once 'model/UserModel.class.php';
require_once 'controller/ProfileController.class.php';
require_once 'controller/FormProfileUpdateController.class.php';
//require_once 'controller/FormProfileUploadController.class.php';

$loader = new Twig_Loader_Filesystem(__DIR__ . '/view');
$twig = new Twig_Environment($loader);

$user = new UserModel();
$controller = new ProfileController();
$update = new FormProfileUpdateController();
//$upload = new FormProfileUploadController();

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

if ($controller->info_updated() && $update->is_valid())
{
    // Password must be correct to update informations
    if ($user->password_match($_POST['info-email'], $_POST['info-oldpwd']))
    {
        // If no new mail has been entered
        if ($user->get_mail() === $_POST['info-email'])
        {
            $user->update();
        }
        // If new mail is available
        else if ($user->mail_is_available($_POST['info-email']))
        {
            $user->update();
        }
    }
}
/*if ($controller->picture_uploaded() && $upload->is_valid())
{

}*/

?>