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

require_once '../controller/FormController.class.php';
require_once '../model/UserModel.class.php';

$response = [];
$user = new UserModel();
$form = new FormController();

$form->checkFieldIsFilled(
    $response,
    'signup-forename',
    'Please enter your forename'
);

$form->checkFieldIsFilled(
    $response,
    'signup-surname',
    'Please enter your forename'
);

$form->checkBirthdateIsValid(
    $response,
    'signup-dob',
    'You must be at least 18 years old to sign-up'
);

$form->checkFieldIsFilled(
    $response,
    'signup-city',
    'Please enter your forename'
);

if ($form->checkValidMail(
    $response,
    'signup-mail',
    'Please enter a valid mail address'
))
{
    if ($user->exists($_POST['signup-mail']))
    {
        $response['signup-mail'] = 'That mail address is already in use';
    }
    else
    {
        $response['signup-mail'] = 'valid';
    }
}

$form->checkPasswordStrength(
    $response,
    'signup-password',
    'Your password is too weak'
);

$form->checkPasswordsMatch(
    $response,
    'signup-password',
    'signup-password-conf',
    'Passwords don\'t match'
);

echo json_encode(
    $response
);

?>
