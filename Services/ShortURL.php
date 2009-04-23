<?php

require_once 'Services/ShortURL/Exception.php';

abstract class Services_ShortURL
{
    static public function factory($service, array $options = array())
    {
        $file = 'Services/ShortURL/' . $service . '.php';
        include_once $file;

        $class = 'Services_ShortURL_' . $service;
        if (!class_exists($class, false)) {
            throw new Services_ShortURL_Exception(
                'Service class invalid or missing'
            ); 
        }

        $instance = new $class($options);
        if (!$instance instanceof Services_ShortURL_Interface) {
            throw new Services_ShortURL_Exception(
                'Service instance is invalid'
            ); 
        }

        return $instance;
    }

    final private function __construct()
    {

    }
}

?>
