<?php
declare(strict_types=1);
namespace chilimatic\lib\Config\Parser;
use chilimatic\lib\Config\Engine\DataStructure\Node;

/**
 * Class ConfigFileParser
 *
 * @package chilimatic\lib\Config\Parser
 */
class ConfigFileParser
{

    /**
     * default list of single line comment characters
     * separated by ,
     * -> list is exploded in the constructor
     *
     * @var array
     */
    const COMMENT_CHARACTER_LIST = ['#','//'];

    const DATA_SET_INDEX = 'data';
    const KEY_SET_INDEX = 'key';
    const COMMENT_SET_INDEX = 'comment';


    const PATTERN_KEY_VALUE_MATCH = '/^[\s]*([a-zA-Z\_\-\.]+)?[\s]*[=][\s]*(.*)$/';
    const PATTERN_SERIALIZE = '/^(O:\d+|a:\d+|i:\d+|s:\d+|b:\d+|d:\d+)/';
    const PATTERN_STRIP_QUOTES = '/^["|\']{1}(.*)["|\']{1}$/';

    private static $nodeTemplate;

    /**
     * checks if it's a comment in the config
     *
     * @param $line
     *
     * @return bool
     */
    private static function isComment($line) : bool
    {
        // if it's an empty line you might as well skip it
        if (empty($line)) {
            return true;
        }

        return (bool) preg_match('/^[\s]*([\/]{2}|#{1})/i', $line);
    }

    /**
     * @param array $currentConfig
     *
     * @return \SplQueue
     */
    public static function parse(array $currentConfig) : \SplQueue
    {
        $currentComment = '';

        $queue = new \SplQueue();

        // loop through all lines
        for ($i = 0, $count = (int)count($currentConfig); $i < $count; $i++) {
            if (!$currentConfig[$i]) {
                continue;
            } elseif (self::isComment($currentConfig[$i])) {
                $currentComment .= $currentConfig[$i];
                continue;
            }
            $keyValueMatch = [];

            if (strpos($currentConfig[$i], "=") === false) {
                continue;
            }

            preg_match(
                self::PATTERN_KEY_VALUE_MATCH,
                $currentConfig[$i],
                $keyValueMatch
            );


            if (0 !== count($keyValueMatch)) {
                $queue->enqueue(
                    [
                        self::KEY_SET_INDEX     => $keyValueMatch[1],
                        self::DATA_SET_INDEX    => $keyValueMatch[2],
                        self::COMMENT_SET_INDEX => $currentComment
                    ]
                );
                // clear the comment
                $currentComment = '';
            }
        }

        return $queue;
    }

    /**
     * method to set the current type and initializes it
     *
     * @param $data
     *
     * @return mixed
     */
    private static function initType($data)
    {
        if (!is_string($data)) {
            return false;
        }

        $data = trim($data);
        switch (true) {
            case (in_array($data, ['true', 'false'], false)):
                return (bool)(strpos($data, 'true') !== false) ? true : false;
            case !is_numeric($data):
                if ($res = json_decode($data)) {
                    return $res;
                } else if (preg_match(self::PATTERN_SERIALIZE, $data) &&  ($res = @unserialize($data)) !== false) {
                    return $res;
                } else if ((preg_match(self::PATTERN_STRIP_QUOTES, $data, $match)) === 1) {
                    return (string) $match[1];
                } else {
                    return (string)$data;
                }
        }

        if (is_numeric($data) && strpos($data, '.') === false) {
            return (int)$data;
        } else {
            return (float)$data;
        }
    }


    /**
     * @param Node $node
     * @param \SplQueue $queue
     * @return Node
     */
    public static function appendToNode(Node $node, \SplQueue $queue) : Node
    {
        if (!self::$nodeTemplate) {
            self::$nodeTemplate = new Node(null, '', null);
        }

        foreach ($queue as $set) {
            $childNode = clone self::$nodeTemplate;
            $childNode
                ->setKey($set['key'])
                ->setData(self::initType($set['data']))
                ->setParent($node)
                ->setComment($set['comment'])
            ;
            $node->addChild($childNode);
        }

        return $node;
    }

}