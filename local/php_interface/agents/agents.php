<?php

/*
 * Обновление остатков в каталоге
 * При остатке товара 0 запускаем запрос на закупку
 */
function updateStockAgent() {
    include_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/agents/update_stock.php");
    return "updateStockAgent();";
}