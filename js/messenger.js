const trololo = (response) =>
{
  console.log(response);
};

const refresh = () =>
{
  $.post(
    'ajax/refresh.php',
    {id_msg: ...},
    trololo,
    'text'
  )
};

var reloadMessages = setInterval(refresh, 1000)

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
