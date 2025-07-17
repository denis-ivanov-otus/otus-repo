<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
global $APPLICATION;
$APPLICATION->ShowHead();

$APPLICATION->IncludeComponent(
    'bitrix:main.ui.grid',
    '',
    [
        'GRID_ID' => $arResult['GRID_ID'],
        'COLUMNS' => [
            ['id' => 'ID', 'name' => 'ID', 'default' => true],
            ['id' => 'VISIT_DATE', 'name' => 'Дата визита', 'default' => true],
            ['id' => 'MANAGER', 'name' => 'Менеджер', 'default' => true],
            ['id' => 'COMMENTS', 'name' => 'Заметки', 'default' => true],
        ],
        'ROWS' => $arResult['ROWS'],
        'NAV_OBJECT' => $arResult['NAV_OBJECT'],
        'AJAX_MODE' => 'Y',
        'AJAX_OPTION_JUMP' => 'N',
        'AJAX_OPTION_HISTORY' => 'N',
        'SHOW_ROW_CHECKBOXES' => false,
        'SHOW_GRID_SETTINGS_MENU' => true,
        'SHOW_NAVIGATION_PANEL' => true,
        'SHOW_PAGINATION' => true,
        'SHOW_SELECTED_COUNTER' => false,
        'SHOW_TOTAL_COUNTER' => true,
        'ALLOW_SORT' => true,
        'ALLOW_COLUMNS_SORT' => true,
        'ALLOW_ROWS_SORT' => false,
        'ALLOW_COLUMNS_RESIZE' => true,
        'ALLOW_HORIZONTAL_SCROLL' => true,
        'ALLOW_PIN_HEADER' => true,
        'TOTAL_ROWS_COUNT' => count($arResult['ROWS']),
        'AJAX_ID' => CAjax::GetComponentID('bitrix:main.ui.grid', '', '')
    ]
);

