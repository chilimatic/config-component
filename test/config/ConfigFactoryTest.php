<?php
use chilimatic\lib\Config\Adapter\File;
use chilimatic\lib\Config\ConfigFactory;

/**
 * Created by PhpStorm.
 * User: j
 * Date: 31.05.15
 * Time: 15:43
 */
class ConfigFactory_Test extends PHPUnit_Framework_TestCase
{
    private static $testDataDir;


    public static function setUpBeforeClass()
    {
        self::$testDataDir = realpath(__DIR__ . '/../data');
    }


    /**
     * @test
     */
    public function getFileConfig()
    {

        $c = ConfigFactory::make(
            'File',
            [
                File::CONFIG_PATH_INDEX => self::$testDataDir
            ]
        );

        self::assertInstanceOf('\chilimatic\lib\Config\Adapter\File', $c);
    }

    /**
     * @test
     */
    public function getIniConfig()
    {
        $c = ConfigFactory::make(
            'Ini',
            [
                'file' => self::$testDataDir . '/*.ini'
            ]
        );

        self::assertInstanceOf('\chilimatic\lib\Config\Adapter\Ini', $c);
    }

    /**
     * @test
     *
     * @expectedException \LogicException
     * @expectedExceptionMessage The Config Type has to be specified ... $type is empty
     */
    public function catchLogicExceptionNoType()
    {
        ConfigFactory::make(
            '',
            [
                File::CONFIG_PATH_INDEX => self::$testDataDir
            ]
        );
    }

    /**
     * @test
     *
     * @expectedException TypeError
     */
    public function catchTypeErrorWrongTypeParam()
    {
        ConfigFactory::make(
            null,
            [
                File::CONFIG_PATH_INDEX => self::$testDataDir
            ]
        );
    }

    /**
     * @test
     *
     * @expectedException \LogicException
     * @expectedExceptionMessage The Config class has to be implemented and accessible ... chilimatic\lib\Config\Adapter\Asfdasfd is not found
     */
    public function catchLogicExceptionClassDoesNotExist()
    {
        ConfigFactory::make(
            'asfdasfd',
            [
                File::CONFIG_PATH_INDEX => self::$testDataDir
            ]
        );
    }
}