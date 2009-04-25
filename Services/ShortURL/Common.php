<?php

/**
 * An abstract interface for dealing with short URL services
 *
 * PHP version 5.2.0+
 *
 * LICENSE: This source file is subject to the New BSD license that is          
 * available through the world-wide-web at the following URI:
 * http://www.opensource.org/licenses/bsd-license.php. If you did not receive  
 * a copy of the New BSD License and are unable to obtain it through the web, 
 * please send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  Services
 * @package   Services_ShortURL
 * @author    Joe Stump <joe@joestump.net> 
 * @copyright 2009 Joe Stump <joe@joestump.net> 
 * @license   http://tinyurl.com/new-bsd New BSD License
 * @version   CVS: $Id:$
 * @link      http://pear.php.net/package/Services_ShortURL
 * @link      http://github.com/joestump/services_shorturl
 */

require_once 'HTTP/Request2.php';
require_once 'Services/ShortURL/Exception.php';

/**
 * A common class for all short URL drivers
 *
 * @category    Services
 * @package     Services_ShortURL
 * @author      Joe Stump <joe@joestump.net> 
 */
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
