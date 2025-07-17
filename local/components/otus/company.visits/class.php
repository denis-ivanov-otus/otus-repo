<?php

use Bitrix\Main\Loader;
use Bitrix\Main\UI\Extension;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Context;
use Bitrix\Main\UI\PageNavigation;
use Otus\CrmModule\VisitTable;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class OtusCompanyVisitsComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        if (!Loader::includeModule('otus.crm_module')) {
            ShowError(GetMessage('NOT_MODULE_TEXT'));
            return;
        }

        $request = Context::getCurrent()->getRequest();
        $companyId = (int)($this->arParams['ENTITY_ID'] ?? 0);

        $filter = ['=COMPANY_ID' => $companyId];

        $nav = new PageNavigation('company_visits');
        $nav->allowAllRecords(true)
            ->setPageSize(10)
            ->initFromUri();

        $result = VisitTable::getList([
            'filter' =>  $filter,
            'select' => ['ID', 'VISIT_DATE', 'MANAGER', 'COMMENTS'],
            'order' => ['VISIT_DATE' => 'DESC'],
            'offset' => $nav->getOffset(),
            'limit' => $nav->getLimit(),
            'count_total' => true,
        ]);


        $nav->setRecordCount($result->getCount());

        $rows = [];
        while ($item = $result->fetch()) {

            $rows[] = [
                'data' => [
                    'ID' => $item['ID'],
                    'VISIT_DATE' => $item['VISIT_DATE'] instanceof \Bitrix\Main\Type\Date
                        ? $item['VISIT_DATE']->format('d.m.Y')
                        : $item['VISIT_DATE'],
                    'MANAGER' => $item['MANAGER'],
                    'COMMENTS' => $item['COMMENTS'],
                ],
            ];
        }

        $this->arResult = [
            'GRID_ID' => 'company_visits_grid_' . $companyId,
            'ROWS' => $rows,
            'NAV_OBJECT' => $nav,
        ];

        $this->includeComponentTemplate();
    }
}