<?php
declare(strict_types=1);

namespace chilimatic\lib\Config\Adapter;
use chilimatic\lib\Config\Engine\DataStructure\Node;
use chilimatic\lib\Config\IConfig;

/**
 * Class AbstractConfig
 * @package chilimatic\lib\Config
 */
abstract class AbstractConfig implements IConfig
{

    /**
     * comment within the nodes that it's a given parameter
     * through the constructor
     *
     * @var string
     */
    const INIT_PARAMETER = 'init-param';

    /**
     * main config node
     *
     * @var Node
     */
    public $mainNode;


    /**
     * get the last use node [insert/delete/update .... and so on]
     *
     * @var Node|null
     */
    public $lastNewNode;

    /**
     * constructor
     *
     * @param mixed $param
     */
    public function __construct($param = null)
    {
        // set the main node on which all other nodes should be appended
        $this->mainNode = new Node(null, IConfig::MAIN_NODE_KEY, null);

        // add custom parameters
        if ($param && is_array($param)) {
            // set the given parameters
            foreach ((array) $param as $key => $value) {
                $node = new Node($this->mainNode, $key, $value, self::INIT_PARAMETER);
                $this->mainNode->addChild($node);
            }
        }

        $this->load($param);
    }


    /**
     * loads the config based on the type / source
     *
     * @return mixed
     */
    abstract public function load($param = null);

    /**
     * deletes a config
     *
     * @param string $key
     *
     * @return mixed
     */
    public function delete(string $key = '') : bool
    {
        $nodeList = $this->mainNode->getByKey($key);
        if (empty($nodeList)) {
            true;
        }

        foreach ($nodeList as $node) {
            $node->delete();
        }

        unset($node);

        return true;
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function deleteById(string $id = '') : bool
    {
        $node = $this->mainNode->getById($id);

        if (empty($node)) {
            true;
        }

        return ($node->delete() ? true : false);
    }

    /**
     * gets a specific parameter
     *
     * @param $var
     *
     * @return mixed
     */
    public function get(string $var)
    {
        if (!$this->mainNode) {
            return null;
        }

        $node = $this->mainNode->getLastByKey($var);
        if ($node === null) {
            return null;
        }

        return $node->getData();
    }

    /**
     * gets a specific parameter
     *
     * @param $id
     *
     * @internal param $var
     * @return mixed
     */
    public function getById(string $id)
    {
        $node = $this->mainNode->getById($id);
        if ($node === null) {
            return null;
        }

        return $node->getData();
    }

    /**
     * sets a specific parameter
     *
     * @param $id
     * @param $val
     *
     * @return mixed
     */
    public function setById(string $id, $val)
    {
        // set the variable
        if (empty($id)) {
            return $this;
        }

        $node = new Node($this->mainNode, $id, $val);

        $this->mainNode->addChild($node);

        return $this;

    }

    /**
     * sets a specific parameter
     *
     * @param $key
     * @param $val
     *
     * @return mixed
     */
    public function set(string $key, $val)
    {
        // set the variable
        if (empty($key)) {
            return $this;
        }

        $node = $this->mainNode->getLastByKey($key);

        if (!$node || !$node->getParent()) {
            $newNode = new Node($this->mainNode, $key, $val);
            $this->mainNode->addChild($newNode);
        } else {
            $newNode = new Node($node->getParent(), $key, $val);
            $node->getParent()->addChild($newNode);
        }

        $this->lastNewNode = $newNode;

        return $this;

    }

    /**
     * saves the specified config
     *
     * @param Node $node
     *
     * @internal param $array ;
     *
     * @return mixed
     */
    abstract public function saveConfig(Node $node = null);
}