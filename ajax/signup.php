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
** Here, nothing will be shown about password confirmation unless password is valid
*/

ini_set('display_errors', 1);

require_once '../controller/FormSignUpController.class.php';
require_once '../model/UserModel.class.php';

$user = new UserModel();
$form = new FormSignUpController();

$form->is_valid();

/*
** ID of fields in the form
*/
$account = 'signup-accname';
$mail = 'signup-mail';

// Only check if account is available if a valid name has been entered
if ($form->field_is_valid($account))
{
    if ($user->account_exists($_POST[$account]))
    {
        $form->set_state($account, 'That account name is already in use');
    }
}

// Only check if mail is available if a valid address has been entered
if ($form->field_is_valid($mail))
{
    if ($user->mail_exists($_POST[$mail]))
    {
        $form->set_state($mail, 'That mail address is already bound to an account');
    }
}

echo json_encode(
    $form->status()
);

?>
