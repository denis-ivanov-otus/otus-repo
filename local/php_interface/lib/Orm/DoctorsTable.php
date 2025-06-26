<?php
namespace Local\Orm;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\ElementPropertyTable;
use Bitrix\Iblock\PropertyTable;

class DoctorsTable extends DataManager
{
    public static function getTableName()
    {
        return 'b_iblock_element';
    }

    public static function getMap()
    {
        return [
            new Fields\IntegerField('ID', ['primary' => true]),
            new Fields\IntegerField('IBLOCK_ID'),
            new Fields\StringField('NAME'),
            new Fields\StringField('CODE'),
            new Fields\TextField('DETAIL_TEXT'),
            new Fields\BooleanField('ACTIVE', ['values' => ['N', 'Y']]),
            new Reference(
                'PROPERTIES',
                ElementPropertyTable::class,
                Join::on('this.ID', 'ref.IBLOCK_ELEMENT_ID')
                    ->where('ref.IBLOCK_PROPERTY_ID', static::getProceduresPropertyId())
            ),
            new Reference(
                'PROCEDURE',
                ElementTable::class,
                Join::on('this.PROPERTIES.VALUE', 'ref.ID')
            ),
        ];
    }

    public static function getProceduresPropertyId(): int
    {
        static $id = 0;
        if (!$id) {
            $res = PropertyTable::getList([
                'filter' => ['IBLOCK_ID' => 16, '=CODE' => 'PROCEDURES'],
                'select' => ['ID'],
                'limit' => 1
            ])->fetch();
            $id = (int)($res['ID'] ?? 0);
        }
        return $id;
    }
}