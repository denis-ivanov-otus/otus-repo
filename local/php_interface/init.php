<?php
define ('DEBUG_FILE_NAME', $_SERVER['DOCUMENT_ROOT'].'/logs/'.date("Y-m-d").'.log');

if(file_exists(__DIR__."/src/autoloader.php")){
    require_once __DIR__."/src/autoloader.php";
}