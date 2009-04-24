<?php

require_once 'Services/ShortURL/Common.php';
require_once 'Services/ShortURL/Interface.php';
require_once 'Services/ShortURL/Exception/NotImplemented.php';
require_once 'Services/ShortURL/Exception/CouldNotCreate.php';
require_once 'Services/ShortURL/Exception/CouldNotExpand.php';
require_once 'Services/ShortURL/Exception/InvalidOptions.php';

class      Services_ShortURL_shortie
extends    Services_ShortURL_Common
implements Services_ShortURL_Interface
{
    private $api = 'http://short.ie/api';

    public function shorten($url)
    {
        $params = array(
            'format' => 'xml',
            'url'    => $url
        );

        $sets = array();
        foreach ($params as $key => $val) {
            $sets[] = $key . '=' . $val;
        }

        $url = $this->api . '?' . implode('&', $sets);
        $xml = $this->sendRequest($url);
        return (string)$xml->shortened;
    }

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

        return $xml;
    }
}

?>
