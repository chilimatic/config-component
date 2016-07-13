<?php
/**
 * Created by JetBrains PhpStorm.
 * User: j
 * Date: 26.10.13
 * Time: 19:19
 * Node System like the JSDOM
 */

namespace chilimatic\lib\config;

use chilimatic\lib\Datastructure\Graph\INode;
use chilimatic\lib\Datastructure\Graph\Node as GraphNode;

/**
 * Class Node
 *
 * @package chilimatic\lib\config
 */
class Node extends GraphNode
{
    
    /**
     * Config Node if loaded
     * can be mixed since it should dynamic
     *
     * @var mixed
     */
    protected $comment = '';

    /**
     * constructor
     *
     * @param INode $parentNode
     * @param $key
     * @param $data
     * @param string $comment
     */
    public function __construct(INode $parentNode = null,string $key, $data,string $comment = '')
    {
        // get the current node
        $this->parentNode = $parentNode;
        // set the current key identifier
        $this->key = $key;
        // set the current value of the node
        $this->data = $data;


        if (!$this->parentNode || !$this->parentNode->key) {
            $this->id = (string)self::DEFAULT_KEY_DELIMITER . $key . self::DEFAULT_KEY_DELIMITER;
        } else {
            $this->id = (string)$this->parentNode->key . self::DEFAULT_KEY_DELIMITER . $key . self::DEFAULT_KEY_DELIMITER;
        }

        $this->id = preg_replace('/[#]{2,}/', self::DEFAULT_KEY_DELIMITER, $this->id);

        // optional comment
        $this->comment = $comment;
        $this->initChildren();
    }




    /**
     * @param string $comment
     */
    public function setComment(string $comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getComment() : string
    {
        return $this->comment;
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return (string) (is_scalar($this->getData()) ? $this->getData() : json_encode($this->getData()));
    }
}