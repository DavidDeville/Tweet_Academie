<?php

ini_set('display_errors', 1);

require_once 'FormController.class.php';

/*
** Form Validation Base Controller
*/
final class FormTweetController extends FormController
{
    /*
    ** Valid image extensions for upload
    */
    private $valid_extensions;
    
    public function __construct()
    {
        parent::__construct();

        $this->valid_extensions = ['png', 'jpg', 'jpeg'];
    }

    /*
    ** Checks if the whole form is valid
    ** 
    ** @return bool: true if the form is valid, otherwise, returns false
    */
    public function is_valid()
    {
        $this->check_content();
        $this->check_upload();

        return(
            parent::is_valid()
        );
    }

    /* 
    ** Function to check the tweet content
    ** 
    ** @return bool: true if the form is valid, otherwise, returns false
    */
    public function check_content()
    {
        if (! $this->field_is_filled('tweet-content', 'Your tweet can\'t be empty'))
        {
            return (false);
        }
        else if (strlen($_POST['tweet-content']) > 140)
        {
            $this->set_state('tweet-content', 'Your tweet can\'t exceed 140 characters');
            return (false);
        }
        return (true);
    }

    /*
    ** Checks if the tweet contains an image to upload
    ** Data of the image has to be posted in 'tweet-upload-content'
    **
    ** @return bool: true if tweet contains an image,false otherwise
    */
    public function contains_upload() 
    {
        return ($_POST['tweet-upload-content'] !== '');
    }

    /*
    ** Checks if the form contains a valid upload
    ** IE no upload, or an image with supported format
    ** Writes in $status with key 'tweet-upload'
    **
    ** @return bool: true if upload is valid or missing, false otherwise
    */
    private function check_upload()
    {
        if ($_POST['tweet-upload-name'] === '' && $_POST['tweet-upload-content'] === '')
        {
            $this->set_state('tweet-upload', $this->valid_message);
            return (true);
        }

        $extension = explode(',', $_POST['tweet-upload-content'])[0];
        $extension = substr($extension, strpos($extension, '/') + 1);
        $extension = substr($extension, 0, strpos($extension, ';'));

        if (! in_array($extension, $this->valid_extensions))
        {
            $this->set_state('tweet-upload', 'Image format not supported');
            return (false);
        }
        else
        {
            $this->set_state('tweet-upload', $this->valid_message);
            return (true);
        }
    }
}
?>