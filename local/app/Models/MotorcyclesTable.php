<?php
namespace App\Models;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Iblock\Elements\ElementManufacturerTable;
use Bitrix\Iblock\Elements\ElementCountriesTable;

class MotorcyclesTable extends DataManager
{
    public static function getTableName()
    {
        return 'dil_motorcycles';
    }

    public static function getMap()
    {
        return [
            new IntegerField('ID', ['primary' => true, 'autocomplete' => true]),
            new StringField('UF_MODEL'),
            new IntegerField('UF_YEAR'),
            new IntegerField('UF_ENGINE_VOLUME'),
            new IntegerField('UF_MANUFACTURER_ID'),
            new IntegerField('UF_COUNTRY_ID'),

            // 👇 Добавляем Reference-связи
            new Reference(
                'MANUFACTURER',
                ElementManufacturerTable::class,
                ['=this.UF_MANUFACTURER_ID' => 'ref.ID'],
                ['join_type' => 'left']
            ),
            new Reference(
                'COUNTRY',
                ElementCountriesTable::class,
                ['=this.UF_COUNTRY_ID' => 'ref.ID'],
                ['join_type' => 'left']
            ),
        ];
    }
}