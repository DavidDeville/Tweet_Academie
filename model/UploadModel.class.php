<?php

require_once 'Model.class.php';

/*
** Model for uploads, handles profile pictures, profile banners and tweets images
*/
final class UploadModel extends Model
{
    /*
    ** The differents types of uploads
    */
    const TWEET = 0;
    const PROFILE = 1;
    const BANNER = 2;

    /*
    ** Array of string to describe types of upload, keys are consts above
    ** This is what will be put in database
    */
    private $types;

    public function __construct()
    {
        parent::__construct();
        
        $this->types = [
            $this::TWEET => 'tweet image',
            $this::PROFILE => 'profile picture',
            $this::BANNER => 'profile banner'
        ];
    }

    /*
    ** Entry point of the model, redirects to the appropriate handling method,
    ** depending of the upload type
    **
    ** @param int $uploader: the account ID of the user
    ** @param string $path: the path to the image to upload
    ** @param int $type: the type of the upload 
    **      @see class consts
    */ 
    public function new(int $uploader, string $path, int $type)
    {
        if ($type === $this::TWEET)
        {
            $this->tweet($uploader, $path);
        }
        else if ($type === $this::PROFILE)
        {
            $this->handle_profile($uploader, $path);
        }
        else if ($type === $this::BANNER)
        {
            $this->handle_banner($uploader, $path);
        }
    }

    /* 
    ** Gets the URL of the profile picture of the requested user and returns it
    **
    ** @param int $user: the ID of the user to get profile picture for
    **
    ** @return string: the URL to the user's profile picture
    */
    public function user_profile(int $user)
    {
        $upload_query = $this->link->perpare(
            'SELECT path
            FROM upload
            WHERE 
                uploader_id = :user &&
                type = :type'
        );
        $upload_query->execute([
            ':user' => $user,
            ':type' => $this->types[$this::PROFILE]
        ]);
        return (
            $upload_query->fetch(PDO::FETCH_ASSOC)
        );
    }

    /* 
    ** Gets the URL of the banner of the requested user and returns it
    **
    ** @param int $user: the ID of the user to get banner for
    **
    ** @return string: the URL to the user's banner
    */ 
    public function user_banner(int $user)
    {
        $upload_query = $this->link->perpare(
            'SELECT path
            FROM upload
            WHERE 
                uploader_id = :user &&
                type = :type'
        );
        $upload_query->execute([
            ':user' => $user,
            ':type' => $this->types[$this::BANNER]
        ]);
        return (
            $upload_query->fetch(PDO::FETCH_ASSOC)
        );
    }

    /*
    ** Inserts a new tweet image in the database
    **
    ** @param int $user: the ID of the user uploading the image
    ** @param string $path: the path to the uploaded image
    */
    private function tweet(int $user, string $path)
    {
        $upload_query = $this->link->prepare(
            'INSERT INTO upload (
                uploader_id,
                path,
                type
            ) VALUES (
                :user,
                :path,
                :type
            )'
        );
        $upload_query->execute([
            ':user' => $user,
            ':path' => $path,
            ':type' => $this->types[$this::TWEET]
        ]);
    }
    
    /*
    ** Decides which action to perform, depending if the user already has a
    ** profile picture or not
    **
    ** @param int $user: the ID of the concerned user
    ** @param string $path: the path to the uploaded image
    */
    private function handle_profile(int $user, string $path)
    {
        if ($this->user_has_profile($user))
        {
            $this->update_profile($user, $path);
        }
        else
        {
            $this->new_profile($user, $path);
        }
    }

    /*
    ** Checks whether or not the user has already a profile picture
    **
    ** @param int $user: the ID of the user to check 
    **
    ** @return bool: true if the user has a profile picture, false otherwise
    */
    public function user_has_profile(int $user)
    {
        $profile_query = $this->link->prepare(
            'SELECT id
            FROM upload
            WHERE
                uploader_id = :user &&
                type = :type'
        );
        $profile_query->execute([
            ':user' => $user,
            ':type' => $this->types[$this::PROFILE]
        ]);
        return (
            $profile_query->rowCount() !== 0
        );
    }

    /*
    ** Inserts a new profile picture in database
    **
    ** @param int $user: the ID of the uploader
    ** @param string $path: the path to the uploaded image
    */
    private function new_profile(int $user, string $path)
    {
        $upload_query = $this->link->prepare(
            'INSERT INTO upload (
                uploader_id,
                path,
                type
            ) VALUES (
                :user,
                :path,
                :type
            )'
        );
        $upload_query->execute([
            ':user' => $user,
            ':path' => $path,
            ':type' => $this->types[$this::PROFILE]
        ]);
    }

    /*
    ** Updates the profile picture of the user
    **
    ** @param int $user: the ID of the user to change profile picture
    ** @param string $path: the path to the uploaded image
    */
    private function update_profile(int $user, string $path)
    {
        $update_query = $this->link->prepare(
            'UPDATE upload
            SET path = :path
            WHERE
                uploader_id = :user &&
                type = :type'
        );
        $update_query->execute([
            ':path' => $path,
            ':user' => $user,
            ':type' => $this->types[$this::PROFILE]
        ]);
    }
    
    /*
    ** Decides which action to perform, depending if the user already has a
    ** profile banner or not
    **
    ** @param int $user: the ID of the concerned user
    ** @param string $path: the path to the uploaded image
    */
    private function handle_banner(int $user, string $path)
    {
        if ($this->user_has_banner($user))
        {
            $this->update_banner($user, $path);
        }
        else
        {
            $this->new_banner($user, $path);
        }
    }

    /*
    ** Checks whether or not the user has already a profile banner
    **
    ** @param int $user: the ID of the user to check 
    **
    ** @return bool: true if the user has a profile banner, false otherwise
    */
    private function user_has_banner(int $user)
    {
        $profile_query = $this->link->prepare(
            'SELECT id
            FROM upload
            WHERE
                uploader_id = :user &&
                type = :type'
        );
        $profile_query->execute([
            ':user' => $user,
            ':type' => $this->types[$this::BANNER]
        ]);
        return (
            $profile_query->rowCount() !== 0
        );
    }

    /*
    ** Inserts a new profile banner in database
    **
    ** @param int $user: the ID of the uploader
    ** @param string $path: the path to the uploaded image
    */
    private function new_banner(int $user, string $path)
    {
        $upload_query = $this->link->prepare(
            'INSERT INTO upload (
                uploader_id,
                path,
                type
            ) VALUES (
                :user,
                :path,
                :type
            )'
        );
        $upload_query->execute([
            ':user' => $user,
            ':path' => $path,
            ':type' => $this->types[$this::BANNER]
        ]);
    }

    /*
    ** Updates the profile banner of the user
    **
    ** @param int $user: the ID of the user to change banner
    ** @param string $path: the path to the uploaded image
    */
    private function update_banner(int $user, string $path)
    {
        $update_query = $this->link->prepare(
            'UPDATE upload
            SET path = :path
            WHERE
                uploader_id = :user &&
                type = :type'
        );
        $update_query->execute([
            ':path' => $path,
            ':user' => $user,
            ':type' => $this->types[$this::BANNER]
        ]);
    }
}

?>