<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
GLOBAL $APPLICATION;
$APPLICATION->SetTitle("Каталог мотоциклов");

use Bitrix\Main\Loader;
use Bitrix\Main\DB\SqlExpression;
use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\ElementPropertyTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\ORM\Fields\Relations\Reference;

use App\Models\MotorcyclesTable;

Loader::includeModule('iblock');


function getPropertyIdByCode(int $iblockId, string $code): ?int {
    $property = PropertyTable::getList([
        'filter' => ['IBLOCK_ID' => $iblockId, 'CODE' => $code],
        'select' => ['ID']
    ])->fetch();
    return $property ? (int)$property['ID'] : null;
}

$manufacturerIblockId = \Bitrix\Iblock\IblockTable::getList([
    'filter' => ['=CODE' => 'manufacturer'],
    'select' => ['ID']
])->fetch()['ID'];

$countryIblockId = \Bitrix\Iblock\IblockTable::getList([
    'filter' => ['=CODE' => 'countries'],
    'select' => ['ID']
])->fetch()['ID'];

$foundingYearPropId = getPropertyIdByCode($manufacturerIblockId, 'FOUNDING_YEAR');
$continentPropId = getPropertyIdByCode($countryIblockId, 'CONTINENT');

if (!$foundingYearPropId || !$continentPropId) {
    die("❌ Свойства не найдены");
}

$result = MotorcyclesTable::getList([
    'select' => [
        'ID',
        'UF_MODEL',
        'UF_YEAR',
        'UF_ENGINE_VOLUME',
        'MANUFACTURER_NAME' => 'MANUFACTURER.NAME',
        'COUNTRY_NAME' => 'COUNTRY.NAME',
        'FOUNDING_YEAR' => 'MANUFACTURER_YEAR.VALUE',
        'CONTINENT' => 'COUNTRY_CONTINENT.VALUE',
    ],
    'runtime' => [
        new Reference(
            'MANUFACTURER',
            ElementTable::class,
            ['=this.UF_MANUFACTURER_ID' => 'ref.ID'],
            ['join_type' => 'left']
        ),
        new Reference(
            'MANUFACTURER_YEAR',
            ElementPropertyTable::class,
            [
                '=this.UF_MANUFACTURER_ID' => 'ref.IBLOCK_ELEMENT_ID',
                '=ref.IBLOCK_PROPERTY_ID' => new SqlExpression('?i', $foundingYearPropId),
            ],
            ['join_type' => 'left']
        ),
        new Reference(
            'COUNTRY',
            ElementTable::class,
            ['=this.UF_COUNTRY_ID' => 'ref.ID'],
            ['join_type' => 'left']
        ),
        new Reference(
            'COUNTRY_CONTINENT',
            ElementPropertyTable::class,
            [
                '=this.UF_COUNTRY_ID' => 'ref.IBLOCK_ELEMENT_ID',
                '=ref.IBLOCK_PROPERTY_ID' => new SqlExpression('?i', $continentPropId),
            ],
            ['join_type' => 'left']
        ),
    ],
])->fetchAll();



echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr>
    <th>ID</th>
    <th>Модель</th>
    <th>Год</th>
    <th>Двигатель</th>
    <th>Производитель</th>
    <th>Основан</th>
    <th>Страна</th>
    <th>Континент</th>
</tr>";

foreach ($result as $row) {
    echo "<tr>";
    echo "<td>{$row['ID']}</td>";
    echo "<td>{$row['UF_MODEL']}</td>";
    echo "<td>{$row['UF_YEAR']}</td>";
    echo "<td>{$row['UF_ENGINE_VOLUME']}</td>";
    echo "<td>" . ($row['MANUFACTURER_NAME'] ?: '—') . "</td>";
    echo "<td>" . ($row['FOUNDING_YEAR'] ?: '—') . "</td>";
    echo "<td>" . ($row['COUNTRY_NAME'] ?: '—') . "</td>";
    echo "<td>" . ($row['CONTINENT'] ?: '—') . "</td>";
    echo "</tr>";
}

echo "</table>";

require_once ($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');



