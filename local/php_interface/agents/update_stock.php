<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Iblock\ElementTable;
use Bitrix\Catalog\ProductTable;

Loader::includeModule('iblock');
Loader::includeModule('catalog');

$IBLOCK_ID = IBLOCK_CATALOG_ID;
$stockService = 'https://www.random.org/integers/?num=1&min=0&max=100&col=1&base=10&format=plain&rnd=new';

$products = ElementTable::getList([
    'filter' => ['=IBLOCK_ID' => $IBLOCK_ID, '=ACTIVE' => 'Y'],
    'select' => ['ID', 'NAME']
])->fetchAll();

$http = new HttpClient(["timeout" => 5]);

foreach ($products as $product) {
    $productId = (int)$product['ID'];
    $productName = $product['NAME'];

    $response = $http->get($stockService);
    $stock = ($response !== false) ? (int)trim($response) : 0;

    $updateResult = ProductTable::update($productId, [
        'QUANTITY' => $stock,
    ]);

    if ($updateResult->isSuccess()) {
        AddMessage2Log("Обновлён: {$productName} (ID: {$productId}) → Остаток: {$stock}", "stock_agent");
    } else {
        AddMessage2Log("Ошибка обновления {$productName} (ID: {$productId}): " . implode('; ', $updateResult->getErrorMessages()), "stock_agent");
    }
}

return "updateStockAgent();";