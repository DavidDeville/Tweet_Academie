<?php

require_once 'PageController.class.php';

/*
** Controller for the index
*/
class IndexController extends PageController
{
    public function signed_up()
    {
        return false;
    }
}

?>