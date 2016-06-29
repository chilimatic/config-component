<?php

/**
 * Created by PhpStorm.
 * User: j
 * Date: 31.05.15
 * Time: 15:43
 */
class ConfigFactory_Test extends PHPUnit_Framework_TestCase
{

    public static function setUpBeforeClass()
    {
        touch(__DIR__ . '/*.cfg');
        touch(__DIR__ . '/*.test.cfg');
        touch(__DIR__ . '/test.ini');
    }

    public static function tearDownAfterClass()
    {
        unlink(__DIR__ . '/*.cfg');
        unlink(__DIR__ . '/*.test.cfg');
        unlink(__DIR__ . '/test.ini');
    }



    /**
     * @test
     */
    public function getFileConfig()
    {
        $c = \chilimatic\lib\Config\ConfigFactory::make(
            'File',
            [
                \chilimatic\lib\Config\File::CONFIG_PATH_INDEX => __DIR__
            ]
        );

        self::assertInstanceOf('\chilimatic\lib\Config\File', $c);
    }

    /**
     * @test
     */
    public function getIniConfig()
    {
        $c = \chilimatic\lib\Config\ConfigFactory::make(
            'Ini',
            [
                'file' => __DIR__ . '/test.ini'
            ]
        );

        self::assertInstanceOf('\chilimatic\lib\Config\Ini', $c);
    }

    /**
     * @test
     *
     * @expectedException \LogicException
     * @expectedExceptionMessage The Config Type has to be specified ... $type is empty
     */
    public function catchLogicExceptionNoType()
    {
        \chilimatic\lib\Config\ConfigFactory::make(
            '',
            [
                \chilimatic\lib\Config\File::CONFIG_PATH_INDEX => __DIR__
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
        \chilimatic\lib\Config\ConfigFactory::make(
            null,
            [
                \chilimatic\lib\Config\File::CONFIG_PATH_INDEX => __DIR__
            ]
        );
    }

    /**
     * @test
     *
     * @expectedException \LogicException
     * @expectedExceptionMessage The Config class has to be implemented and accessible ... chilimatic\lib\Config\Asfdasfd is not found
     */
    public function catchLogicExceptionClassDoesNotExist()
    {
        \chilimatic\lib\Config\ConfigFactory::make(
            'asfdasfd',
            [
                \chilimatic\lib\Config\File::CONFIG_PATH_INDEX => __DIR__
            ]
        );
    }
}