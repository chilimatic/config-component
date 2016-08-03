<?php
/**
 *
 * @author j
 * Date: 5/12/15
 * Time: 4:53 PM
 *
 * File: ConfigFactory.php
 */

namespace chilimatic\lib\Config;
use chilimatic\lib\Config\Adapter\AbstractConfig;

/**
 * Class ConfigFactory
 *
 * @package chilimatic\lib\Config
 */
class ConfigFactory
{

    const ADAPTER_NAMESPACE = __NAMESPACE__ . '\\Adapter\\';

    /**
     * Factory for creating Config objects
     *
     * the type specifies the Config Type like 'ini' or 'file' or another one implemented lateron
     *
     * @param string $type
     * @param array $param
     *
     * @throws \LogicException
     *
     * @return AbstractConfig
     */
    public static function make(string $type, $param = null) : AbstractConfig
    {
        if (!$type) {
            throw new \LogicException("The Config Type has to be specified ... \$type is empty");
        }



        $className = self::ADAPTER_NAMESPACE . (string)ucfirst($type);

        if (!class_exists($className, true)) {
            throw new \LogicException("The Config class has to be implemented and accessible ... $className is not found");
        }

        return new $className($param);
    }
}