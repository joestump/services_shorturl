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
 * @category    Services
 * @package     Services_ShortURL
 * @author      Joe Stump <joe@joestump.net> 
 * @copyright   Joe Stump <joe@joestump.net> 
 * @license     http://www.opensource.org/licenses/bsd-license.php 
 * @version     CVS: $Id:$
 * @link        http://pear.php.net/package/Services_ShortURL
 * @link        http://github.com/joestump/services_shorturl
 */     

require_once 'Services/ShortURL/Common.php';
require_once 'Services/ShortURL/Interface.php';
require_once 'Services/ShortURL/Exception/NotImplemented.php';
require_once 'Services/ShortURL/Exception/CouldNotCreate.php';
require_once 'Services/ShortURL/Exception/CouldNotExpand.php';

/**
 * Interface for creating/expanding is.gd links
 *
 * @category    Services
 * @package     Services_ShortURL
 * @author      Joe Stump <joe@joestump.net> 
 * @link        http://is.gd/api_info.php
 */
class      Services_ShortURL_isgd 
extends    Services_ShortURL_Common
implements Services_ShortURL_Interface
{
    protected $api = 'http://is.gd/api.php'; 

    public function shorten($url)
    {
        $url = $this->api . '?longurl=' . $url;        
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
