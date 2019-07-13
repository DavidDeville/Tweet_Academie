// /*
// ** From a button on a tweet-div, returns the most outern div making the tweet
// **
// ** @param button: the button that triggered the event
// */
// const tweetFromButton = (button) =>
// {
//     return $(button).parents().closest('.list-group');
// };

// /*
// ** Creates the form object containing tweet datas to send on ajax request
// **
// ** @param tweet: the most outern tweet div
// */
// const createTweetForm = (tweet) =>
// {
//     const form = 
//     {
//         author: tweet.find('.tweet-author').text().substr(1),
//         date: tweet.find('.tweet-submited').text(),
//         content: tweet.find('.tweet-content').text()
//     };
//     return form;
// };

// /*
// ** Bindings existing and upcoming like buttons on ajax request to like the targeted tweet
// **
// ** @param event: auto-sent event
// */
// $('.tweet-like').on('click', (event) =>
// {
//     //let tweetForm = tweetFromButton(event.target);
//     ajaxRequest(
//         'ajax/tweet_like.php',
//         createTweetForm(tweetFromButton(event.target))
//     );
// });

// /*
// ** Executes a custom ajax request
// **
// ** @param targetFile: the PHP script to target
// ** @param form: the data-object to submit to targeted script
// */ 
// const ajaxRequest = (targetFile, tweetForm) =>
// {
//     $.post(
//         targetFile,
//         tweetForm
//     );
// };

// /*
// ** Bindings existing and upcoming reply buttons on ajax request to re-post the targeted tweet
// **
// ** @param event: auto-sent event
// */
// $('.tweet-retweet').on('click', (event) =>
// {
//     //let tweetForm = tweetFromButton(event.target);
//     ajaxRequest(
//         'ajax/tweet_repost.php',
//         createTweetForm(tweetFromButton(event.target))
//     );
// });

// /*
// ** Sends an ajax request and determines if the form should be submited
// **
// ** @see forms.js for callback
// */
// $('#tweet-send').click((event) =>
// {
//     event.preventDefault();

//     const form =
//     {
//         'tweet-content': $('#tweet-content').val() // La valeur du tweet
//     };

//     /*
//     ** We target a file (the .php one)
//     ** We point out the data type sent to the file
//     ** The next parameter is the name of the return function
//     ** And the last one is the return of data's type
//     **
//     ** If no 'invalid-feedback' has been found
//     ** another request with the data is sent to index.php
//     ** and refreshes it
//     */
//     $.post(
//         'ajax/tweet_send.php',
//         form,
//         treatAjaxFormResponse,
//         'json'
//     ).then(() =>
//     {
//         if ($('#tweet-send').find('.invalid-feedback').length === 0)
//         {
//             $.post(
//                 'index.php',
//                 form,
//                 trololo,
//                 'text'
//             ).then(() =>
//             {
//                 tweetRefresh();
//                 $('#tweet-content').val('');
//             });
//         }
//     });
// });

// const trololo = (response) =>
// {
//     console.log(response);
// };

// const tweetRefresh = () =>
// {
//     $.post(
//         'ajax/tweet_refresh.php',
//         {
//             timestamp: timestamp
//         }
//     ).then((htmlResponse) =>
//     {
//         $('.jumbotron').prepend(htmlResponse)
//         timestamp = Date.now();    
//     });
// };

// let timestamp = Date.now();

// /*
// ** Autorefresh
// */
// setInterval(tweetRefresh, 3000);

  

///////////////////////////////////////
/*
** Ajax request to upload the profile picture
*/
/*
$('#upload-submit').click((event) =>
{
    
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
});*/
/////////////////////////////////////////////////



const trololo = (response) =>
{
    console.log(response);
};



/*
** Reader has to be set on image selection, otherwise it's undefined
*/ 
$('#tweet-upload').change(() =>
{
    reader.readAsDataURL($('#tweet-upload')[0].files[0]);
});

/*
** Ajax request to upload the tweet
*/
$('#tweet-send').click((event) =>
{
    event.preventDefault();

    let filename = $('#tweet-upload').val();
    filename = filename.substr(filename.lastIndexOf('\\') + 1);

    let tweetForm = 
    {
        'tweet-content': $('#tweet-content').val(),
        'tweet-target': false,
        'tweet-upload-name': filename,
        'tweet-upload-content': reader.result
    };

    $.post(
        'ajax/tweet_send.php',
        tweetForm,
        treatAjaxFormResponse,
        'json'
    ).then(() =>
    {
        if ($('#tweet').find('.invalid-feedback').length === 0)
        {
            $.post(
                currentUrl(),
                tweetForm
            ).then(() =>
            {
                tweetRefresh();
                $('#tweet-content').val('');
            });
        }
    });
});

$('.tweet-reply').click((event) => 
{
    event.preventDefault();
});

/*
** Ajax request to repost a tweet
*/
$('.tweet-retweet').click((event) =>
{
    event.preventDefault();

    let tweet = $(event.target).closest('.tweet');
    let tweetForm = 
    {
        'tweet-content': tweet.find('.tweet-content').text(),
        'tweet-author': tweet.find('.tweet-author').text().substr(1),
        'tweet-submited': tweet.find('.tweet-submited').text(),
        'tweet-action': 'tweet-repost'
    };

    $.post(
        currentUrl(),
        tweetForm
    ).then(() =>
    {
        tweetRefresh();
    });
});

/*
** Ajax request to like a tweet
*/
$('.tweet-like').click((event) => 
{
    event.preventDefault();

    let tweet = $(event.target).closest('.tweet');
    let tweetForm = 
    {
        'tweet-content': tweet.find('.tweet-content').text(),
        'tweet-author': tweet.find('.tweet-author').text().substr(1),
        'tweet-submited': tweet.find('.tweet-submited').text(),
        'tweet-action': 'tweet-like'
    };

    $.post(
        currentUrl(),
        tweetForm,
        trololo,
        'text'
    );
});

/*
** Fetches latest tweets and adds them to the current page
*/
const tweetRefresh = () =>
{
    $.post(
        'ajax/tweet_refresh.php',
        {
            timestamp: timestamp
        }
    ).then((htmlTweets) =>
    {
        $('.jumbotron').prepend(htmlTweets);
    });

    timestamp = Date.now();
};

/*
** The image reader, required for both image selection and upload
*/
let reader = new FileReader();

/*
** The timstamp for auto-refresh
*/
let timestamp = Date.now();

/*
** Auto-refresh every 3s
*/
setInterval(tweetRefresh, 3000);