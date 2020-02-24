<?php

ini_set('display_errors', 1);

require_once 'PageController.class.php';
require_once 'model/UserModel.class.php';
require_once 'model/UploadModel.class.php';
require_once 'FormProfileUpdateController.class.php';
require_once 'FormProfileUploadController.class.php';

/*
** Controller for profile.php
*/
final class ProfileController extends PageController
{
    /*
    ** The user model
    */
    private $user;

    /*
    ** The info-update form controller
    */
    private $update;

    /*
    ** The upload model
    */
    private $uploadModel;

    /*
    ** The picture-upload form controller
    */
    private $upload;

    public function __construct()
    {
        parent::__construct();

        $this->user = new UserModel();
        $this->update = new FormProfileUpdateController();
        $this->upload = new FormProfileUploadController();
        $this->uploadModel = new UploadModel();

        $this->redirect_on_invalid_profile();
    }

    /*
    ** Displays the appropriate view for the current context
    **      @see PageController::display_view()
    */
    public function display_view()
    {
        
        if ($this->requested_self_profile())
        {
            $this->display_self_profile();
        }
        else
        {
            $this->display_public_profile();
        }
    }

    /*
    ** Decides which action should be done
    **      @see PageController::handle_request()
    */
    public function handle_request()
    {     
        if ($this->profile_updated())
        { 
            if ($this->update->is_valid)
            {
                if ($this->password_is_correct())
                {
                    if ($this->valid_mail())
                    {
                        $this->user->update();
                    }
                }
            }
            
        }
        else if ($this->picture_uploaded())
        {
            if ($this->upload->is_valid())
            {
                $this->upload_image();
            }
        }
    }

    /*
    ** Stores the uploaded image (from upload form) to the image directory
    ** on the server, and ensure the target directory exists
    */
    private function upload_image()
    {
        $this->create_directory('uploads', 'profile');

        $image_data = explode(',', $_POST['upload-file']);
        $image_format = $image_data[0];
        $image_extension = substr($image_format, strpos($image_format, '/') + 1);
        $image_extension = substr($image_extension, 0, strpos($image_extension, ';'));
        $image_content = $image_data[1];
        $image_path = 'uploads/profile/' . $this->user->get_account_name() . '.' . $image_extension;
        
        $current_profile = $this->uploadModel->user_profile($this->user->get_account_id());
        if ($current_profile !== false)
        {
            unlink($current_profile);
        }
        $image_stream = fopen(
            $image_path,
            'wb'
        );
        fwrite($image_stream, base64_decode($image_content));
        fclose($image_stream);

        $this->uploadModel->new(
            $this->user->get_account_id(),
            $image_path,
            UploadModel::PROFILE
        );
    }
    
    /*
    ** Creates the given directory if it doesn't already exist
    **
    ** @param string $path: where to create the directory
    ** @param string $name: the directory to create inside $path
    */
    private function create_directory(string $path, string $name)
    {
        if ((! file_exists($path)) || (! is_dir($path)))
        {
            mkdir($path);
        }
        $target = $path . '/' . $name;
        if ((! file_exists($target)) || (! is_dir($target)))
        {
            mkdir($target);
        }
    }

    /*
    ** Redirects the user if invalid profile is requested, ie
    ** trying to see the profile of non-existing user
    */
    private function redirect_on_invalid_profile()
    {
        if (isset($_GET['account']))
        {
            $target = $_GET['account'];
            if ($this->user->account_is_available($target))
            {
                header('location: index.php');
            }
        }
        else
        {
            header('location: index.php');
        }
    }

    /*
    ** Checks if the user entered the correct password
    **
    ** @return bool: true if the user entered the correct password, false otherwise
    */
    private function password_is_correct()
    {
        return (
            $this->user->password_match(
                $this->user->get_mail(), 
                $_POST['info-oldpwd']
            )
        );
    }

    /*
    ** Checks if the user entered a valid mail
    **
    ** @return bool: true if the user entered the mail bound to his account,
    **      if another mail has been entered, returns trus if it's available, false otherwise
    */ 
    private function valid_mail()
    {
        $is_old_mail = ($_POST['info-email'] === $this->user->get_mail());
        $new_mail_available = (
            (! $is_old_mail) &&
            $this->user->mail_is_available($_POST['info-email'])
        );

        return (
            $is_old_mail || $new_mail_available
        );
    }
    
    /*
    ** Checks if the user is trying to see his own profile
    **
    ** @return bool: true if the user requested hiw own profile, false otherwise
    */
    private function requested_self_profile()
    {
        return (
            $_GET['account'] === $this->user->get_account_name()
        );
        return (false);
    }

    /*
    ** Displays the connected user's profile
    */
    private function display_self_profile()
    {
        echo $this->twig->render('self_profile.htm.twig', [
            'profile_picture' => $this->uploadModel->user_profile($this->user->get_account_id()),
            'followers' => $this->user->get_followers(),
            'followings' => $this->user->get_followings(),
            'account_name' => $this->user->get_account_name(),
            'email' => $this->user->get_mail(),
            'name' => $this->user->get_pseudo(),
            'city' => $this->user->get_city(),
            'dob' => $this->user->get_birth_date()
        ]);
    }

    /*
    ** Displays the profile of someone else than the connected user
    */
    private function display_public_profile()
    {
        $target = $this->user->get_infos($_GET['account']);
        
        echo $this->twig->render('profile.htm.twig', [
            'profile_picture' => $this->uploadModel->user_profile($target['id']),
            'account_name' => $this->user->get_account_name(),
            'target_name' => $target['display_name'],
            'email' => $target['email'],
            'name' => $target['display_name'],
            'city' => $target['city'],
            'dob' => $target['birth_date']
        ]);
    }

    /*
    ** Checks if the user submited the sign-up form
    **
    ** @return bool: true if user submited sign-up form, false otherwise
    */
    private function profile_updated()
    {
        return (
            $this->form_matches(
                'info'
            )
        );
    }

    /*
    ** Checks if the user submited the sign-in form
    **
    ** @return bool: true if user submited sign-in form, false otherwise
    */
    private function picture_uploaded()
    {
        return (
            $this->form_matches(
                'upload'
            )
        );
    }
}

?>