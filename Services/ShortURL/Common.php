<?php

require_once 'HTTP/Request2.php';
require_once 'Services/ShortURL/Exception.php';

abstract class Services_ShortURL_Common
{
    protected $options = array();
    protected $req = null;

    public function __construct(array $options = array(), 
                                HTTP_Request2 $req = null) 
    {
        if ($req !== null) {
            $this->accept($req);
        } else {
            $this->req = new HTTP_Request2();
            $this->req->setAdapter('Curl');
            $this->req->setHeader(
                'User-Agent', get_class($this) . ' @version@'
            );
        }

        $this->options = $options;
    }

    public function accept($object)
    {
        if (!is_object($object)) {
            throw new InvalidArgumentException("Can't accept non-objects.");
        }

        if ($object instanceof HTTP_Request2) {
            $this->req = $object;
        } else {
            throw new InvalidArgumentException(
                "Can't accept object of type " . get_class($object)
            );
        }
    }

    public function expand($url)
    {
        $this->req->setUrl($url);
        $this->req->setMethod('GET');
        $result = $this->req->send();

        if (intval(substr($result->getStatus(), 0, 1)) != 3) {
            throw new Services_ShortURL_Exception_CouldNotExpand(
                'Non-300 code returned', $result->getStatus()
            );
        }

        return trim($result->getHeader('Location'));       
    }

    /**
     * Fetch information about the short URL
     *
     * @param string $url The short URL to fetch information for
     *
     * @throws {@link Services_ShortURL_Exception_NotImplemented}
     * @return mixed
     */
    public function stats($url)
    {
        throw new Services_ShortURL_Exception_NotImplemented(
            'Stats is not implemented for ' . get_class($this)
        );
    }

    /**
     * Fetch information about the short URL
     *
     * @param string $url The short URL to fetch information for
     *
     * @throws {@link Services_ShortURL_Exception_NotImplemented}
     * @return mixed
     */
    public function info($url)
    {
        throw new Services_ShortURL_Exception_NotImplemented(
            'Info is not implemented for ' . get_class($this)
        );
    }
}

?>
