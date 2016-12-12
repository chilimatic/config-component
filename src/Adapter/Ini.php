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
                throw new ExceptionConfig(
                    _('No config file was given, please make sure one is provided in the param array'),
                    0,
                    1,
                    __METHOD__,
                    __LINE__
                );
            }

            $this->configFileSet = $this->getConfigFileSet($param[self::FILE_INDEX]);


            if (isset($param[self::PROCESS_SECTION_INDEX])) {
                $this->processSections = (bool)$param[self::PROCESS_SECTION_INDEX];
            }
            if (isset($param[self::SCANNER_MODE_INDEX])) {
                $this->scannerMode = (int)$param[self::SCANNER_MODE_INDEX];
            }

            $this->populateEngine(
                $this->parseConfigSet()
            );
        } catch (ExceptionConfig $e) {
            throw $e;
        }
    }

    /**
     * @param array $data
     * @return Node|void
     */
    public function populateEngine(array $data = [])
    {
        if (0 === count($data)) {
            return null;
        }

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

        return $this->mainNode;
    }



    /**
     * @return array
     */
    public function parseConfigSet(){
        $data = [];
        if (0 !== count($this->configFileSet)) {
            foreach($this->configFileSet as $fileName) {
                $data = array_merge_recursive(
                    parse_ini_file($fileName, $this->processSections, $this->scannerMode)
                );
            }
        }

        return $data;
    }


    /**
     * @param string $path
     * @return array
     */
    public function getConfigFileSet(string $path) : array
    {
        $set = [];
        if (is_dir($path)) {
            $iniFileList = array_filter(
                scandir($path),
                function($file)  {
                    return strpos($file, '.ini') !== false;
                }
            );

            $set = array_map(
                function($fileName) use ($path){
                    return $path . DIRECTORY_SEPARATOR . $fileName;
                },
                $iniFileList
            );
        } else {
            $set[] = $path;
        }

        return $set;
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