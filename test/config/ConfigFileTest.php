<?php

/**
 *
 * @author j
 * Date: 6/4/15
 * Time: 5:34 PM
 *
 * File: ConfigFileTest.php
 */
class ConfigFile_Test extends PHPUnit_Framework_TestCase
{
    /**
     * @var chilimatic\lib\Config\AbstractConfig
     */
    public static $config;

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

        self::$config = \chilimatic\lib\Config\ConfigFactory::make('File',
            [
                \chilimatic\lib\Config\File::CONFIG_PATH_INDEX => self::$testDataDir,
                'host_id'                                      => 'www.test.com'
            ]
        );
    }

    /**
     * @after
     */
    public function deleteConfigs()
    {
        unlink(self::$testDataDir . '/*.cfg');
        unlink(self::$testDataDir . '/*.test.com.cfg');
    }

    /**
     * @test
     */
    public function configFileInstanceTest()
    {
        self::assertInstanceOf('\chilimatic\lib\Config\File', self::$config);
    }

    /**
     * @test
     */
    public function getHirachicalValue1AsString()
    {
        self::assertEquals('test2', self::$config->get('value1'));
    }

    /**
     * @test
     */
    public function addConfigSet()
    {
        self::$config->set('value1', 12);
        self::assertEquals(12, self::$config->get('value1'));
    }

    /**
     * @test
     */
    public function addMultipleConfigSet()
    {
        self::$config->set('value1', 12);
        self::$config->set('value1', 13);
        self::assertEquals(13, self::$config->get('value1'));
    }

    /**
     * @test
     */
    public function deleteConfigNodesByKey()
    {
        self::$config->set('value1', 12);
        self::$config->set('value1', 13);

        self::$config->delete('value1');
        self::assertEquals(null, self::$config->get('value1'));
    }

    /**
     * @test
     */
    public function deleteConfigNodesById()
    {
        self::$config->set('value12', 12);
        self::$config->delete('value1');

        self::assertEquals(null, self::$config->get('value1'));
    }


}
