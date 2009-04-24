<?php

require_once 'Net/URL2.php';
require_once 'Validate.php';
require_once 'Services/ShortURL/Exception.php';

abstract class Services_ShortURL
{
    static private $options = array();

    static private $services = array(
        'bit.ly'      => 'bitly',
        'is.gd'       => 'isgd',
        'tinyurl.com' => 'TinyURL',
        'digg.com'    => 'Digg',
        'tr.im'       => 'trim',
        'short.ie'    => 'shortie'
    );

    static public function factory($service, array $options = array())
    {
        if (!in_array($service, self::$services)) {
            throw new Services_ShortURL_Exception(
                'Service ' . $service . ' is invalid'
            ); 
        }

        $file = 'Services/ShortURL/' . $service . '.php';
        include_once $file;

        $class = 'Services_ShortURL_' . $service;
        if (!class_exists($class, false)) {
            throw new Services_ShortURL_Exception(
                'Service class invalid or missing'
            ); 
        }

        if (empty($options) && isset(self::$options[$service])) {
            $options = self::$options[$service];
        }

        $instance = new $class($options);
        if (!$instance instanceof Services_ShortURL_Interface) {
            throw new Services_ShortURL_Exception(
                'Service instance is invalid'
            ); 
        }

        return $instance;
    }

    static public function detect($url)
    {
        $url  = new Net_URL2($url);
        $host = $url->getHost();

        if (!isset(self::$services[$host])) {
            throw new Services_ShortURL_Exception_UnknownService();
        }

        return self::factory(self::$services[$host]);
    }

    static public function extract($string)
    {
        $m      = array();
        $regExp = '#(?P<url>http://(' . 
                  implode('|', array_keys(self::$services)) . 
                  ')/[a-z0-9A-Z]+)\b#';

        if (!preg_match_all($regExp, $string, $m)) {
            return array();
        }

        $ret = array();
        foreach ($m['url'] as $url) {
            $api = self::detect($url);
            $ret[$url] = $api->expand($url);
        }

        return $ret;
    }

    static public function addService($host, $driver)
    {
        self::$services[$host] = $driver;
    }

    static public function setServiceOptions($service, array $options)
    {
        self::$options[$service] = $options;
    }

    final private function __construct()
    {

    }
}

?>
