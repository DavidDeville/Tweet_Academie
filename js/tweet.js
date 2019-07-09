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
                //Add the tweet using the design found in feed view (WIP)
                $('#test').append('blablabla');
                // $('#target').append('<div>').addClass('list-group>');
                // $('.list-group').append('<div>').addClass('list-group-item list-group-item-action flex-column align-items-start active mt-3');
                alert('success');

            });
        }
    });
});

/*
** From a button on a tweet-div, returns the most outern div making the tweet
**
** @param button: the button that triggered the event
*/
const tweetFromButton = (button) =>
{
    return $(button).parents().closest('.list-group');
};

/*
** Creates the form object containing tweet datas to send on ajax request
**
** @param tweet: the most outern tweet div
*/
const createTweetForm = (tweet) =>
{
    const form = 
    {
        author: tweet.find('.tweet-author').text().substr(1),
        date: tweet.find('.tweet-submited').text(),
        content: tweet.find('.tweet-content').text()
    };
    return form;
};
/*
** Bindings existing and upcoming like buttons on ajax request to like the targeted tweet
**
** @param event: auto-sent event
*/
$('.tweet-like').on('click', (event) =>
{
    //let tweetForm = tweetFromButton(event.target);
    ajaxRequest(
        'ajax/tweet_like.php',
        createTweetForm(tweetFromButton(event.target))
    );
});

/*
** Executes a custom ajax request
**
** @param targetFile: the PHP script to target
** @param form: the data-object to submit to targeted script
*/ 
const ajaxRequest = (targetFile, tweetForm) =>
{
    $.post(
        targetFile,
        tweetForm
    );
};

/*
** Bindings existing and upcoming reply buttons on ajax request to re-post the targeted tweet
**
** @param event: auto-sent event
*/
$('.tweet-retweet').on('click', (event) =>
{
    //let tweetForm = tweetFromButton(event.target);
    ajaxRequest(
        'ajax/tweet_repost.php',
        createTweetForm(tweetFromButton(event.target))
    );
});