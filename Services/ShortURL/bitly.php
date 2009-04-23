<?php

require_once 'Services/ShortURL/Common.php';
require_once 'Services/ShortURL/Interface.php';
require_once 'Services/ShortURL/Exception/NotImplemented.php';
require_once 'Services/ShortURL/Exception/CouldNotCreate.php';
require_once 'Services/ShortURL/Exception/CouldNotExpand.php';
require_once 'Services/ShortURL/Exception/InvalidOptions.php';

class      Services_ShortURL_bitly
extends    Services_ShortURL_Common
implements Services_ShortURL_Interface
{
    private $api = 'http://api.bit.ly';

    public function __construct(array $options = array(), 
                                HTTP_Request2 $req = null) 
    {
        parent::__construct($options, $req);

        if (!isset($this->options['login'])) {
            throw new Services_ShortURL_Exception_InvalidOptions(
                'A login is required for bit.ly'
            );
        }

        if (!isset($this->options['apiKey'])) {
            throw new Services_ShortURL_Exception_InvalidOptions(
                'An apiKey is required for bit.ly'
            );
        }
    }

    public function create($url)
    {
        $params = array(
            'version' => '2.0.1',
            'format'  => 'xml',
            'longUrl' => $url,
            'login'   => $this->options['login'],
            'apiKey'  => $this->options['apiKey']        
        );

        $sets = array();
        foreach ($params as $key => $val) {
            $sets[] = $key . '=' . $val;
        }

        $url = $this->api . '/shorten?' . implode('&', $sets);
        $xml = $this->sendRequest($url);
        return (string)$xml->results->nodeKeyVal->shortUrl;
    }

/*
    public function expand($url)
    {
        $params = array(
            'version'  => '2.0.1',
            'format'   => 'xml',
            'shortUrl' => $url,
            'login'    => $this->options['login'],
            'apiKey'   => $this->options['apiKey']        
        );

        $sets = array();
        foreach ($params as $key => $val) {
            $sets[] = $key . '=' . $val;
        }

        $url = $this->api . '/expand?' . implode('&', $sets);
        $xml = $this->sendRequest($url);
        return (string)$xml->results->wSMch->longUrl;
    }
*/

    private function sendRequest($url)
    {
        $this->req->setUrl($url);
        $this->req->setMethod('GET');

        $result = $this->req->send(); 
        if ($result->getStatus() != 200) {
            throw new Services_ShortURL_Exception_CouldNotExpand(
                'Non-300 code returned', $result->getStatus()
            );
        }

        $xml = @simplexml_load_string($result->getBody());
        if (!$xml instanceof SimpleXMLElement) {
            throw new Services_ShortURL_Exception_CouldNotCreate(
                'Could not parse API response'
            );
        }

        if ((int)$xml->errorCode > 0) {
            throw new Services_ShortURL_Exception_CouldNotCreate(
                (string)$xml->errorMessage, (int)$xml->errorCode
            );
        }

        return $xml;
    }
}

?>
