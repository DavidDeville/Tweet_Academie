const trololo = (response) =>
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
