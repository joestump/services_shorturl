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

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Services/ShortURL.php';
require_once 'Validate.php';

/**
 * Basic test cases that shorten/expand 
 *
 * @category Services
 * @package  Services_ShortURL
 * @author   Joe Stump <joe@joestump.net>
 * @license  http://tinyurl.com/new-bsd New BSD License
 * @link     http://api.tr.im/website/api
 */             
class Services_ShortURLTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test URL
     *
     * @var string $testURL A test URL for expand/shorten
     */
    protected $testURL = 'http://www.joestump.net/2009/02/25-things.html';

    /**
     * Options for services
     *
     * WARNING: Please do not abuse any credentials you see in here as we might
     * get banned from APIs for doing so.
     *
     * @var array $options Options array
     * @see Services_ShortURLTest::setUp()
     */
    protected $options = array(
        'TinyURL' => array(),
        'Isgd'    => array(),
        'Trim'    => array(),
        'Shortie' => array(),
        'Bitly'   => array(
            'login'  => 'servicesshorturl',
            'apiKey' => 'R_242f2503a1c7ff9d07aaa1835722c42f'
        ),
        'Digg'    => array(
            'appkey' => 'http://pear.php.net/package/Services_ShortURL/@version@'
        )
    );

    /**
     * Set up the test
     *
     * @return void
     */
    public function setUp()
    {
        foreach ($this->options as $service => $options) {
            Services_ShortURL::setServiceOptions($service, $options);
        }
    }

    /**
     * Test creating and then expanding a URL
     *
     * @param string $service The service to test
     *
     * @dataProvider allServices
     * @return void
     */
    public function testCreateThenExpand($service)
    {
        $api = Services_ShortURL::factory($service);

        // Create a short URL and do some sanity checking
        $small = $api->shorten($this->testURL);
        $this->assertType('string', $small);
        $this->assertTrue(Validate::uri($small), 'Invalid URL: ' . $small);

        // Expand the short URL and do some sanity checking
        $big = $api->expand($small);
        $this->assertEquals($this->testURL, $big);
    }

    /**
     * Test detecting services from URLs
     *
     * @param string $url     The URL to detect a service for
     * @param string $service The service expected to be detected
     *
     * @dataProvider detectServices
     * @return void
     */
    public function testDetect($url, $service)
    {
        $api      = Services_ShortURL::detect($url);
        $expected = 'Services_ShortURL_' . $service;
        $this->assertType($expected, $api);
        
        $big = $api->expand($url);
        $this->assertEquals($this->testURL, $big);
    }

    /**
     * Test extraction function
     *
     * @see Services_ShortURLTest::detectServices()
     * @return void
     */
    public function testExtract()
    {
        $tmp  = $this->detectServices();
        $urls = array();
        foreach ($tmp as $arr) {
            $urls[] = $arr[0];
        }

        $string = implode(' ', $urls);
        $result = Services_ShortURL::extract($string);

        $this->assertEquals(count($urls), 
                            count($result), 
                            "Number of URLs extracted do not match.");

        foreach ($result as $short => $long) {
            $this->assertEquals($this->testURL, $long);
        }
    }

    /**
     * Return all services
     *
     * @return array List of services
     */
    public function allServices()
    {
        return array(
            array('TinyURL'),
            array('Isgd'),
            array('Trim'),
            array('Shortie'),
            array('Bitly'),
            array('Digg')
        );
    }

    /**
     * Return example URLs + service name
     * 
     * @return array List of services with URLs
     */
    public function detectServices()
    {
        return array(
            array('http://tinyurl.com/ddef9j', 'TinyURL'),
            array('http://short.ie/40yi4x', 'Shortie'),
            array('http://is.gd/uaPi', 'Isgd'),
            array('http://tr.im/jCBG', 'Trim'),
            array('http://bit.ly/10qgu', 'Bitly'),
            array('http://digg.com/d1kAa1', 'Digg')
        );
    }
}

?>
