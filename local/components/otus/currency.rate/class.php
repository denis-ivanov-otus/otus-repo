<?php
use Bitrix\Main\Loader;
use Bitrix\Currency\CurrencyLangTable;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class CurrencyRateComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        if (!Loader::includeModule("currency")) {
            ShowError(GetMessage("NOT_MODULE_TEXT"));
            return;
        }

        $currencyCode = $this->arParams["CURRENCY"] ?? null;
        $this->arResult["CURRENCY"] = $currencyCode;

        if ($currencyCode) {
            $baseCurrency = \CCurrency::GetBaseCurrency();
            $this->arResult["BASE_CURRENCY"] = $baseCurrency;

            $this->arResult["RATE"] = \CCurrencyRates::GetConvertFactor(
                $currencyCode,
                $baseCurrency
            );

            $langRow = CurrencyLangTable::getRow([
                'filter' => [
                    'CURRENCY' => $currencyCode,
                    'LID' => LANGUAGE_ID,
                ],
                'select' => ['FULL_NAME'],
            ]);
            $this->arResult["CURRENCY_NAME"] = $langRow['FULL_NAME'] ?? $currencyCode;

            // Название базовой валюты
            $baseLangRow = CurrencyLangTable::getRow([
                'filter' => [
                    'CURRENCY' => $baseCurrency,
                    'LID' => LANGUAGE_ID,
                ],
                'select' => ['FULL_NAME'],
            ]);
            $this->arResult["BASE_CURRENCY_NAME"] = $baseLangRow['FULL_NAME'] ?? $baseCurrency;

        } else {
            $this->arResult["RATE"] = false;
            $this->arResult["CURRENCY_NAME"] = '';
            $this->arResult["BASE_CURRENCY_NAME"] = '';
        }

        $this->includeComponentTemplate();
    }
}