<?php
declare(strict_types=1);

namespace chilimatic\lib\Config\Adapter;

use chilimatic\lib\Config\Engine\DataStructure\Node;
use chilimatic\lib\Config\Exception\ExceptionConfig;
use chilimatic\lib\Config\IConfig;
use chilimatic\lib\Config\Parser\ConfigFileParser;


/**
 * Class self
 *
 * @package chilimatic\lib\Config
 */
class File extends AbstractConfig
{
    /**
     * @var string
     */
    const CONFIG_PATH_INDEX = 'config_path';
    const HIERARCHY_PLACEHOLDER_INDEX = 'hierarchy_placeholder';
    const HOST_ID_KEY = 'host_id';


    const HIERARCHY_PLACEHOLDER = '*';

    /**
     * extension for config files
     *
     * @var string
     */
    CONST FILE_EXTENSION = 'cfg';


    /**
     * @var string
     */
    private $config_path;

    /**
     * @var Node
     */
    private $nodeTemplate;


    /**
     * @param array $param
     *
     * @throws ExceptionConfig
     * @throws \Exception
     */
    public function __construct($param = null)
    {
        $this->nodeTemplate = new Node(null, '', null);

        // set the main node on which all other nodes should be appended
        $this->mainNode = clone $this->nodeTemplate;;
        $this->mainNode->setKey(self::MAIN_NODE_KEY);

        // add custom parameters
        if (is_array($param)) {
            // set the given parameters
            foreach ((array) $param as $key => $value) {
                $node = clone $this->nodeTemplate;
                $node->setKey($key)
                    ->setData($value)
                    ->setComment(self::INIT_PARAMETER);

                $this->mainNode->addChild($node);
            }
        }

        if (!$this->get(self::HIERARCHY_PLACEHOLDER_INDEX)) {
            $this->set(self::HIERARCHY_PLACEHOLDER_INDEX, self::HIERARCHY_PLACEHOLDER);
        }

        // get the path of the config if the path has not been set
        if (!($this->config_path = $this->get(self::CONFIG_PATH_INDEX))) {
            throw new ExceptionConfig('no path for configfiles has been set');
        }

        $this->_initHostId();

        if ($param) {
            $this->load($param);
        } else {
            $this->load();
        }
    }


    /**
     * gets the current host id for the machine
     */
    private function _initHostId()
    {

        if ($this->get('host_id')) {
            return;
        }
        $node = clone $this->nodeTemplate;
        // if an apache is running use the http host of it
        if (!empty($_SERVER ['HTTP_HOST'])) {
            $node->setKey(self::HOST_ID_KEY)->setParent($this->mainNode)->setData($_SERVER ['HTTP_HOST']);
            $this->mainNode->addChild(
                $node
            );
        }
        else // else check if there are console parameters
        {
            // split them via spaces
            foreach ($GLOBALS ['argv'] as $param) {
                if (strpos($param, IConfig::CLI_COMMAND_DELIMITER) === false) {
                    continue;
                }
                // split the input into a key value pair
                $inp = (array)explode(IConfig::CLI_COMMAND_DELIMITER, $param);
                if (strtolower(trim($inp[0])) == IConfig::CLI_HOST_VARIABLE) {
                    $node
                        ->setKey(self::HOST_ID_KEY)
                        ->setParent($this->mainNode)
                        ->setData(trim((string)$inp[1]));
                    break;
                }
            }
            unset($inp, $param);
        }

        $this->checkHostId();
    }

    /**
     * @return void
     */
    private function checkHostId()
    {
        $host_id                = $this->get(self::HOST_ID_KEY);
        /**
         * if there's a specific port remove the port
         *
         * @todo keep in mind that maybe someone needs a port specific behaviour for his app
         */
        if (($pos = strpos((string) $host_id, ':')) !== false) {
            $node = clone $this->nodeTemplate;
            $node
                ->setKey(self::HOST_ID_KEY)
                ->setParent($this->mainNode)
                ->setData((string) substr($host_id, 0, $pos));
        }
    }


    /**
     * @return array
     */
    private function getConfigSet() : array
    {
        $configSet            = [];
        $hierarchy_placeholder  = $this->get(self::HIERARCHY_PLACEHOLDER_INDEX);

        // split up the server host_id to an array
        $id_part_list = (array) explode(self::CONFIG_DELIMITER, (string) $this->get('host_id'));
        if (count($id_part_list) < 3) {
            array_unshift($id_part_list, $hierarchy_placeholder);
        }

        // we don't need to rebuild this standard strings all the time
        $config_del = $hierarchy_placeholder . self::CONFIG_DELIMITER;
        $extension  = self::CONFIG_DELIMITER . self::FILE_EXTENSION;

        // the first config is the current host id + .cfg
        $self = (string)$this->config_path . '/' . (string) implode(self::CONFIG_DELIMITER, $id_part_list) . (string)$extension;


        // add an extra iteration so there is a specific config for a subdomain
        // and a generic one for all subdomains in this toplevel domain
        $count = (int)count($id_part_list) + 1;
        $i     = 0;
        do {
            // shift the first position of the array
            array_shift($id_part_list);

            // if the file exists add it to the "to be parsed list"
            if ($self && file_exists($self) && !in_array($self, $configSet, false)) {
                $configSet [] = (string) $self;
            }

            $file_name = (string)(count($id_part_list) > 0 ? implode(self::CONFIG_DELIMITER, $id_part_list) . (string) $extension : self::FILE_EXTENSION);
            $self      = (string)$this->config_path . '/' . (string)$config_del . $file_name;
            ++$i;
        } while ($i < $count);

        return $configSet;
    }

    /**
     * @param array $configSet
     * @param string $hierarchy_placeholder
     * @return mixed
     */
    private function sortConfigSet(array $configSet, string $hierarchy_placeholder) : array
    {
        if (0 === count($configSet)) {
            return $configSet;
        }

        /**
         * Config sort algorithm
         *
         * lambda function for sorting
         *
         * @param $a string
         * @param $b string
         *
         * @return int
         */
        uasort($configSet, function ($fileNameA, $fileNameB) use ($hierarchy_placeholder) {
            // include to the normal namespace

            if (substr_count($fileNameA, self::CONFIG_DELIMITER) === substr_count($fileNameB, self::CONFIG_DELIMITER)) {
                if (strpos($fileNameA, $hierarchy_placeholder) !== false && strpos($fileNameB, $hierarchy_placeholder) === false) {
                    return -1;
                } elseif (strpos($fileNameA, $hierarchy_placeholder) === false && strpos($fileNameB, $hierarchy_placeholder) !== false) {
                    return 1;
                }

                return 0;
            }

            return (substr_count($fileNameA, self::CONFIG_DELIMITER) > substr_count($fileNameB, self::CONFIG_DELIMITER) ? 1 : -1);
        });

        return $configSet;
    }

    /**
     * gets the needed config files based on the
     * url or parameters given by the console
     *
     * @return array
     */
    protected function _getConfigSet() : array
    {

        if (empty($_SERVER) && empty($GLOBALS['argv'])) {
            return [];
        }

        return $this->sortConfigSet(
            $this->getConfigSet(),
            $this->get(self::HIERARCHY_PLACEHOLDER_INDEX)
        );

    }


    /**
     * loads the config settings
     *
     * @param null $param
     *
     * @return Node|null
     * @throws ExceptionConfig
     */
    public function load($param = null)
    {

        $configSet = $this->_getConfigSet();

        if (0 === count($configSet)) {
            // set default config set for the default execution
            $configSet = [
                realpath(
                    "{$this->config_path}/{$this->get(self::HIERARCHY_PLACEHOLDER_INDEX)}"
                    . (string) self::CONFIG_DELIMITER
                    . (string) self::FILE_EXTENSION
                )
            ];
            $this->set('config_set', $configSet);
        }

        return $this->populateEngine(
            $this->checkConfigForDefaultConfig($configSet)
        );
    }

    /**
     * @param array $configSet
     * @throws ExceptionConfig
     * @return array
     */
    private function checkConfigForDefaultConfig(array $configSet) : array
    {
        if (!file_exists($configSet[0])) {
            throw new ExceptionConfig(
                "No default config file declared {$this->config_path}/{$this->get(self::HIERARCHY_PLACEHOLDER_INDEX)}"
                . (string) self::CONFIG_DELIMITER
                . (string) self::FILE_EXTENSION
            );
        }

        return $configSet;
    }


    /**
     * @param array $configSet
     * @return Node|null
     */
    public function populateEngine(array $configSet)
    {
        if (0 === count($configSet)) {
            return null;
        }

        // first insert point;
        foreach ($configSet as $config) {
            if (!$config || !is_string($config)) {
                continue;
            }

            /**
             * get the key for the config node
             */
            $key = explode('/', $config);
            $key = substr(array_pop($key), 0, -4);

            $node = clone $this->nodeTemplate;
            $node
                ->setParent($this->mainNode)
                ->setKey($key)
                ->setData($config)
                ->setComment('self');


            $this->lastNewNode = $node;
            // add the config node
            $this->mainNode->addChild($node);
            ConfigFileParser::appendToNode(
                $node,
                ConfigFileParser::parse(
                    $this->getConfigFileContent(
                        $config
                    )
                )
            );
        }

        return $this->mainNode;
    }


    /**
     * reads the specific config file
     *
     * @param string $configPath
     *
     * @return array
     */
    private function getConfigFileContent(string $configPath) : array
    {
        // if empty just skip it
        if (!filesize($configPath)) {
            return [];
        }

        // read the file handler
        $config = file_get_contents($configPath);

        // check for linebreaks
        if (strpos($config, "\n") === false) {
            $config = [
                $config
            ];
        } else {
            $config = (array) explode("\n", $config);
        }

        return $config;
    }

    /**
     * @param Node $node
     *
     * @return bool
     */
    public function saveConfig(Node $node = null) : bool
    {
        if (null === $node) {
            return $this->saveNode();
        }

        return true;
    }

    /**
     * @return bool
     */
    public function saveNode()
    {
        return true;
    }
}