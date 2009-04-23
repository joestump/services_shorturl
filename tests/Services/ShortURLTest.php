<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Services/ShortURL.php';
require_once 'Validate.php';

class Services_ShortURLTest extends PHPUnit_Framework_TestCase
{
    protected $testURL = 'http://www.joestump.net/2009/02/25-things.html';

    /**
     * @dataProvider allServices
     */
    public function testCreateThenExpand($service, $options)
    {
        $api   = Services_ShortURL::factory($service, $options);

        // Create a short URL and do some sanity checking
        $small = $api->create($this->testURL);
        $this->assertType('string', $small);
        $this->assertTrue(Validate::uri($small), 'Invalid URL: ' . $small);

        // Expand the short URL and do some sanity checking
        $big = $api->expand($small);
        $this->assertEquals($this->testURL, $big);
    }

    public function allServices()
    {
        return array(
            array('TinyURL', array()),
            array('isgd', array()),
            array('trim', array()),
            array('bitly', array(
                'login'  => 'servicesshorturl',
                'apiKey' => 'R_242f2503a1c7ff9d07aaa1835722c42f'
            )),
            array('Digg', array(
                'appkey' => 'http://pear.php.net/package/Services_ShortURL'
            ))
        );
    }
}

?>
