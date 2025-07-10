<?php
use Bitrix\Main\Loader;
use Bitrix\Currency\CurrencyTable;
use Bitrix\Currency\CurrencyLangTable;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!Loader::includeModule("currency")) {
    return;
}

$currencyList = [];

$currencyIterator = CurrencyTable::getList([
    'select' => ['CURRENCY'],
    'order' => ['CURRENCY' => 'ASC'],
]);

while ($currency = $currencyIterator->fetch()) {
    $code = $currency['CURRENCY'];

    $langRow = CurrencyLangTable::getRow([
        'filter' => [
            'CURRENCY' => $code,
            'LID' => LANGUAGE_ID,
        ],
        'select' => ['FULL_NAME'],
    ]);

    $name = $langRow['FULL_NAME'] ?? $code;
    $currencyList[$code] = "[{$code}] {$name}";
}

$arComponentParameters = [
    "PARAMETERS" => [
        "CURRENCY" => [
            "PARENT" => "BASE",
            "NAME" => GetMessage("CUR_PARAM_NAME"),
            "TYPE" => "LIST",
            "VALUES" => $currencyList,
            "DEFAULT" => "USD",
            "REFRESH" => "N",
        ],
    ],
];