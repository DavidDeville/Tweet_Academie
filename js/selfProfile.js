/*
** The image reader, required for both image selection and upload
*/
let reader = new FileReader();
  
/*
** Reader has to be set on image selection, otherwise it's undefined
*/ 
$('#upload-file').change(() =>
{
    reader.readAsDataURL($('#upload-file')[0].files[0]);
});

/*
** Ajax request to upload the profile picture
*/
$('#upload-submit').click((event) =>
{
    event.preventDefault();

    let filename = $('#upload-file').val();
    filename = filename.substr(filename.lastIndexOf('\\') + 1);

    const form = 
    {
        'upload-file': reader.result,
        'upload-name': filename
    };

    $.post(
        'ajax/profile_upload.php',
        form,
        treatAjaxFormResponse,
        'json'
    ).then(() =>
    {
        $.post(
            currentUrl(),
            form
        );
    });
});

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
                currentUrl(),
                form
            ).then(() =>
            {
                location.reload();
            });
        }
    });
});

//$('<div>ERROR</div>').addClass('invalid-feedback').insertAfter($('#upload-file'));