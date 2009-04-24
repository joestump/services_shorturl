<?php

interface Services_ShortURL_Interface 
{
    /**
     */
    public function shorten($url);

    /**
     */
    public function expand($url);

    /**
     */
    public function stats($url);

    /**
     */
    public function info($url);
}

?>
