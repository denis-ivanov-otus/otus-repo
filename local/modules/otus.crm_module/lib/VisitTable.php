<?php

namespace Otus\CrmModule;

use Bitrix\Main\Entity;

class VisitTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'b_otus_company_visits';
    }

    public static function getMap()
    {
        return [
            new Entity\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
            ]),
            new Entity\IntegerField('COMPANY_ID'),
            new Entity\DateField('VISIT_DATE'),
            new Entity\StringField('MANAGER'),
            new Entity\TextField('COMMENTS'),
        ];
    }
}