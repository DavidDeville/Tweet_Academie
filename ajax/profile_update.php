<?php

/*
** Ajax target for update form on user profile
** Assumes fields are filled with valid values
** Returns a JSON object where attributes names are
** form fields, and attributes values are error messages if any, 'valid' otherwise
**
** Please note that any status in form will be treated in the calling
** javascript, so to not give informations about some fields under certain 
** conditions, don't add them
*/

ini_set('display_errors', 1);

session_start();

require_once '../model/UserModel.class.php';
require_once '../controller/FormProfileUpdateController.class.php';

$user = new UserModel();
$form = new FormProfileUpdateController();

$form->is_valid();

/*
** ID of fields in the form
*/
$mail = 'info-email';
$password = 'info-oldpwd';

// If new mail has been enteredn check if it's available
if ($user->get_mail() !== $_POST[$mail])
{
    if ($user->mail_exists($_POST[$mail]))
    {
        $form->set_state($mail, 'That mail address is already bound to an account');
    }
}
// Check password math only if it has been entered
if ($form->field_is_valid($password))
{
    if (! $user->password_match($user->get_mail(), $_POST[$password]))
    {
        $form->set_state($password, 'Wrong Password');
    }
}

echo json_encode(
    $form->status()
);

?>