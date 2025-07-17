<?php
define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);
define('PUBLIC_AJAX_MODE', true);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

global $APPLICATION;

$APPLICATION->ShowAjaxHead();

$APPLICATION->IncludeComponent(
    'otus:company.visits',
    '',
    [
        'ENTITY_ID' => (int)$_REQUEST['PARAMS']['params']['ENTITY_ID'],
        'AJAX_MODE' => 'Y',
    ],
    false,
    ['HIDE_ICONS' => 'Y']
);