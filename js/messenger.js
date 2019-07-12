/*const trololo = (response) =>
{
  console.log(response);
};

const refresh = () =>
{
  let bite = urlParams();
  $.post(
    'ajax/messenger_refresh.php',
    {
      id_conv: bite.id,
      id_msg: $('.id_msg').last().text()
    },
    trololo,
    'text'
  );
};

$(document).ready(function()
{
  var reloadMessages = setInterval(refresh, 5000);

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
          },
          'text'
        ).then(() =>
        {
          reloadMessages
        });
      }
    });
  });
});

// requête ajax sur un fichier php qui récupère les messages envoyés après un certain temps, réponse au format html
// la requête envoit la réponse à une fonction s'occupe de rajouter chaque message à la $page

// après envoi d'un message -> auto refresh
// au bout de quelques secondes -> auto refresh
*/

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
    'ajax/messenger_refresh.php',
    {
      timestamp: timestamp,
      'conv-id': urlParams().id,
      account_name: account_name
    },
    displayMessages,
    'text'
  );

  timestamp = Date.now();
};

/*
** Callback for fetchMessages()
** Adds messages to the display zone
**
** @var htmlResponse: messages as sent by the ajax request
*/
const displayMessages = (htmlResponse) =>
{
  $(htmlResponse).insertBefore('#messenger-controls');
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