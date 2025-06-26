<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Добавить врача");

use Bitrix\Main\Loader;
use Bitrix\Main\Context;
use Bitrix\Iblock\Elements\ElementDoctorsTable;
use Bitrix\Iblock\Elements\ElementProceduresTable;

Loader::includeModule('iblock');
$iblockId = 16;
$el = new \CIBlockElement();

$request = Context::getCurrent()->getRequest();

// Получаем список процедур
$procedures = ElementProceduresTable::getList([
    'select' => ['ID', 'NAME'],
    'filter' => ['=ACTIVE' => 'Y'],
])->fetchAll();

// Обработка формы
if ($request->isPost() && $request->getPost('action') === 'add_doctor') {
    $name = trim($request->getPost('name'));
    $procedureIds = array_map('intval', $request->getPost('procedures') ?? []);

    $addResult = ElementDoctorsTable::add([
        'IBLOCK_ID' => $iblockId,
        'NAME' => $name,
        'ACTIVE' => 'Y',
    ]);

    if ($addResult->isSuccess()) {
        $newElementId = $addResult->getId();

        \CIBlockElement::SetPropertyValuesEx($newElementId, $iblockId, [
            'PROCEDURES' => $procedureIds,
        ]);

        LocalRedirect('/doctors/index.php');
    } else {
        echo '<div style="color:red;">Ошибка: ' . implode(', ', $addResult->getErrorMessages()) . '</div>';
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
        <h2>Добавить врача</h2>
        <form class="doctor-add-form" method="post">
            <input type="hidden" name="action" value="add_doctor">
            <input type="text" name="name" placeholder="ФИО врача" required>
            <select name="procedures[]" multiple>
                <?php foreach ($procedures as $proc): ?>
                    <option value="<?= $proc['ID'] ?>"><?= htmlspecialchars($proc['NAME']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="submit" value="Сохранить">
        </form>
    </div>

<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>