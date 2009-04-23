<?php


chdir(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

require_once dirname(__FILE__) . '/Services/ShortURLTest.php';
require_once 'PHPUnit/Framework/TestSuite.php';                                 

class Services_ShortURL_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite();
        $suite->addTestSuite('Services_ShortURLTest');
        return $suite;
    }
}

?>
