<?php

ini_set('display_errors', 1);

require_once 'Model.class.php';

/*
** Model for tweets, contains everything to read / write tweets
*/
class TweetModel extends Model
{
    /*
    ** Function that writes a tweet
    **
    ** @param int $sender_user: user sending the tweet
    **
    ** @param Array $user_convs: contains all of the user's convs
    */
    public function post(int $sender_id, string $content)
    {
        $this->reply($sender_id, $content);
    }

    /*
    ** Function to reply to a tweet
    **
    ** @param int $sender_user: user replying to the tweet
    **
    ** @param string $content: contains all of the tweet content
    **
    ** @param int $parent_post_id: id of the original tweet
    */
    public function reply(int $sender_id, string $content, int $parent_post_id = NULL)
    {
        $tweet_query = $this->link->prepare(
            'INSERT INTO post (
                sender_id, 
                content, 
                parent_post_id, 
                submit_time
            ) VALUES (
                :sender_id,
                :content,
                :parent_post_id,
                NOW()
            )'
        );

        $tweet_query->execute([
            ':sender_id' => $sender_id,
            ':content' => $content,
            ':parent_post_id' => $parent_post_id
        ]);
    }

    /*
    ** Function to delete a tweet
    **
    ** @param int $id: id of the tweet to delete
    */
    public function delete(int $id)
    {
        $delete_query = $this->link->prepare(
            'DELETE FROM post
            WHERE id = :id'
        );

        $delete_query->execute([
            ':id' => $id
        ]);
    }

    /*
    ** Function to return a tweet
    **
    ** @param int $id: id of the tweet to get
    **
    ** @return array: specific tweet passed in parameter
    */
    public function get_tweet(int $id)
    {
        $get_tweet_query = $this->link->prepare(
            'SELECT id,
            sender_id,
            content,
            submit_time
            FROM post
            WHERE id = :id'
        );

        $get_tweet_query->execute([
            ':id' => $id
        ]);

        return(
            $get_tweet_query->fetch(PDO::FETCH_ASSOC)
        );
    }

    /*
    ** Function to get all tweets from logged user
    **
    ** @param int $sender_id: id of the user sending tweets
    **
    ** @return array: all tweets of logged user
    */
    public function by_user(int $sender_id)
    {
        $get_tweet_query = $this->link->prepare(
            'SELECT sender_id,
            content,
            submit_time
            FROM post
            WHERE sender_id = :sender_id'
        );

        $get_tweet_query->execute([
            ':sender_id' => $sender_id
        ]);

        return(
            $get_tweet_query->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    /*
    ** Function to get all tweets from followed users
    **
    ** @param array $following_ids: ids of person you're currently following
    **
    ** @return array: all tweets of followed users
    */
    public function for_user(Array $following_ids)
    {
        $following_list = '(';
        foreach ($following_ids as $following)
        {
            $following_list .= $following['user_id'] . ',';
        }
        $following_list = substr($following_list, 0, strlen($following_list) - 1);
        $following_list .= ')';
        
        $get_followers_tweets = $this->link->prepare(
            'SELECT 
                display_name AS author,
                username as author_account,
                content,
                submit_time
            FROM post INNER JOIN user ON user.id = post.sender_id
            WHERE sender_id IN ' . $following_list . ' 
            ORDER BY submit_time DESC'
        );
        $get_followers_tweets->execute();
        return(
            $get_followers_tweets->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    /*
    ** Function to get all tweets from followed users with a timestamp
    **
    ** @param array $following_ids: ids of person you're currently following
    **
    ** @return array: all tweets of followed users based on a timestamp
    */
    public function for_user_by_time(Array $following_ids, int $timestamp)
    {
        $following_list = '(';
        foreach ($following_ids as $following)
        {
            $following_list .= $following['user_id'] . ',';
        }
        $following_list = substr($following_list, 0, strlen($following_list) - 1);
        $following_list .= ')';
        
        $latest_tweets = $this->link->prepare(
            'SELECT 
                display_name AS author,
                username as author_account,
                content,
                submit_time
            FROM post INNER JOIN user ON user.id = post.sender_id
            WHERE sender_id IN ' . $following_list . ' &&
            submit_time > FROM_UNIXTIME(:stamp) 
            ORDER BY submit_time DESC'
        );
        $latest_tweets->execute([
            ':stamp' =>  $timestamp / 1000
        ]);
        return(
            $latest_tweets->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    /*
    ** Function to modify an existing tweet
    **
    ** @param int $id: id of the tweet to update
    **
    ** @param string $content: replace original content by the new one
    */
    public function update(int $id, string $content)
    {
       $update_tweet_query = $this->link->prepare(
           'UPDATE post 
           SET content = :content
           WHERE id = :id'
       );

       $update_tweet_query->execute([
            ':content' => $content,
            ':id' => $id
       ]);
    }

    /*
    ** Makes the user like the tweet, if he hasn't already
    **
    ** @param int $user_id: id of the user that liked the tweet
    ** @param int $post_id: id of the liked tweet
    */
    public function like(int $user_id, int $post_id)
    {
        if (! $this->is_liked_by($user_id, $post_id))
        {
            $like_tweet_query = $this->link->prepare(
                'INSERT INTO favorite (
                    user_id, 
                    post_id
                ) VALUES (
                    :user_id,
                    :post_id
                )'
            );
    
            $like_tweet_query->execute([
                ':user_id' => $user_id,
                ':post_id' => $post_id
            ]);
        }
    }

    /*
    ** Checks whether or not the user already liked the tweet
    **
    ** @param int $user: the ID of the user
    ** @param int $tweet: the ID of the tweet
    **
    ** @return bool: true if the used liked the post, false otherwise
    */
    public function is_liked_by(int $user, int $tweet)
    {
        $liked_query = $this->link->prepare(
            'SELECT id
            FROM favorite
            WHERE 
                post_id = :tweet &&
                user_id = :user'
        );
        $liked_query->execute([
            ':tweet' => $tweet,
            ':user' => $user
        ]);
        return (
            $liked_query->rowCount() > 0
        );
    }

    /*
    ** Removes the like of the user on the given tweet
    ** If user didn't like the post, nothing will be done
    **
    ** @param int $user_id: the ID of the user
    ** @param int $post_id: the ID of the tweet
    */
    public function unlike (int $user_id, int $post_id)
    {
        if ($this->is_liked_by($user_id, $post_id))
        {
            $delete_query = $this->link->prepare(
                'DELETE FROM favorite
                WHERE 
                    user_id = :user &&
                    post_id = :post'
            );
            $delete_query->execute([
                ':user' => $user_id,
                ':post' => $post_id
            ]);
        }
    }

    /*
    ** Finds a tweet from it's content, author and submit time
    **
    ** @param string $content: the content of the tweet to find
    ** @param string $date: the date and time to tweet has been submited on
    ** @param int $author: the ID of the author
    **
    ** @return int: the ID of the found tweet, or false if no match is found
    **      If several matches are found, only the first one is returned
    */
    public function find(string $content, string $date, int $author)
    {
        $find_query = $this->link->prepare(
            'SELECT id 
            FROM post
            WHERE
                sender_id = :user &&
                content = :content &&
                submit_time = :date'
        );
        $find_query->execute([
            ':user' => $author,
            ':content' => $content,
            ':date' => $date
        ]);
        if ($find_query->rowCount() > 0)
        {
            return (
                $find_query->fetch(PDO::FETCH_ASSOC)['id']
            );
        }
        return (false);
    }

    /*
    ** Function to get the number of likes of a tweet
    **
    ** @param int $post_id: id of the liked tweet
    **
    ** @return array: number of likes
    */
    public function get_likes(int $post_id)
    {
        $get_likes_query = $this->link->prepare(
            'SELECT COUNT(id)
            FROM favorite
            WHERE post_id = :post_id'
        );

        $get_likes_query->execute([
            ':post_id' => $post_id
        ]);

        return(
            $get_likes_query->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    /*
    ** Function to get the id of the likers of a tweet
    **
    ** @param int $user_id: id of the user that liked the tweet
    **
    ** @return array: id of the likers
    */
    public function get_likers(int $post_id)
    {
        $get_likers_query = $this->link->prepare(
            'SELECT user_id
            FROM favorite
            WHERE post_id = :post_id'
        );

        $get_likers_query->execute([
            ':post_id' => $post_id
        ]);

        return(
            $get_likers_query->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    /*
    ** Function to repost a tweet
    **
    ** @param int $sender_id: id of the person sending the tweet
    **
    ** @param int $source_id: id of the original tweet
    */
    public function repost(int $sender_id, int $source_id)
    {
        $source = $this->get_tweet($source_id);

        $repost_query = $this->link->prepare(
            'INSERT INTO post (
                sender_id,
                source_post_id, 
                content,
                submit_time
            ) VALUES (
                :sender_id,
                :source_post_id,
                :content,
                NOW()
            )'
        );

        $repost_query->execute([
            ':sender_id' => $sender_id,
            ':source_post_id' => $source['id'],
            ':content' => $source['content']
        ]);
    }

    /*
    ** Function to get tweets by Hashtag
    **
    ** @param string $content: content of the tweets containing a specific hashtag
    **
    ** @return array: all tweets containing the hashtag
    */
    public function hashtag(string $hashtag)
    {
        $hashtag_query = $this->link->prepare(
            'SELECT 
                post.id,
                user.display_name AS author,
                user.username AS author_account,
                content,
                submit_time
            FROM post
            INNER JOIN user ON user.id = post.sender_id
            WHERE content LIKE :hashtag
            ORDER BY submit_time DESC'
        );

        $hashtag_query->execute([
            ':hashtag' => '%#' . $hashtag . '%'
        ]);

        return(
            $hashtag_query->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    /*
    ** Function to get tweets from a list of #hashtags
    **
    ** @param Array $hashtags: array of strings, every requested hashtags, without the '#'
    **
    ** @return Array: every tweets containing at least one requested hashtag
    */
    public function hashtags(Array $hashtags)
    {
        $tweets = [];

        foreach ($hashtags as $hashtag)
        {
            foreach ($this->hashtag($hashtag) as $tweet)
            {
                array_push($tweets, $tweet);
            }
        }
        return ($tweets);
    }
}

?>