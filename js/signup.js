/*
** Sends an ajax request and determines if the form should be submited
**
** @see forms.js for callback
*/
$('#signup-submit').click((event) =>
{
    event.preventDefault();
        
    const form = 
    {
        'signup-forename': $('#signup-forename').val(),
        'signup-surname': $('#signup-surname').val(),
        'signup-dob': $('#signup-dob').val(),
        'signup-city': $('#signup-city').val(),
        'signup-mail': $('#signup-mail').val(),
        'signup-password': $('#signup-pwd').val(),
        'signup-password-conf': $('#signup-pwdcheck').val()
    };

    $.post(
        'ajax/signup.php',
        form,
        treatResponse,
        'json'
    )
    .then(() =>
    {
        if ($('.invalid-feedback').length === 0)
        {
            $.post(
                'index.php',
                form
            ).then(() =>
            {
                $(location).attr('href', 'index.php');
            });
        }
    });
});
