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

chdir(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

require_once dirname(__FILE__) . '/Services/ShortURLTest.php';
require_once 'PHPUnit/Framework/TestSuite.php';                                 

/**
 * The all tests suite file
 *
 * @category Services                                                        
 * @package  Services_ShortURL                                               
 * @author   Joe Stump <joe@joestump.net>                                    
 * @license  http://tinyurl.com/new-bsd New BSD License
 * @link     http://api.tr.im/website/api
 */             
class Services_ShortURL_AllTests
{
    /**
     * Create the suite
     *
     * @return object Instance of {@link PHPUnit_Framework_TestSuite}
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite();
        $suite->addTestSuite('Services_ShortURLTest');
        return $suite;
    }
}

?>
