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

//AJAX Call for Autocomplete (WIP)

$('#search-input').keyup(function()
{
    $.ajax({
        type: 'POST',
        url: 'ajax/user_completion.php',
        data:'keyword='+$(this).val(),
        dataType: 'json',
    success: function(data)
    {  
        //$('#suggestion-box').show();
        //$('#suggestion-box').append('<div>').addClass("members-list");
        for(var property in data)
        {
            var item = data[property];
            //console.log(item);
            //$('.members-list').append("<a></a>").addClass('link').attr("href","profil.php?account=").text(item.account_name);
            //$('#suggestion-box').append('<a></a>').attr("href", "profil.php?account=").text(item.account_name).append('<a></a>');          
        }
        $('#suggestion-box').html('<a class=search-member></a>');
        $('.search-member').attr('href', 'profile.php?account='+item.account_name).text(item.account_name);
        //$('.search-member').hide();
        //$('#suggestion-box').hide();
        //$('.link').append("</>");
        // $("#suggestion-box").html(item.account_name);
        // $('#suggestion-box').val("<a>" + item.account_name + "</a>").attr("href", "profil.php?account=Flobinator");
        // $('#search-input').css("background", "#FFF");
    }
    });

});

//Select Users
// function selectUser(val)
// {
//     $('#search-input').val(val);
//     $('#suggestion-box').hide();
// }