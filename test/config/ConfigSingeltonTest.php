<?php
use chilimatic\lib\Config\Adapter\File;
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
        self::$testDataDir = realpath(__DIR__ . '/../data');
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
     * @expectedException chilimatic\lib\Config\Exception\ExceptionConfig
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
                File::CONFIG_PATH_INDEX => self::$testDataDir
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
                File::CONFIG_PATH_INDEX => self::$testDataDir
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
                File::CONFIG_PATH_INDEX => self::$testDataDir
            ]
        );

        self::assertEquals('memcached', Config::get('cache_type'));
    }
}