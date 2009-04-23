<?php

interface Services_ShortURL_Interface 
{
    /**
     */
    public create($url);

    /**
     */
    public expand($url);

    /**
     */
    public stats($url);

    /**
     */
    public info($url);
}

?>
