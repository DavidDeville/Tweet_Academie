/*
** URL functions
*/

/*
** Returns an object of args in the URL
*/
const urlParams = () =>
{
    let args = {};
    let url = window.location.toString();
    let paramString = url.substr(url.lastIndexOf('/'));
    paramString = paramString.substr(paramString.indexOf('?') + 1);

    let argsList = paramString.split('&');
    for (let arg of argsList)
    {
        let split = arg.split('=');
        args[split[0]] = split[1];
    }
    return args;
};
