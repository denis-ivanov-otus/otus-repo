<?php

namespace Otus\CrmModule;

class CrmTabManager
{
    public function getTabs(int $entityId, int $entityTypeId, array $existingTabs = []): array
    {
        return [
            [
                'id' => 'otus_company_visits',
                'name' => GetMessage('OTUS_COMPANY_TAB_NAME'),
                'loader' => [
                    'serviceUrl' => '/local/modules/otus.crm_module/ajax/visits.lazyload.php',
                    'componentData' => [
                        'template' => '',
                        'params' => [
                            'ENTITY_ID' => $entityId,
                        ],
                        'componentName' => 'otus:company.visits',
                    ],
                ],
                'enableLazyLoad' => true,
                'sort' => 150,
            ]
        ];
    }
}