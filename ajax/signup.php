<?php

/*
** Ajax target for signin form
** Assumes fields are filled with valid values
** Returns a JSON object where attributes names are
** form fields, and attributes values are error messages if any, 'Valid' otherwise
**
** Please note that any key added in $answer will be treated in the calling
** javascript, so to not give informations about some fields under certain 
** conditions, don't add them in $response
*/

//ini_set('display_errors', 1);

require_once '../controller/FormController.class.php';
require_once '../model/UserModel.class.php';

$response = [];
$user = new UserModel();
$form = new FormController();

$form->check_field_is_filled(
    $response,
    'signup-username',
    'Please enter your username'
);

if($form->check_field_is_filled(
    $response,
    'signup-accname',
    'Please enter your account name'
))
{
    if ($user->account_exists('signup-accname'))
    {
        $response['signup-accname'] = 'This account name is already used';
    }
    else
    {
        $response['signup-accname'] = 'valid';
    }
}

$form->check_birthdate_is_valid(
    $response,
    'signup-dob',
    'You must be at least 18 years old to sign-up'
);

$form->check_field_is_filled(
    $response,
    'signup-city',
    'Please enter your city'
);

if ($form->check_valid_mail(
    $response,
    'signup-mail',
    'Please enter a valid mail address'
))
{
    if ($user->mail_exists($_POST['signup-mail']))
    {
        $response['signup-mail'] = 'That mail address is already in use';
    }
    else
    {
        $response['signup-mail'] = 'valid';
    }
}

if($form->check_password_strength(
    $response,
    'signup-pwd',
    'Your password is too weak, it must contain lowercase, uppercase, ' . 
    'digits and symbols (!@#$%^&amp;)'
))
{
    $form->check_passwords_match(
        $response,
        'signup-pwd',
        'signup-pwdcheck',
        'Passwords don\'t match'
    );
}

echo json_encode(
    $response
);

?>
