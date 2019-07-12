<?php

ini_set('display_errors', 1);

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
    ** @param string $account_name: the account name to search in the database
    **
    ** @return bool: false if the account was found in database, true otherwise
    */
    public function account_is_available(string $account_name)
    {
        return (
            ! $this->account_exists($account_name)
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
    ** Checks whether or not the user exists in the database
    **
    ** @param string $mail: the mail to search in the database
    **
    ** @return bool: false if the mail was found in database, true otherwise
    */
    public function mail_is_available(string $mail)
    {
        return (
            ! $this->mail_exists($mail)
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
        $update_query = $this->link->prepare(
            'UPDATE user
            SET
                display_name = :pseudo,
                email = :mail,
                birth_date = :birthdate,
                city = :city
            WHERE id = :user_id'
        );
        $update_query->execute([
            ':pseudo' => $_POST['info-name'],
            ':mail' => $_POST['info-email'],
            ':birthdate' => $_POST['info-dob'],
            ':city' => $_POST['info-city'],
            ':user_id' => $this->get_account_id()
        ]);
        if ($_POST['info-pwd'] !== '')
        {
            $this->update_password($_POST['info-pwd']);
        }
        $this->update_session();
    }

    /*
    ** Updates the user password
    **
    ** @param string $password: the new password for the user, non hashed
    */
    private function update_password(string $password)
    {
        $password_query = $this->link->prepare(
            'UPDATE user
            SET password = :password
            WHERE id = :user_id'
        );
        $password_query->execute([
            ':password' => hash('ripemd160', $password . $this->hashSalt),
            ':user_id' => $this->get_account_id()
        ]);
    }

    /*
    ** Updates the session variables for the user from the form on his profile
    */
    private function update_session()
    {
        $_SESSION['pseudo'] = $_POST['info-name'];
        $_SESSION['email'] = $_POST['info-email'];
        $_SESSION['birth-date'] = $_POST['info-dob'];
        $_SESSION['city'] = $_POST['info-city'];
    }

    /*
    ** Returns all information from a user from its account name
    */
    public function get_infos(string $account_name)
    {
        $infos_query = $this->link->prepare(
            'SELECT
                id,
                username,
                display_name,
                email,
                birth_date,
                city
            FROM user WHERE username = :account_name'
        );
        $infos_query->execute([
            ':account_name' => $account_name
        ]);
        return (
            $infos_query->fetch(
                PDO::FETCH_ASSOC
            )
        );
    }

    /*
    ** Makes the connected user follow someone else if he's not already following him
    **
    ** @param string $account_name: the name of the account to follow
    */
    public function follow(string $account_name)
    {
        $target_id = $this->get_infos($account_name)['id'];
        if ($this->follows($target_id))
        {
            return (false);
        }
        else
        {
            $follow_query = $this->link->prepare(
                'INSERT INTO follower (
                    user_id,
                    follower_id,
                    follow_date
                ) VALUES (
                    :user_id,
                    :follower_id,
                    NOW()
                )'
            );
            $follow_query->execute([
                ':user_id' => $target_id,
                ':follower_id' => $this->get_account_id()
            ]);
        }
        return (true);
    }

    /*
    ** Checks if the connected user followed the specified user
    **
    ** @param int $target_id: the ID of the account to check for following
    **
    ** @return bool: true if the connected user follows the target ID, false otherwise
    */
    private function follows(int $target_id)
    {
        $follow_query = $this->link->prepare(
            'SELECT id
            FROM follower
            WHERE
                user_id = :target_id &&
                follower_id = :follower_id'
        );
        $follow_query->execute([
            ':target_id' => $target_id,
            ':follower_id' => $this->get_account_id()
        ]);
        return (
            $follow_query->rowCount() > 0
        );
    }

    /*
    ** Returns the list of people the connected user follows
    **
    ** @return Array: the list of people (usernames) the connected user follows
    */
    public function get_followings()
    {
        $followings_query = $this->link->prepare(
            'SELECT username
            FROM follower
                INNER JOIN user
                    ON follower.user_id = user.id
            WHERE follower_id = :follower_id'
        );
        $followings_query->execute([
            ':follower_id' => $this->get_account_id()
        ]);
        return (
            $followings_query->fetchAll(
                PDO::FETCH_ASSOC
            )
        );
    }

    /*
    ** Returns the list of ID of people the connected user follows
    **
    ** @return Array: the list of people (usernames) the connected user follows
    */
    public function get_followings_id()
    {
        $followings_query = $this->link->prepare(
            'SELECT user_id
            FROM follower
            WHERE follower_id = :follower_id'
        );
        $followings_query->execute([
            ':follower_id' => $this->get_account_id()
        ]);
        return (
            $followings_query->fetchAll(
                PDO::FETCH_ASSOC
            )
        );
    }

    /*
    ** Returns the list of people who follows the connected user
    **
    ** @return Array: the list of people who follows the connected user
    */
    public function get_followers()
    {
        $followers_query = $this->link->prepare(
            'SELECT username
            FROM follower
                INNER JOIN user
                    ON follower.user_id = user.id
            WHERE user_id = :user_id'
        );
        $followers_query->execute([
            ':user_id' => $this->get_account_id()
        ]);
        return (
            $followers_query->fetchAll(
                PDO::FETCH_ASSOC
            )
        );
    }

    /*
    ** Finds a member account from a pattern
    **
    ** @param string $pattern: the pattern to look for
    **
    ** @return Array: every account matching the pattern
    */
    public function by_pattern(string $pattern)
    {
        $pattern_query = $this->link->prepare(
            'SELECT
                username as account_name,
                display_name as pseudo,
                city,
                birth_date
            FROM user
            WHERE username LIKE :pattern'
        );
        $pattern_query->execute([
            ':pattern' => '%' . $pattern . '%'
        ]);
        return ($pattern_query->fetchAll(
            PDO::FETCH_ASSOC
        ));
    }

    /*
    ** Finds members account from a list of pattern
    **
    ** @param string $pattern: patterns to look for
    **
    ** @return Array: every account matching at least one pattern
    */
    public function by_patterns(Array $patterns)
    {
        $matches = [];

        foreach ($patterns as $pattern)
        {
            foreach ($this->by_pattern($pattern) as $match)
            {
                array_push($matches, $match);
            }
        }
        return ($matches);
    }

    /*
    ** Updates the theme depending of the current one
    **
    ** @param int $user_id: The current user ID
    */
    public function change_theme(int $user_id)
    {
      if ($this->get_theme($user_id)['theme'] == 'light')
      {
        $changetodark = $this->link->prepare(
          'UPDATE user
          SET theme_color = "dark"
          WHERE id = :user_id'
        );
        $changetodark->execute([
          ':user_id' => $user_id
        ]);
      }
      else
      {
        $changetolight = $this->link->prepare(
          'UPDATE user
          SET theme_color = "light"
          WHERE id = :user_id'
        );
        $changetolight->execute([
          ':user_id' => $user_id
        ]);
      }
    }

    /*
    ** Returns the current user theme
    **
    ** @param int $user_id: Current user ID
    */
    public function get_theme(int $user_id)
    {
      $get_theme = $this->link->prepare(
        'SELECT theme_color AS "theme"
        FROM user
        WHERE id = :user_id'
      );
      $get_theme->execute([
        ':user_id' => $user_id
      ]);
      return(
        $get_theme->fetch(
          PDO::FETCH_ASSOC
      ));
    }
}

?>
