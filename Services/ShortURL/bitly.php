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

require_once 'Services/ShortURL/Common.php';
require_once 'Services/ShortURL/Interface.php';
require_once 'Services/ShortURL/Exception/NotImplemented.php';
require_once 'Services/ShortURL/Exception/CouldNotCreate.php';
require_once 'Services/ShortURL/Exception/CouldNotExpand.php';
require_once 'Services/ShortURL/Exception/InvalidOptions.php';

/**
 * Interface for creating/expanding bit.ly links
 *
 * @category Services
 * @package  Services_ShortURL
 * @author   Joe Stump <joe@joestump.net>
 * @license  http://tinyurl.com/new-bsd New BSD License
 * @link     http://pear.php.net/package/Services_ShortURL
 * @link     http://bit.ly
 */
class      Services_ShortURL_bitly
extends    Services_ShortURL_Common
implements Services_ShortURL_Interface
{
    /**
     * API URL
     *
     * @var string $api The URL for the API
     */
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

    public function shorten($url)
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
