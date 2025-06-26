<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Добавить процедуру");

use Bitrix\Main\Loader;
use Bitrix\Main\Context;
use Bitrix\Iblock\Elements\ElementProceduresTable;

Loader::includeModule('iblock');

$request = Context::getCurrent()->getRequest();

if ($request->isPost() && $request->getPost('action') === 'add_procedure') {
    $name = trim($request->getPost('procedure_name'));

    if ($name !== '') {

        $result = ElementProceduresTable::add([
            'IBLOCK_ID' => 17,
            'NAME' => $name,
            'ACTIVE' => 'Y',
        ]);

        if ($result->isSuccess()) {
            LocalRedirect('/doctors/index.php');
        } else {
            echo '<div style="color:red;">Ошибка: ' . implode(', ', $result->getErrorMessages()) . '</div>';
        }
    } else {
        echo '<div style="color:red;">Название процедуры не может быть пустым</div>';
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
    </style>

    <div class="card">
        <h2>Добавить процедуру</h2>
        <form class="doctor-add-form" method="post">
            <input type="hidden" name="action" value="add_procedure">
            <input type="text" name="procedure_name" placeholder="Название процедуры" required>
            <input type="submit" value="Добавить">
        </form>
    </div>

<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>