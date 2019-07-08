<?php

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
        // crééer tweet avec ces paramètres
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
            echo 'current state: ' . $following_list;
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
            WHERE sender_id IN ' . $following_list
        );
        $get_followers_tweets->execute();
        return(
            $get_followers_tweets->fetchAll(PDO::FETCH_ASSOC)
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
    ** Function to like a tweet
    **
    ** @param int $user_id: id of the user that liked the tweet
    **
    ** @param int $post_id: id of the liked tweet
    */

    public function like(int $user_id, int $post_id)
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
        $tweets_by_hashtag_query = $this->link->prepare(
            'SELECT id,
            sender_id,
            content,
            submit_time
            FROM post
            WHERE content LIKE :hashtag'
        );

        $tweets_by_hashtag_query->execute([
            ':hashtag' => '%' . $hashtag . '%'
        ]);

        return(
            $tweets_by_hashtag_query->fetchAll(PDO::FETCH_ASSOC)
        );
    }
}
?>