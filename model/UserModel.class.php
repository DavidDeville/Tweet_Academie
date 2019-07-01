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
        $this->hashSalt = 'si tu aimes la wac tape dans tes mains';
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
            'SELECT * FROM user WHERE username = :username'
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
            'SELECT * FROM user WHERE email = :mail'
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
}

?>