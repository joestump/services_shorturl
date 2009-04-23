<?php

require_once 'Services/ShortURL/Common.php';
require_once 'Services/ShortURL/Interface.php';
require_once 'Services/ShortURL/Exception/NotImplemented.php';
require_once 'Services/ShortURL/Exception/CouldNotCreate.php';
require_once 'Services/ShortURL/Exception/CouldNotExpand.php';
require_once 'Services/ShortURL/Exception/InvalidOptions.php';

class      Services_ShortURL_Digg
extends    Services_ShortURL_Common
implements Services_ShortURL_Interface
{
    protected $api = 'http://services.digg.com/url/short';

    public function __construct(array $options = array(), 
                                HTTP_Request2 $req = null) 
    {
        parent::__construct($options, $req);

        if (!isset($this->options['appkey'])) {
            throw new Services_ShortURL_Exception_InvalidOptions(
                'An appkey is required for Digg'
            );
        }
    }

    public function create($url)
    {
        $url = $this->api . '/create?appkey=' . 
               urlencode($this->options['appkey']) . '&url=' . 
               urlencode($url);

        $xml = $this->sendRequest($url);

        return (string)$xml->shorturl['short_url'];
    }

    public function expand($url)
    {
        $m = array();
        $regExp = '#http://digg.com/(?P<id>[du][0-9][a-zA-Z0-9]{1,6})?#';
        if (!preg_match($regExp, $url, $m)) {
            throw new Services_ShortURL_Exception_CouldNotExpand(
                $url . ' is not a valid Digg URL'
            );
        }

        $url = $this->api . '/' . $m['id'] . '?appkey=' . 
               urlencode($this->options['appkey']);

        $xml = $this->sendRequest($url);
        
        return (string)$xml->shorturl['link'];
    }

    private function sendRequest($url)
    {
        $this->req->setUrl($url);
        $this->req->setMethod('GET');

        $result = $this->req->send(); 
        if ($result->getStatus() != 200) {
            var_dump($result->getBody());
            throw new Services_ShortURL_Exception_CouldNotCreate(
                'Non-200 code returned', $result->getStatus()
            );
        }

        $xml = simplexml_load_string($result->getBody());
        if (!$xml instanceof SimpleXMLElement) {
            throw new Services_ShortURL_Exception_CouldNotCreate(
                'Could not parse API response'
            );
        }

        if (!isset($xml->shorturl) || !isset($xml->shorturl['short_url'])) {
            throw new Services_ShortURL_Exception_CouldNotCreate(
                'Bad response from Digg API'
            );
        }

        return $xml;
    }
}

?>
