/*
** Javascript on public profiles
*/

/*
** Treats the Ajax follow request and displays the response
** in a custom popup
**
*/
const displayFollowStatus = (response) =>
{
    let popup = 
    {   
        position: 'top-end',
        showConfirmButton: false,
        timer: 1666
    };
    if (response.state)
    {
        popup.title = 'You are now following @' + response.target;
        popup.type = 'success';
    }
    else
    {
        popup.title = 'You are already following @' + response.target;
        popup.type = 'error';
    }
    Swal.fire(popup);
};

/*
** AJAX request to follow the target on 'follow' button
*/
$('#follow').click((event) =>
{
    event.preventDefault();

    let target = urlParams().account;

    $.post(
        'ajax/follow.php',
        {
            target: target
        },
        displayFollowStatus,
        'json'
    );
});

/*
** Redirects the user to the conversation page
*/
const displayConversation = (convID) =>
{
    location.replace('messenger.php?id=' + convID);
};

/*
** AJAX request to get ID of the conversation
*/
$('#talk').click((event) =>
{
    event.preventDefault();
    console.log(urlParams);

    $.post(
        'ajax/profile_talk.php',
        {
            target: urlParams().account
        },
        displayConversation,
        'text'
    );
});