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

$form->field_is_filled(
    $response, 
    'signup-surname', 
    'Please enter your surname'
);
$form->field_is_filled(
    $response, 
    'signup-forename', 
    'Please enter your forename'
);
$form->birthdate_is_valid(
    $response,
    'signup-birthdate',
    'You must be at least 18 to sign-up on this website'
);
$form->field_is_filled(
    $response, 
    'signup-gender', 
    'Please select your gender'
);
$form->field_is_filled(
    $response, 
    'signup-city', 
    'Please enter your city'
);
$response['signup-mail'] = (
    $user->exists($_POST['signup-mail'])
) ? 'That mail address is already bound to an account' : 'Valid';
$form->password_is_strong_enough(
    $response, 
    'signup-password', 
    'Your password is too weak, it should contain lower case, upper case, ' .
        ' numbers and symbols (!@#\$%\^&amp;)'
);
$form->passwords_match(
    $response,
    'signup-password',
    'signup-password-conf',
    'Passwords don\'t match'
);

echo json_encode(
    $response
);

?>
