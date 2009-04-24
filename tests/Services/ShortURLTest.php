<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Services/ShortURL.php';
require_once 'Validate.php';

class Services_ShortURLTest extends PHPUnit_Framework_TestCase
{
    protected $testURL = 'http://www.joestump.net/2009/02/25-things.html';

    protected $options = array(
        'TinyURL' => array(),
        'isgd'    => array(),
        'trim'    => array(),
        'shortie' => array(),
        'bitly'   => array(
            'login'  => 'servicesshorturl',
            'apiKey' => 'R_242f2503a1c7ff9d07aaa1835722c42f'
        ),
        'Digg'    => array(
            'appkey' => 'http://pear.php.net/package/Services_ShortURL'
        )
    );

    public function setUp()
    {
        foreach ($this->options as $service => $options) {
            Services_ShortURL::setServiceOptions($service, $options);
        }
    }

    /**
     * @dataProvider allServices
     */
    public function testCreateThenExpand($service)
    {
        $api   = Services_ShortURL::factory($service);

        // Create a short URL and do some sanity checking
        $small = $api->shorten($this->testURL);
        $this->assertType('string', $small);
        $this->assertTrue(Validate::uri($small), 'Invalid URL: ' . $small);

        // Expand the short URL and do some sanity checking
        $big = $api->expand($small);
        $this->assertEquals($this->testURL, $big);
    }

    /**
     * @dataProvider detectServices
     */
    public function testDetect($url, $service)
    {
        $api = Services_ShortURL::detect($url);
        $expected = 'Services_ShortURL_' . $service;
        $this->assertType($expected, $api);
        
        $big = $api->expand($url);
        $this->assertEquals($this->testURL, $big);
    }

    /**
     * @group Extract
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

    public function allServices()
    {
        return array(
            array('TinyURL'),
            array('isgd'),
            array('trim'),
            array('shortie'),
            array('bitly'),
            array('Digg')
        );
    }

    public function detectServices()
    {
        return array(
            array('http://tinyurl.com/ddef9j', 'TinyURL'),
            array('http://short.ie/40yi4x', 'shortie'),            
            array('http://is.gd/uaPi', 'isgd'),
            array('http://tr.im/jCBG', 'trim'),
            array('http://bit.ly/10qgu', 'bitly'),
            array('http://digg.com/d1kAa1', 'Digg')
        );
    }
}

?>
