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
require_once 'Services/ShortURL/Exception/CouldNotShorten.php';
require_once 'Services/ShortURL/Exception/CouldNotExpand.php';

/**
 * Interface for creating/expanding piep.net links
 *
 * @category Services
 * @package  Services_ShortURL
 * @author   Dominik Deobald <dominik.deobald@interdose.com>
 * @license  http://tinyurl.com/new-bsd New BSD License
 * @link     http://pear.php.net/package/Services_ShortURL
 * @link     http://piep.net/
 */
class      Services_ShortURL_PiepNet
extends    Services_ShortURL_Common
implements Services_ShortURL_Interface
{
    /**
     * piep.net's API URL
     *
     * @var string $api The location of piep.net's API
     */
    protected $api = 'http://api.interdose.com/api/shorturl/v1/shorten';
	protected $defaultOptions = array(
		'service' => 'piep.net'
	);

    /**
     * Shorten a link using piep.net
     *
     * @param string $url The URL to shorten
     *
     * @throws {@link Services_ShortURL_Exception_CouldNotShorten}
     * @return string
     */
    public function shorten($url)
    {
		$myOptions = array_merge($this->defaultOptions, $this->options);
        $url = $this->api . '?url=' . urlencode($url);
		
		foreach (array('service', 'user', 'pass', 'apikey') as $v) {
			if (strlen($myOptions[$v]) > 0) {
				$url .= '&' . $v . '=' . urlencode($myOptions[$v]);
			}
		}

        $this->req->setUrl($url);
        $this->req->setMethod('GET');

        $result = $this->req->send();
        if ($result->getStatus() != 200) {
            throw new Services_ShortURL_Exception_CouldNotShorten(
                'Non-200 code returned', $result->getStatus()
            );
        }

        return trim($result->getBody());
    }
}

?>
