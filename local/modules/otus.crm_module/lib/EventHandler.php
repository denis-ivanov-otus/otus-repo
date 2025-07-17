<?php

namespace Otus\CrmModule;

use Bitrix\Main\Event;
use Bitrix\Main\EventResult;

class EventHandler
{
    public static function onTabsInit($event)
    {
        if (!($event instanceof Event)) {
            return new EventResult(EventResult::SUCCESS, []);
        }

        $params = $event->getParameters();

        $entityId = (int)($params['entityID'] ?? 0);
        $entityTypeId = (int)($params['entityTypeID'] ?? 0);
        $tabs = $params['tabs'] ?? [];

        if ($entityTypeId !== 4) {
            return new EventResult(EventResult::SUCCESS, ['tabs' => $tabs]);
        }

        $manager = new CrmTabManager();
        $customTabs = $manager->getTabs($entityId, $entityTypeId, $tabs);

        return new EventResult(EventResult::SUCCESS, [
            'tabs' => array_merge($tabs, $customTabs),
        ]);
    }
}