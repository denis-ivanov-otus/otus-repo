<?php
require_once('const.php');
require_once('agents/agents.php');
require_once('handlers/deals.php');

define ('DEBUG_FILE_NAME', $_SERVER['DOCUMENT_ROOT'].'/logs/'.date("Y-m-d").'.log');

if(file_exists(__DIR__."/src/autoloader.php")){
    require_once __DIR__."/src/autoloader.php";
}

include_once __DIR__ . '/../app/autoload.php';

// вывод данных
function pr($var, $type = false) {
    echo '<pre style="font-size:10px; border:1px solid #000; background:#FFF; text-align:left; color:#000;">';
    if ($type)
        var_dump($var);
    else
        print_r($var);
    echo '</pre>';
}