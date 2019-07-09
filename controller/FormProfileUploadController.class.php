<?php

require_once 'FormController.class.php';

/*
** Controller for the profile-upload form
*/
final class FormProfileUploadController extends FormController
{
    /*
    ** The set of valid image formats
    */
    private $valid_types;
    
    public function __construct()
    {
        parent::__construct();

        $this->valid_types = ['png', 'jpg'];
    }

    public function is_valid()
    {
        $this->file_chosen();
        $this->valid_type();

        return (
            parent::is_valid()
        );
    }

    private function file_chosen()
    {
        return (
            $this->field_is_filled(
                'upload-file',
                'Please choose an image'
            )
        );
    }
    
    private function valid_type()
    {
        $extension = substr(
            $_POST['upload-name'], 
            strrpos($_POST['upload-name'], '.') + 1
        );

        if ($this->field_is_valid('upload-file'))
        {
            if (! in_array($extension, $this->valid_types))
            {
                $this->set_state('upload-file', 'Image format non supported');
            }
        }
    }
}

?>