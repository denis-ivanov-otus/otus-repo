<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

$curDate = date("d.m.Y H:i:s");
echo $curDate;
\Bitrix\Main\Diag\Debug::writeToFile($curDate, 'date', 'logs/task_1.log');
//\Bitrix\Main\Diag\Debug::dumpToFile($curDate,'date', 'logs/task_1.log');

require_once ($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
