<?php
use Bitrix\Main\EventManager;

CModule::AddAutoloadClasses('otus.crm_module', [
    'Otus\\CrmModule\\EventHandler' => 'lib/EventHandler.php',
    'Otus\\CrmModule\\CrmTabManager' => 'lib/CrmTabManager.php',
    'Otus\\CrmModule\\VisitTable'     => 'lib/VisitTable.php',
]);

EventManager::getInstance()->addEventHandler(
    'crm',
    'onEntityDetailsTabsInitialized',
    ['Otus\\CrmModule\\EventHandler', 'onTabsInit']
);
