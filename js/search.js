// TODO: split input field to query both hashtag and members

/*
** Ajax request to check search form validity before submiting it
*/
$('#search-submit').click((event) =>
{
    event.preventDefault();

    const form = 
    {
        content: $('#search-input').val();
    };

    $.post(
        'ajax/search.php',
        form,
        treatAjaxFormResponse,
        'json'
    ).then(() =>
    {
        if ($('#search-form').find('.invalid-feedback').length === 0)
        {
            $('#search-form').submit();
        }
    });
});