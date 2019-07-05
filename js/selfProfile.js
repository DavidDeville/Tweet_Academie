/*
** Ajax request to upload profile forms
*/
$('#submitinfo').click((event) =>
{
    event.preventDefault();

    const form =
    {
        'info-email': $('#info-email').val(),
        'info-name': $('#info-name').val(),
        'info-city': $('#info-city').val(),
        'info-dob': $('#info-dob').val(),
        'info-oldpwd': $('#info-oldpwd').val(),
        'info-pwd': $('#info-pwd').val(),
        'info-checkpwd': $('#info-checkpwd').val()
    };

    $.post(
        'ajax/profile_update.php',
        form,
        treatAjaxFormResponse,
        'json'
    ).then(() =>
    {
        if ($('#modifyinfo').find('.invalid-feedback').length === 0)
        {
            $.post(
                'profile.php',
                form
            ).then(() =>
            {
                location.reload();
                //console.log('formulaire envoyé');
            });
        }
    });
});