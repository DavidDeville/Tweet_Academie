const trololo = (response) =>
{
    console.log(response);
};

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
        'signup-username': $('#signup-username').val(),
        'signup-accname': $('#signup-accname').val(),
        'signup-dob': $('#signup-dob').val() ,
        'signup-city': $('#signup-city').val(),
        'signup-mail': $('#signup-mail').val(),
        'signup-pwd': $('#signup-pwd').val(),
        'signup-pwdcheck': $('#signup-pwdcheck').val()
    };

    $.post(
        'ajax/signup.php',
        form,
        treatAjaxFormResponse,
        'json'
    ).then(() =>
    {
        if ($('#signup').find('.invalid-feedback').length === 0)
        {
            $.post(
                'index.php',
                form,
                trololo,
                'text'
            ).then(() =>
            {
                $(location).attr('href', 'index.php');
            });
        }
    });
});
