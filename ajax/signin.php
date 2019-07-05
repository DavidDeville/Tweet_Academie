<?php

/*
** Ajax target for signin form
** Assumes fields are filled with valid values
** Returns a JSON object where attributes names are
** form fields, and attributes values are error messages if any, 'Valid' otherwise
**
** Please note that any status in form will be treated in the calling
** javascript, so to not give informations about some fields under certain 
** conditions, don't add them
** Here, password info isn't display unless valid and bound mail is given
*/

require_once '../controller/FormSignInController.class.php';
require_once '../model/UserModel.class.php';

$user = new UserModel();
$form = new FormSignInController();

$form->is_valid();

/*
** ID of fields in the form
*/
$mail = 'signin-mail';
$password = 'signin-pwd';

// Only check if accountexists if a valid address has been entered
if ($form->field_is_valid($mail))
{
    // Only check password validity is valid account has been entered
    if ($user->mail_exists($_POST[$mail]))
    {
        if (! $user->password_match($_POST[$mail], $_POST[$password]))
        {
            $form->set_state($password, 'Wrong password');
        }
    }
    else
    {
        $form->set_state($mail, 'That mail address isn\'t bound to an account');
    }
}

echo json_encode(
    $form->status()
);

?>
