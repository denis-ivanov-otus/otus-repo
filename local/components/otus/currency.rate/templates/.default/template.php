<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<?php if ($arResult["RATE"]): ?>
    <p>
        <?= GetMessage('CUR_CURRENCY') ?> <strong><?= htmlspecialcharsbx($arResult["CURRENCY_NAME"]) ?></strong>
        (<?= htmlspecialcharsbx($arResult["CURRENCY"]) ?>)
        <?= GetMessage('CUR_BASE')?> <strong><?= htmlspecialcharsbx($arResult["BASE_CURRENCY_NAME"]) ?></strong>
        (<?= htmlspecialcharsbx($arResult["BASE_CURRENCY"]) ?>):
        <br>
        <strong><?= number_format($arResult["RATE"], 4) ?></strong>
    </p>
<?php else: ?>
    <p><?= GetMessage('CUR_NOT_FOUND')?></p>
<?php endif; ?>