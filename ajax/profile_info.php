<?php

/*
** Ajax target for update form on user profile
** Assumes fields are filled with valid values
** Returns a JSON object where attributes names are
** form fields, and attributes values are error messages if any, 'valid' otherwise
**
** Please note that any key added in $response will be treated in the calling
** javascript, so to not give informations about some fields under certain 
** conditions, don't add them in $response
*/

ini_set('display_errors', 1);

session_start();

require_once '../model/UserModel.class.php';
require_once '../controller/FormController.class.php';

$response = [];
$form = new FormController();
$user = new UserModel();

$form->check_valid_mail(
    $response,
    'info-email',
    'Please enter a valid mail address'
);

$form->check_field_is_filled(
    $response,
    'info-name',
    'Please enter your pseudo'
);

$form->check_field_is_filled(
    $response,
    'info-city',
    'Please enter your city'
);

$form->check_birthdate_is_valid(
    $response,
    'info-dob',
    'You must at least 18 years old'
);

if ($user->password_match($user->get_mail(), $_POST['info-oldpwd']))
{
    $response['info-oldpwd'] = 'valid';
}
else
{
    $response['info-oldpwd'] = 'Wrong password';
}

if ($_POST['info-pwd'] !== '')
{
    if ($form->check_password_strength(
        $response,
        'info-pwd',
        'Password is took weak, it should contain lower case, upper case, digits and symbols'
    ))
    {
        $form->check_passwords_match(
            $response,
            'info-pwd',
            'info-checkpwd',
            'Passwords don\'t match'
        );
    }
}

echo json_encode(
    $response
);

?>