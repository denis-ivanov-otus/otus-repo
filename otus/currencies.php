<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Курс валюты");
?>

<?php
$APPLICATION->IncludeComponent(
	"otus:currency.rate", 
	".default", 
	array(
		"CURRENCY" => "EUR",
		"COMPONENT_TEMPLATE" => ".default"
	),
	false
);
?>

<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");