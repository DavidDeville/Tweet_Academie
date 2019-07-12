/*
** The timestamp after which messages have to be fetched
*/
let timestamp = Date.now();

/*
** Gets all new messages from the server and adds them to the display zone
*/
const fetchMessages = () =>
{
  let profileLink = $('.nav-link[href^="profile.php"]').attr('href'); 
  let account_name = profileLink.substr(profileLink.lastIndexOf('=') + 1);

  $.post(
    'ajax/messenger_refresh.php', {
    timestamp: timestamp,
    'conv-id': urlParams().id,
    account_name: account_name
  }).then((response) => {
    $(response).insertBefore('#messenger-controls');
    timestamp = Date.now();
  });
};

/*
** Callback for send button
** Checks the message form, then submit it if there's no errors, and updates the display zone
*/
$('#messenger-send').click((event) =>
{
  event.preventDefault();
  
  $.post(
      'ajax/message_send.php',
      {
          'messenger-input': $('#messenger-input').val()
      },
      treatAjaxFormResponse,
      'json'
  ).then(() =>
  {
    if ($('#messenger-form').find('.invalid-feedback').length === 0)
    {
      $.post(
        currentUrl(),
        {
          'messenger-input': $('#messenger-input').val()
        }
      ).then(() =>
      {
        fetchMessages();
        $('#messenger-input').val('');
      });
    }
  });
});

/*
** Autorefresh
*/
setInterval(fetchMessages, 3000);