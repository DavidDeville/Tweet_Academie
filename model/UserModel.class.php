<?php

require_once 'Model.class.php';

/*
** Model for the user, contains everything related to the user database
*/
class UserModel extends Model
{
    /*
    ** The hash used to hash passwords
    */
    private $hashSalt;

    public function __construct()
    {
        parent::__construct();
        $this->hashSalt = 'vive le projet tweet_academy';
    }

    /*
    ** Checks whether or not the user exists in the database
    **
    ** @param string $account_name: the account name to search in the database
    **
    ** @return bool: true if the account was found in database, false otherwise
    */
    public function account_exists(string $account_name)
    {
        $account_query = $this->link->prepare(
            'SELECT id FROM user WHERE username = :username'
        );
        $account_query->execute([
            ':username' => $account_name
        ]);
        return (
            $account_query->rowCount() > 0
        );
    }

    /*
    ** Checks whether or not the user exists in the database
    **
    ** @param string $mail: the mail to search in the database
    **
    ** @return bool: true if the mail was found in database, false otherwise
    */
    public function mail_exists(string $mail)
    {
        $mail_query = $this->link->prepare(
            'SELECT id FROM user WHERE email = :mail'
        );
        $mail_query->execute([
            ':mail' => $mail
        ]);
        return (
            $mail_query->rowCount() > 0
        );
    }

    /*
    ** Checks whether or not the given password is correct
    **
    ** @param string $mail: the mail of the account to test
    ** @param string $password: the password to check
    **
    ** @return bool: true if the password is correct, false otherwise
    */
    public function password_match(string $mail, string $password)
    {
        $password_query = $this->link->prepare(
            'SELECT password FROM user WHERE email = :mail'
        );
        $password_query->execute([
            ':mail' => $mail
        ]);
        $user_password = $password_query->fetch(
            PDO::FETCH_ASSOC
        )['password'];
        return (
            hash(
                'ripemd160', 
                $password . $this->hashSalt
            ) === $user_password
        );
    }

    /*
    ** Checks if the user is currently connected
    **
    ** @return bool: true if the user is connected, false otherwise
    */
    public function is_connected()
    {
        return (
            count($_SESSION) > 0
        );
    }

    /*
    ** Puts the user in the database, based on sign-up form
    */
    public function register()
    {
        $register_query = $this->link->prepare(
            'INSERT INTO user (
                username, 
                display_name, 
                email, 
                password, 
                birth_date, 
                city
            ) VALUES (
                :account_name,
                :display_name,
                :mail,
                :password,
                :birth_date,
                :city
            )'
        );
        $register_query->execute([
            ':account_name' => $_POST['signup-accname'],
            ':display_name' => $_POST['signup-username'],
            ':mail' => $_POST['signup-mail'],
            ':password' => hash('ripemd160', $_POST['signup-pwd'] . $this->hashSalt),
            ':birth_date' => $_POST['signup-dob'],
            ':city' => $_POST['signup-city']
        ]);
        
    }

    /*
    ** Connects the user and stores his informations in $_SESSION
    ** To access his informations, please use accessors below
    **      @see $this->get_account_id()
    **      @see $this->get_account_name()
    **      @see $this->get_pseudo()
    **      @see $this->get_mail()
    **      @see $this->get_birth_date()
    **      @see $this->get_city()
    */
    public function login()
    {
        if (isset($_POST['signup-mail']))
        {
            $mail = $_POST['signup-mail'];
        }
        if (isset($_POST['signin-mail']))
        {
            $mail = $_POST['signin-mail'];
        }
        $infos_query = $this->link->prepare(
            'SELECT 
                id as "account-id", 
                username as "account-name", 
                display_name as "pseudo", 
                email, 
                birth_date as "birth-date", 
                city
            FROM user
            WHERE email = :mail'
        );
        $infos_query->execute([
            ':mail' => $mail
        ]);
        $infos = $infos_query->fetch(
            PDO::FETCH_ASSOC
        );
        foreach (array_keys($infos) as $user_info)
        {
            $_SESSION[$user_info] = $infos[$user_info];
        }
    }

    /*
    ** Wraper for the field user.id in database
    ** Requires the user to be connected
    **
    ** @return string: the id of the account (unique)
    */
    public function get_account_id()
    {
        if ($this->is_connected())
        {
            return ($_SESSION['account-id']);
        }
    }

    /*
    ** Wraper for the field user.username in database
    ** Requires the user to be connected
    **
    ** @return string: the name of the account (unique)
    */
    public function get_account_name()
    {
        if ($this->is_connected())
        {
            return ($_SESSION['account-name']);
        }
    }

    /*
    ** Wraper for the field user.display_name in database
    ** Requires the user to be connected
    **
    ** @return string: the pseudo of the account
    */
    public function get_pseudo()
    {
        if ($this->is_connected())
        {
            return ($_SESSION['pseudo']);
        }
    }

    /*
    ** Wraper for the field user.email in database
    ** Requires the user to be connected
    **
    ** @return string: the mail of the account
    */
    public function get_mail()
    {
        if ($this->is_connected())
        {
            return ($_SESSION['email']);
        }
    }

    /*
    ** Wraper for the field user.birth_date in database
    ** Requires the user to be connected
    **
    ** @return string: the birth-date of the owner of the account
    */
    public function get_birth_date()
    {
        if ($this->is_connected())
        {
            return ($_SESSION['birth-date']);
        }
    }

    /*
    ** Wraper for the field user.city in database
    ** Requires the user to be connected
    **
    ** @return string: the city of the owner of the account
    */
    public function get_city()
    {
        if ($this->is_connected())
        {
            return ($_SESSION['city']);
        }
    }

    /*
    ** Disconnects the user and removes everything from $_SESSION
    */
    public function logout()
    {
        foreach (array_keys($_SESSION) as $user_info)
        {
            unset($_SESSION[$user_info]);
        }
        session_destroy();
    }

    /*
    ** Updates the user info from the update form on his profile
    */
    public function update()
    {
        $correspondings = [
            'info-email' => 'email',
            'info-name' => 'display_name',
            'info-city' => 'city',
            'info-dob' => 'birth_date',
            'info-pwd' => 'password'
        ];
        $update_query_string = 'UPDATE user SET ';
        if ($_POST['info-pwd'] === '')
        {
            $_POST = array_diff($_POST, [$_POST['info-pwd']]);
        }
        if (isset($_POST['info-checkpwd']))
        {
            $_POST = array_diff($_POST, [$_POST['info-checkpwd']]);
        }
        $_POST = array_diff($_POST, [$_POST['info-oldpwd']]);
        foreach (array_keys($_POST) as $user_info)
        {
            $update_query_string .= $correspondings[$user_info] . ' = "' . $_POST[$user_info] . '", ';
        }
        $update_query_string = substr($update_query_string, 0, strlen($update_query_string) - 2);
        $update_query_string .= ' WHERE id = ' . $this->get_account_id();
        $update_query = $this->link->prepare(
            $update_query_string
        );
        $update_query->execute();
    }
}

?>