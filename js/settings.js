$(document).ready(function()
{
  $.post(
    'ajax/printTheme.php',
    {},
    printTheme,
    'text'
  );
});

$(document).on('click', '#theme', function()
{
  $.post(
    'ajax/settings.php',
    {},
    printTheme,
    'text'
  );
});

const printTheme = (theme) =>
{
  if (theme === "" || theme === undefined)
  {
    $('#theme').text('Theme : dark');
  }
  else
  {
    $('#theme').text('Theme : ' + theme);
  }
  if ($('#theme').text() === 'Theme : light')
  {
    $('#link-theme').attr('href', 'css/light-theme.css');
  }
  else
  {
    $('#link-theme').attr('href', 'css/dark-theme.css');
  }
};
