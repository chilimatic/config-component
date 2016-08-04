<?php
/**
 * Created by JetBrains PhpStorm.
 * User: j
 * Date: 26.10.13
 * Time: 17:53
 *
 */

namespace chilimatic\lib\Config;

use chilimatic\lib\Config\Adapter\AbstractConfig;
use chilimatic\lib\Config\Engine\DataStructure\Node;
use chilimatic\lib\Interfaces\ISingelton;

/**
 * Class Config
 *
 * @package chilimatic\lib\Config
 */
class Config implements ISingelton
{
    /**
     * Default config as a fallback
     */
    const DEFAULT_CONFIG_TYPE = 'File';

    /**
     * singelton instance check
     *
     * @var object
     */
    public static $instance = null;

    /**
     * Config constructor.
     */
    protected function __construct(){}


    /**
     * singelton constructor
     *
     * the $param['type'] is for the factory to create the correct Config
     *
     * @param array $param
     *
     * @return AbstractConfig
     */
    public static function getInstance($param = null)
    {
        if (self::$instance === null) {
            if (isset($param['type'])) {
                $type = $param['type'];
                unset($param['type']);
            } else {
                throw new \LogicException('Config Type was not specified in the param array $param[\'type\']');
            }

            self::$instance = ConfigFactory::make($type, $param);
        }

        // return singelton instance
        return self::$instance;
    }

    /**
     * get wrapper
     *
     * @param string $var
     *
     * @return mixed
     */
    public static function get(string $var)
    {
        return self::$instance->get($var);
    }

    /**
     * gets a specific param based on the id
     *
     * @param string $id
     *
     * @return mixed
     */
    public static function getById(string $id)
    {
        return self::$instance->getById($id);
    }

    /**
     * set wrapper
     *
     * @param string $key
     * @param $value
     *
     * @return mixed
     */
    public static function set(string $key, $value)
    {
        return self::$instance->set($key, $value);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public static function delete(string $key)
    {
        return self::$instance->delete($key);
    }

    /**
     * load module wrapper
     *
     * @param string $module_name
     *
     * @return mixed
     */
    public static function loadModule(string $module_name = '')
    {
        return self::$instance->loadModule($module_name);
    }

    /**
     * save config wrapper
     *
     * @param Node $node
     */
    public function saveConfig(Node $node = null)
    {
        self::$instance->saveConfig($node);
    }
}