/*
** Javascript on public profiles
*/

/*
** AJAX request to follow the target on 'follow' button
*/
$('#follow').click((event) =>
{
    event.preventDefault();

    $.post(
        'ajax/follow.php',
        {
            target: urlParams().account
        }
    );
});

$('#talk').click((event) =>
{
    event.preventDefault();
});