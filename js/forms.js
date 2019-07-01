/*
** Callback for ajax-submited forms
** Displays error messages under form fields if needed
**
** @see ajax target php scripts for response format
**
** @var response: the json response from the server
*/
const treatAjaxFormResponse = (response) =>
{
    console.log(response);
    
    return false;
    
    for (let attribute in response)
    {
        let $input = $('#' + attribute);
        if (response[attribute] !== 'Valid')
        {
            if (! $input.hasClass('is-invalid'))
            {
                $(
                    '<div>' + response[attribute] + '</div>'
                ).addClass(
                    'invalid-feedback'
                ).insertAfter(
                    $input.addClass('is-invalid')
                );
            }
        }
        else
        {
            if ($input.hasClass('is-invalid'))
            {
                $('#' + attribute + ' + .invalid-feedback').remove();
                $input.removeClass('is-invalid');
            }
        }
    }
};
