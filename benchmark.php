<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 12.07.16
 * Time: 21:49
 */

use chilimatic\lib\Config\File;

require_once __DIR__ . '/vendor/autoload.php';

$config = new File(
    [
        File::CONFIG_PATH_INDEX => __DIR__ . '/test/data'
    ]
);

$config->set('process_log_path', 'test1');

$t = $config->get('process_log_path');


$a = $t;