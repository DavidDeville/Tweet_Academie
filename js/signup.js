/*
** Sends an ajax request and determines if the form should be submited
**
** @see forms.js for callback
*/
$('#signup-submit').click(() =>
{
    event.preventDefault();
        
    const form = 
    {
        'signup-surname': $('#signup-surname').val(),
        'signup-forename': $('#signup-forename').val(),
        'signup-birthdate': $('#signup-birthdate').val(),
        'signup-gender': $('#signup-gender').val(),
        'signup-city': $('#signup-city').val(),
        'signup-mail': $('#signup-mail').val(),
        'signup-password': $('#signup-password').val(),
        'signup-password-conf': $('#signup-password-conf').val()
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
