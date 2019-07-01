/*
** Sends an ajax request and determines if the form should be submited
**
** @see forms.js for callback
*/
$('#signin-submit').click((event) =>
{
    event.preventDefault();
    
    const form = 
    {
        'signin-mail': $('#signin-mail').val(),
        'signin-pwd': $('#signin-pwd').val()
    };

    $.post(
        'ajax/signin.php',
        form,
        treatAjaxFormResponse,
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
