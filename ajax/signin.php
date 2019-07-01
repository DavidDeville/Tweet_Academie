<?php

/*
** Ajax target for signin form
** Assumes fields are filled with valid values
** Returns a JSON object where attributes names are
** form fields, and attributes values are error messages if any, 'Valid' otherwise
**
** Please note that any key added in $answer will be treated in the calling
** javascript, so to not give informations about some fields under certain 
** conditions, don't add them in $answer
*/

require_once '../controller/FormController.class.php';
require_once '../model/UserModel.class.php';

$answer = [];
$user = new UserModel();

if ($user->mail_exists($_POST['signin-mail']))
{
    $answer['signin-mail'] = 'valid';
    if ($user->password_match($_POST['signin-mail'], $_POST['signin-pwd']))
    {
        $answer['signin-pwd'] = 'valid';
    }
    else
    {
        $answer['signin-pwd'] = 'Wrong password';
    }
}
else
{
    $answer['signin-mail'] = 'That mail address isn\'t bound to an account';
}

echo json_encode(
    $answer
);

?>
