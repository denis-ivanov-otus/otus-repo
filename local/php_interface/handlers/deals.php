<?php
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Crm\Relation\EntityRelationTable;

EventManager::getInstance()->addEventHandler('crm', 'OnBeforeCrmDealAdd', function (&$arFields) {
    Loader::includeModule('crm');

    //ID смарт-процесса автомобилей
    $vehicleEntityTypeId = SP_GARAGE_ID;

    //Получаем ID автомобиля из поля PARENT_ID_1038
    $vehicleId = (int)($arFields['PARENT_ID_'.$vehicleEntityTypeId] ?? 0);

    //Авто не выбран — пропускаем проверку
    if (!$vehicleId) {
        return;
    }

    //Получаем все сделки, связанные с этим авто
    $linkedDeals = EntityRelationTable::getList([
        'filter' => [
            '=SRC_ENTITY_TYPE_ID' => $vehicleEntityTypeId,
            '=SRC_ENTITY_ID' => $vehicleId,
            '=DST_ENTITY_TYPE_ID' => \CCrmOwnerType::Deal,
        ],
        'select' => ['DST_ENTITY_ID'],
    ])->fetchAll();

    //Связанных сделок нет — можно создавать сделку
    if (empty($linkedDeals)) {
        return;
    }

    $dealIds = array_column($linkedDeals, 'DST_ENTITY_ID');

    //Проверяем, есть ли среди сделок незакрытые
    $res = \CCrmDeal::GetList([], [
        'ID' => $dealIds,
        'CLOSED' => 'N',
    ], ['ID', 'TITLE', 'ASSIGNED_BY_ID'], ['nTopCount' => 1] );

    if ($deal = $res->Fetch()) {
        $arFields['RESULT_MESSAGE'] = 'Нельзя создать заказ-наряд — по указанному автомобилю есть незакрытая сделка (ID: '.$deal['ID'].')';
        return false;
    }
});