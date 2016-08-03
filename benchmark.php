<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 12.07.16
 * Time: 21:49
 */

use chilimatic\lib\Config\File;
use chilimatic\lib\Config\Ini;

require_once __DIR__ . '/vendor/autoload.php';

echo 'starting Memory Read Config: ' . memory_get_usage() / 1024 / 1024 . '<br />';
$startTime = microtime(true);
$config = new File(
    [
        File::CONFIG_PATH_INDEX => __DIR__ . '/test/data'
    ]
);
$endTime = microtime(true);
$endMem = memory_get_usage() / 1024 / 1024;
echo 'end Memory: ' . ( $endMem ). '<br />';
echo "execution time read file: " . ($endTime-$startTime) . '<br />';

echo 'starting Memory: ' . memory_get_usage() / 1024 / 1024 . '<br />';
$startTime = microtime(true);
for($i = 0; $i < 1000; $i++) {
    $config->set('myKey', null);
}
$endTime = microtime(true);
$endMem = memory_get_usage() / 1024 / 1024;
echo "execution time set [$i]: " . ($endTime-$startTime) . '<br />';
echo 'end Memory: ' . ( $endMem ). '<br />';

$startTime = microtime(true);
for($i = 0; $i < 1000; $i++) {
    $config->get('myKey');
}
$endTime = microtime(true);

echo "execution time get [$i]: " . ($endTime-$startTime) . '<br />';


echo 'starting Memory Read Ini Config: ' . memory_get_usage() / 1024 / 1024 . '<br />';
$startTime = microtime(true);
$config = new Ini(
    [
         Ini::FILE_INDEX => __DIR__ . '/test/data/'
    ]
);
$endTime = microtime(true);
$endMem = memory_get_usage() / 1024 / 1024;
echo 'end Memory: ' . ( $endMem ). '<br />';
echo "execution time read ini file: " . ($endTime-$startTime) . '<br />';

echo 'starting Memory: ' . memory_get_usage() / 1024 / 1024 . '<br />';
$startTime = microtime(true);
for($i = 0; $i < 1000; $i++) {
    $config->set('myKey', null);
}
$endTime = microtime(true);
$endMem = memory_get_usage() / 1024 / 1024;
echo "execution time set [$i]: " . ($endTime-$startTime) . '<br />';
echo 'end Memory: ' . ( $endMem ). '<br />';

$startTime = microtime(true);
for($i = 0; $i < 1000; $i++) {
    $config->get('myKey');
}
$endTime = microtime(true);

echo "execution time get [$i]: " . ($endTime-$startTime) . '<br />';