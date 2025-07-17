<?php

use Bitrix\Main\Loader;
use Bitrix\Main\EventManager;

class otus_crm_module extends CModule
{
    public function __construct()
    {
        $arModuleVersion = [];
        include(__DIR__ . '/../version.php');

        $this->MODULE_ID = 'otus.crm_module';
        $this->MODULE_NAME = GetMessage('OTUS_MODULE_NAME');
        $this->MODULE_DESCRIPTION = GetMessage('OTUS_MODULE_DESC');
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->PARTNER_NAME = GetMessage('OTUS_PARTNER_NAME');
        $this->PARTNER_URI = GetMessage('OTUS_PARTNER_URI');
    }

    public function DoInstall()
    {
        global $DB;

        $this->InstallDB();

        RegisterModule($this->MODULE_ID);

        Loader::registerAutoLoadClasses(
            $this->MODULE_ID,
            [
                'Otus\\CrmModule\\EventHandler' => '/lib/EventHandler.php',
                'Otus\\CrmModule\\CrmTabManager' => '/lib/CrmTabManager.php',
            ]
        );

        EventManager::getInstance()->registerEventHandler(
            'crm',
            'onEntityDetailsTabsInitialized',
            $this->MODULE_ID,
            'Otus\\CrmModule\\EventHandler',
            'onTabsInit'
        );
    }

    public function DoUninstall()
    {
        global $DB;

        $this->UnInstallDB();

        EventManager::getInstance()->unregisterEventHandler(
            'crm',
            'onEntityDetailsTabsInitialized',
            $this->MODULE_ID,
            'Otus\\CrmModule\\EventHandler',
            'onTabsInit'
        );

        UnRegisterModule($this->MODULE_ID);
    }

    public function InstallDB()
    {
        global $DB;

        $errors = $DB->RunSQLBatch(__DIR__ . '/db/install.sql');

        if ($errors !== false) {
            throw new \Bitrix\Main\SystemException(implode("\n", $errors));
        }

        $this->InsertDemoData();
    }

    public function UnInstallDB()
    {
        global $DB;

        $errors = $DB->RunSQLBatch(__DIR__ . '/db/uninstall.sql');

        if ($errors !== false) {
            throw new \Bitrix\Main\SystemException(implode("\n", $errors));
        }
    }

    protected function InsertDemoData()
    {
        global $DB;

        $demoData = [
            [1, '2025-07-01', 'Иванов И.И.', 'Первый визит'],
            [1, '2025-07-05', 'Петров П.П.', 'Обсуждали условия поставки'],
            [2, '2025-07-10', 'Сидоров С.С.', 'Подписание договора'],
        ];

        foreach ($demoData as $row) {
            $DB->Query("
                INSERT INTO b_otus_company_visits (COMPANY_ID, VISIT_DATE, MANAGER, COMMENTS)
                VALUES (" . (int)$row[0] . ", '" . $DB->ForSql($row[1]) . "', '" . $DB->ForSql($row[2]) . "', '" . $DB->ForSql($row[3]) . "')
            ");
        }
    }
}