// TODO: split input field to query both hashtag and members

const buildSearchUrl = (basePath) =>
{
    /*
    let hashtags = [];
    let members = [];

    let terms = $('#search-input').val().split(' ');
    terms.forEach((word) => 
    {
        if (word.indexOf('#') === 0)
        {
            hashtags.push(word.substr(1));
        }
        else if (word.indexOf('@') === 0)
        {
            members.push(word.substr(1));
        }
    });
    let argAdded = false;

    if (hashtags.length)
    {
        basePath += '?hashtags=' + hashtags.join(',');
        argAdded = true;
        if (members.length)
        {
            basePath += '&';
        }
    }
    if (members.length)
    {
        if (! argAdded)
        {
            basePath += '?';
            argAdded = true;
        }
        basePath += 'members=' + members.join(',');
    }

    return basePath;*/
};

/*
** Ajax request to check search form validity before submiting it
*/
$('#search-form').submit((event) =>
{
    /*event.preventDefault();

    console.log(buildSearchUrl('search.php'));
    return false;

    const form = 
    {
        content: $('#search-input').val()
    };

    //console.log(form);
    return false;

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
    });*/
});