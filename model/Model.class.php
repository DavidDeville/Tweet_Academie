<?php

/*
** A base model 
*/
abstract class Model
{
    /*
    ** The connection do the database
    */  
    protected $link;

    public function __construct()
    {
        try
        {
            $this->link = new PDO(
                'mysql:host=localhost;dbname=my_meetic', 
                'root', 
                'root'
            );
        }
        catch (Exception $exception)
        {
            die($exception->getMessage());
        }
    }
}

?>