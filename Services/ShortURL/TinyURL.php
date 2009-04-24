<?php

require_once 'Services/ShortURL/Common.php';
require_once 'Services/ShortURL/Interface.php';
require_once 'Services/ShortURL/Exception/NotImplemented.php';
require_once 'Services/ShortURL/Exception/CouldNotCreate.php';
require_once 'Services/ShortURL/Exception/CouldNotExpand.php';

class      Services_ShortURL_TinyURL 
extends    Services_ShortURL_Common
implements Services_ShortURL_Interface
{
    protected $api = 'http://tinyurl.com/api-create.php';

    public function shorten($url)
    {
        $url = $this->api . '?url=' . $url;
        $this->req->setUrl($url);
        $this->req->setMethod('GET');

        $result = $this->req->send();
        if ($result->getStatus() != 200) {
            throw new Services_ShortURL_Exception_CouldNotCreate();            
        }

        return trim($result->getBody());
    }
}

?>
