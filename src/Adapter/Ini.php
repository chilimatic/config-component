<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 02.11.13
 * Time: 12:36
 */

namespace chilimatic\lib\Config\Adapter;
use chilimatic\lib\Config\Engine\DataStructure\Node;
use chilimatic\lib\Config\Exception\ExceptionConfig;
use chilimatic\lib\Config\IConfig;


/**
 * Class Config_Ini
 *
 * @package chilimatic\lib\Config
 */
class Ini extends AbstractConfig
{

    const FILE_INDEX = 'file';
    const PROCESS_SECTION_INDEX = 'process-sections';
    const SCANNER_MODE_INDEX = 'scanner-mode';

    /**
     * name of the config file
     *
     * @var array
     */
    public $configFileSet = [];

    /**
     * scanner mode
     *
     * @var int
     */
    public $scannerMode = null;

    /**
     * process sections
     *
     * @var bool
     */
    public $processSections = null;

    /**
     * @param null $param
     *
     * @throws ExceptionConfig
     * @throws \Exception
     *
     * @return void
     */
    public function load($param = null)
    {
        try {
            if (empty($param[self::FILE_INDEX])) {
                throw new ExceptionConfig(_('No config file was given, please make sure one is provided in the param array'), 0, 1, __METHOD__, __LINE__);
            }

            if (is_dir($param[self::FILE_INDEX])) {
                $path = $param[self::FILE_INDEX];
                $this->configFileSet = array_map(
                    function($fileName) use ($path){
                        return $path . DIRECTORY_SEPARATOR . $fileName;
                    },
                    array_filter(
                        scandir($param[self::FILE_INDEX]),
                        function($file)  {
                            if (strpos($file, '.ini') !== false) {
                                return true;

                            }
                            return false;
                        }
                    )
                );
            } else {
                $this->configFileSet[] = $param[self::FILE_INDEX];
            }


            if (isset($param[self::PROCESS_SECTION_INDEX])) {
                $this->processSections = (bool)$param[self::PROCESS_SECTION_INDEX];
            }
            if (isset($param[self::SCANNER_MODE_INDEX])) {
                $this->scannerMode = (int)$param[self::SCANNER_MODE_INDEX];
            }

            $data = [];
            if ($this->configFileSet) {
                foreach($this->configFileSet as $fileName) {
                    $data = array_merge_recursive(
                        parse_ini_file($fileName, $this->processSections, $this->scannerMode)
                    );
                }
            }

            $this->mainNode = new Node(null, IConfig::MAIN_NODE_KEY, 'main node');
            foreach ($data as $key => $group) {
                if (!is_array($group)) {
                    $newNode = new Node($this->mainNode, $key, $group);
                    $this->mainNode->addChild($newNode);
                    continue;
                }

                $newNode = new Node($this->mainNode, $key, $key);

                foreach ($group as $name => $value) {
                    $childNode = new Node($newNode, $name, $value);
                    $newNode->addChild($childNode);
                }
                $this->mainNode->addChild($newNode);
            }
        } catch (ExceptionConfig $e) {
            throw $e;
        }
    }

    /**
     * deletes a config node
     *
     * @param string $id
     *
     * @return mixed
     */
    public function delete(string $id = "")
    {
        //@todo think of implementation
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
    public function saveConfig(Node $node = null)
    {
    }
}