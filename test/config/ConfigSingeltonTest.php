<?php
use chilimatic\lib\Config\Config;

/**
 *
 * @author j
 * Date: 7/1/15
 * Time: 9:19 PM
 *
 * File: ConfigSingeltonTest.php
 */

class ConfigSingelton_Test extends PHPUnit_Framework_TestCase
{

    /**
     * @var string
     */
    private static $testDataDir;


    public static function setUpBeforeClass()
    {
        self::$testDataDir = __DIR__ ;
        $data = "value1=test\nvalue2=\"test\"\nvalue3='test'\nvalue4=123\nvalue5=12.23\nvalue6={\"test\":123}\nvalue7=a:1:{i:23;i:12;}";
        file_put_contents(self::$testDataDir . '/*.cfg', $data);
        $data2 = "value1=test2\nvalue7=teststring";
        file_put_contents(self::$testDataDir . '/*.test.com.cfg', $data2);
    }

    public static function tearDownAfterClass()
    {
        unlink(self::$testDataDir . '/*.cfg');
        unlink(self::$testDataDir . '/*.test.com.cfg');
    }

    /**
     * @test
     *
     * @expectedException \LogicException
     * @expectedExceptionMessage Config Type was not specified in the param array $param['type']
     */
    public function checkMissingTypeException(){
        Config::getInstance();
    }

    /**
     * @test
     *
     * @expectedException chilimatic\lib\Config\ExceptionConfig
     * @expectedExceptionMessage No config file was given, please make sure one is provided in the param array
     */
    public function checkMissingException(){
       Config::getInstance(
            [
                'type' => 'Ini'
            ]
        );
    }

    /**
     * @test
     */
    public function checkGetSingeltonInstance(){
        $config = Config::getInstance(
            [
                'type' => 'File',
                \chilimatic\lib\Config\File::CONFIG_PATH_INDEX => self::$testDataDir
            ]
        );

        $config2 = Config::getInstance();

        self::assertEquals($config, $config2);
    }


    /**
     * @test
     */
    public function checkSetParam() {
        Config::getInstance(
            [
                'type' => 'File',
                \chilimatic\lib\Config\File::CONFIG_PATH_INDEX => self::$testDataDir
            ]
        );

        Config::set('test', 12);


        self::assertEquals(12, Config::get('test'));
    }

    /**
     * @test
     */
    public function checkGetParam() {
        Config::getInstance(
            [
                'type' => 'File',
                \chilimatic\lib\Config\File::CONFIG_PATH_INDEX => self::$testDataDir
            ]
        );

        self::assertEquals('test', Config::get('value1'));
    }
}