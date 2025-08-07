<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Iblock\ElementTable;
use Bitrix\Catalog\ProductTable;
use Bitrix\Catalog\MeasureTable;
use Bitrix\Crm\Service\Container;
use Bitrix\Crm\ProductRow;

use CBPDocument;
use CBPWorkflowTemplateLoader;

Loader::includeModule('iblock');
Loader::includeModule('catalog');
Loader::includeModule('crm');
Loader::includeModule('bizproc');

$IBLOCK_ID = IBLOCK_CATALOG_ID;
$entityTypeId = SP_PARTS_REQUEST_ID;
$stockService = 'https://www.random.org/integers/?num=1&min=0&max=100&col=1&base=10&format=plain&rnd=new';

$products = ElementTable::getList([
    'filter' => ['=IBLOCK_ID' => $IBLOCK_ID, '=ACTIVE' => 'Y'],
    'select' => ['ID', 'NAME']
])->fetchAll();

$http = new HttpClient(["timeout" => 5]);

$productsToOrder = [];

foreach ($products as $product) {
    $productId = (int)$product['ID'];
    $productName = $product['NAME'];

    // Получаем остаток с сервиса
    $response = $http->get($stockService);
    $stock = ($response !== false) ? (int)trim($response) : 0;

    // Эмулируем 20% шанс обнуления
    if ($stock > 0 && rand(0, 4) === 0) {
        $stock = 0;
    }

    $updateResult = ProductTable::update($productId, [
        'QUANTITY' => $stock,
    ]);

    if ($updateResult->isSuccess()) {
        AddMessage2Log("Обновлён: {$productName} (ID: {$productId}) → Остаток: {$stock}", "stock_agent");

        if ($stock === 0) {
            $productData = ProductTable::getRow([
                'filter' => ['=ID' => $productId],
                'select' => ['ID', 'MEASURE', 'PURCHASING_PRICE']
            ]);

            $priceRow = \Bitrix\Catalog\PriceTable::getRow([
                'filter' => [
                    '=PRODUCT_ID' => $productId,
                    '=CATALOG_GROUP_ID' => 1
                ],
                'select' => ['PRICE', 'CURRENCY']
            ]);

            $price = $priceRow['PRICE'] ?? 1;

            $productsToOrder[] = ProductRow::createFromArray([
                'PRODUCT_ID' => $productId,
                'PRICE' => (float) $price,
                'QUANTITY' => 1,
                'CURRENCY' => $priceRow['CURRENCY'] ?? 'BYN',
                'CUSTOMIZED' => 'Y',
                'PRODUCT_NAME' => $productName
            ]);
        }
    } else {
        AddMessage2Log("Ошибка обновления {$productName} (ID: {$productId}): " . implode('; ', $updateResult->getErrorMessages()), "stock_agent");
    }
}

//Создаём смарт-процесс с привязкой товаров
if (!empty($productsToOrder)) {
    try {
        $factory = Container::getInstance()->getFactory($entityTypeId);

        if ($factory) {
            $item = $factory->createItem();

            $item->set('TITLE', 'Автоматическая заявка на закупку запчастей с остатком 0');
            $item->set('ASSIGNED_BY_ID', 1);

            //Устанавливаем строки товаров
            $item->setProductRows($productsToOrder);

            $saveResult = $item->save();

            if ($saveResult->isSuccess()) {
                AddMessage2Log("Создана заявка на закупку ID {$item->getId()} с " . count($productsToOrder) . " товарами", "stock_agent");

                //Запуск БП на закупку
                $bpTemplateId = BP_ORDER_PARTS_TEMPLATE_ID;

                $elemId = $item->getId();
                $res = \CBPDocument::StartWorkflow(
                    $bpTemplateId, // Идентификатор шаблона БП
                    [
                        "crm",
                        "Bitrix\Crm\Integration\BizProc\Document\Dynamic", // Идентификатор документа БП
                        "DYNAMIC_" . $entityTypeId . "_" . $elemId,
                    ],
                    ["TargetUser" => "user_1"],
                    $arErrorsTmp
                );
                if (count($arErrorsTmp) == 0) {
                    AddMessage2Log("Запущен БП на закупку запчастей для элемента СП ".$elemId);

                } else {
                    AddMessage2Log("Ошибка запуска БП на закупку запчастей для элемента СП ".$elemId);
                }

            } else {
                AddMessage2Log("Ошибка сохранения заявки: " . implode('; ', $saveResult->getErrorMessages()), "stock_agent");
            }
        } else {
            AddMessage2Log("Не удалось получить фабрику для типа {$entityTypeId}", "stock_agent");
        }
    } catch (\Throwable $e) {
        AddMessage2Log("Исключение при создании заявки: " . $e->getMessage(), "stock_agent");
    }
}

return "updateStockAgent();";