/*
** Sends an ajax request and determines if the form should be submited
**
** @see forms.js for callback
*/
$('#tweet-send').click((event) =>
{
    event.preventDefault();

    const form =
    {
        'tweet-content': $('#tweet-content').val()

    };

    $.post(
        'ajax/tweet_send.php',
        form,
        treatAjaxFormResponse,
        'json'
    ).then(() =>
    {
        if ($('#tweet-send').find('.invalid-feedback').length === 0)
        {
            $.post(
                'index.php',
                form,
                'text'
            ).then(() =>
            {
                //Add the tweet using the model found in feed view

                $('')

            });
        }
    });
});