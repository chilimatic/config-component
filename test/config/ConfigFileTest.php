<?php
use chilimatic\lib\Config\Adapter\File;
use chilimatic\lib\Config\ConfigFactory;


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
     * @var chilimatic\lib\Config\Adapter\AbstractConfig
     */
    public static $config;

    /**
     * @var string
     */
    private static $testDataDir;


    public static function setUpBeforeClass()
    {
        self::$testDataDir = realpath(__DIR__ . '/../data');
        try {
            self::$config = ConfigFactory::make(
                'File',
                [
                    File::CONFIG_PATH_INDEX => self::$testDataDir,
                    File::HOST_ID_KEY       => 'www.example.com'
                ]
            );
        } catch (\Throwable $t) {

        }

    }

    /**
     * @test
     */
    public function configFileInstanceTest()
    {
        self::assertInstanceOf('\chilimatic\lib\Config\Adapter\File', self::$config);
    }

    /**
     * @test
     */
    public function getHierarchicalValue1AsString()
    {
        self::assertEquals('memcached', self::$config->get('cache_type'));
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
