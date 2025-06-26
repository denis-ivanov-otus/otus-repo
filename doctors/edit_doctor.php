<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Редактировать врача");

use Bitrix\Main\Loader;
use Bitrix\Main\Context;
use Local\Orm\DoctorsTable;
use Bitrix\Iblock\Elements\ElementProceduresTable;

Loader::includeModule('iblock');

Loader::registerAutoLoadClasses(null, [
    'Local\\Orm\\DoctorsTable' => '/local/php_interface/lib/Orm/DoctorsTable.php',
]);

$iblockIdDoctors = 16;
$iblockIdProcedures = 17;
$doctorId = (int)$_GET['id'];

// Получаем данные врача
$doctor = DoctorsTable::getRow([
    'select' => ['ID', 'NAME', 'DETAIL_TEXT'],
    'filter' => [
        '=ID' => $doctorId,
        '=IBLOCK_ID' => $iblockIdDoctors,
    ],
]);

if (!$doctor) {
    echo '<div style="color:red;">Врач не найден</div>';
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
    return;
}

// Получаем список процедур
$procedures = ElementProceduresTable::getList([
    'select' => ['ID', 'NAME'],
    'filter' => ['IBLOCK_ID' => $iblockIdProcedures, 'ACTIVE' => 'Y'],
])->fetchAll();

// Выбранные процедуры
$selected = [];
$props = \CIBlockElement::GetProperty($iblockIdDoctors, $doctorId, [], ['CODE' => 'PROCEDURES']);
while ($p = $props->Fetch()) {
    if ($p['VALUE']) {
        $selected[] = (string)$p['VALUE'];
    }
}

$request = Context::getCurrent()->getRequest();

if ($request->isPost() && $request->getPost('action') === 'edit_doctor') {
    $name = trim($request->getPost('name'));
    $description = trim($request->getPost('description'));
    $procedureIds = $request->getPost('procedures') ?? [];


    $updateResult = DoctorsTable::update($doctorId, [
        'NAME' => $name,
        'DETAIL_TEXT' => $description,
    ]);

    if ($updateResult->isSuccess()) {
        $elem = new \CIBlockElement();
        \CIBlockElement::SetPropertyValuesEx($doctorId, $iblockIdDoctors, [
            'PROCEDURES' => $procedureIds,
        ]);
        LocalRedirect('/doctors/index.php');
    } else {
        echo '<div style="color:red;">Ошибка обновления: ' . implode(', ', $updateResult->getErrorMessages()) . '</div>';
    }
}
?>

    <style>
        .card {
            background: #f2f6f7;
            border-radius: 6px;
            width: 440px;
            padding: 20px;
            margin: 40px auto;
            filter: drop-shadow(6px 6px 3px #4444dd);
        }
        .doctor-add-form {
            display: flex;
            flex-direction: column;
        }
        .doctor-add-form > * {
            width: 400px;
            margin: 12px;
            padding: 6px;
            border-radius: 6px;
            min-height: 40px;
            font-size: 16px;
        }
        .doctor-add-form > select,
        .doctor-add-form > input[type=submit] {
            width: 416px;
        }
    </style>

    <div class="card">
        <h2>Редактировать врача</h2>
        <form class="doctor-add-form" method="post">
            <input type="hidden" name="action" value="edit_doctor">
            <input type="hidden" name="doctor_id" value="<?= $doctor['ID'] ?>">
            <input type="text" name="name" value="<?= htmlspecialchars($doctor['NAME']) ?>" required>
            <select name="procedures[]" multiple>
                <?php foreach ($procedures as $p): ?>
                    <option value="<?= $p['ID'] ?>" <?= in_array((string)$p['ID'], $selected) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['NAME']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="submit" value="Сохранить">
        </form>
    </div>

<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>