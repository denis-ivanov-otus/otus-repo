<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;
use Bitrix\Iblock\Elements\ElementDoctorsTable;
use Bitrix\Iblock\Elements\ElementProceduresTable;

Loader::includeModule('iblock');

$doctorId = (int)$_GET['id'];
if (!$doctorId) {
    echo 'Некорректный ID';
    return;
}

// Получаем объект врача с привязанными процедурами
$doctors = ElementDoctorsTable::getList([
    'select' => [
        'ID',
        'NAME',
        'PROCEDURES.ELEMENT.ID',
        'PROCEDURES.ELEMENT.NAME',
        'PROCEDURES.ELEMENT.DESCRIPTION',
    ],
    'filter' => [
        '=ID' => $doctorId,
        '=ACTIVE' => 'Y',
    ],
])->fetchCollection();

if ($doctors->isEmpty()) {
    echo 'Процедуры не найдены
    <br>
    <div class="add-buttons">
    <a href="/doctors/edit_doctor.php?id='.$doctorId.'">
        <button>Редактировать</button>
    </a>
    </div>';
    return;
}

$doctor = $doctors->current();
$procedures = [];

foreach ($doctor->getProcedures()->getAll() as $link) {
    $element = $link->getElement();
    if ($element && $element->getId()) {
        $procedures[] = [
            'name' => htmlspecialchars($element->getName()),
            'desc' => $element->getDescription() !== null ? $element->getDescription()->getValue() : '',
        ];
    }
}

?>

<div>
    <?php if (!empty($procedures)): ?>
        <strong>Процедуры:</strong>
        <ul>
            <?php foreach ($procedures as $proc): ?>
                <li>
                    <?= $proc['name'] ?>
                    <?php if (!empty($proc['desc'])): ?>
                        <?= '('.$proc['desc'].')' ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p><em>Процедуры не назначены</em></p>
    <?php endif; ?>

    <br>
    <div class="add-buttons">
    <a href="/doctors/edit_doctor.php?id=<?= $doctorId ?>">
        <button>Редактировать</button>
    </a>
    </div>
</div>