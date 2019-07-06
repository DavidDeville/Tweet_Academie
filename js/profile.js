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

$('#talk').click((event) =>
{
    event.preventDefault();
});