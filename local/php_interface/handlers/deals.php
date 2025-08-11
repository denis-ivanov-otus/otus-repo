<?php
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Crm\Relation\EntityRelationTable;

EventManager::getInstance()->addEventHandler('crm', 'OnBeforeCrmDealAdd', function (&$arFields) {
    if (!Loader::includeModule('crm')) {
        return;
    }

    //ID смарт-процесса автомобилей
    $vehicleEntityTypeId = SP_GARAGE_ID;

    //Получаем ID автомобиля из поля PARENT_ID_1038
    $vehicleId = (int)($arFields['PARENT_ID_'.$vehicleEntityTypeId] ?? 0);

    //Авто не выбран - пропускаем проверку
    if ($vehicleId <= 0) {
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
    $res = \CCrmDeal::GetList(
        [],
        [
            'ID' => $dealIds,
            'CLOSED' => 'N',
        ],
        ['ID', 'TITLE', 'ASSIGNED_BY_ID'],
        ['nTopCount' => 1]
    );

    if ($deal = $res->Fetch()) {
        $dealTitle = isset($deal['TITLE']) ? htmlspecialcharsbx((string)$deal['TITLE']) : '';
        $newDealTitle = isset($arFields['TITLE']) ? htmlspecialcharsbx((string)$arFields['TITLE']) : '';

        //Имя инициатора
        $currentUserId = (is_object($GLOBALS['USER']) && method_exists($GLOBALS['USER'], 'GetID'))
            ? (int)$GLOBALS['USER']->GetID()
            : 0;

        $currentUserName = '';
        if ($currentUserId > 0) {
            $rsUser = \CUser::GetByID($currentUserId);
            if ($arUser = $rsUser->Fetch()) {
                $nameFormat = \CSite::GetNameFormat();
                $currentUserName = \CUser::FormatName($nameFormat, $arUser, true, false);
                $currentUserName = htmlspecialcharsbx($currentUserName);
            }
        }

        //Ссылки на карточки
        $vehicleUrl = "/crm/type/{$vehicleEntityTypeId}/details/{$vehicleId}/";
        $dealUrl = "/crm/deal/details/{$deal['ID']}/";

        //Тексты ссылок
        $vehicleLinkText = "Автомобиль #{$vehicleId}";
        $dealLinkText = $dealTitle !== '' ? $dealTitle : "Сделка #{$deal['ID']}";

        //Уведомление ответственному по существующей незакрытой сделке
        if (Loader::includeModule('im') && (int)$deal['ASSIGNED_BY_ID'] > 0) {
            $notifyMessage = "Попытка создать новую сделку по автомобилю [url={$vehicleUrl}]{$vehicleLinkText}[/url] была заблокирована: "
                ."существует незакрытая сделка [url={$dealUrl}]{$dealLinkText}[/url].";

            if ($newDealTitle !== '') {
                $notifyMessage .= "\nНовая сделка: {$newDealTitle}.";
            }
            if ($currentUserName !== '') {
                $notifyMessage .= "\nИнициатор: {$currentUserName}.";
            }

            \CIMNotify::Add([
                'TO_USER_ID' => (int)$deal['ASSIGNED_BY_ID'],
                'FROM_USER_ID' => 0,
                'NOTIFY_TYPE' => IM_NOTIFY_SYSTEM,
                'NOTIFY_MODULE' => 'crm',
                'NOTIFY_EVENT' => 'deal_blocked_existing_open',
                'NOTIFY_TAG' => "crm_deal_blocked_{$vehicleEntityTypeId}_{$vehicleId}",
                'NOTIFY_MESSAGE' => $notifyMessage,
            ]);
        }

        $arFields['RESULT_MESSAGE'] = 'Нельзя создать заказ-наряд - по указанному автомобилю есть незакрытая сделка (ID: '.(int)$deal['ID'].')';
        return false;
    }
});