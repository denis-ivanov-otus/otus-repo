<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Врачи");

use Bitrix\Main\Loader;
use Local\Orm\DoctorsTable;

Loader::includeModule('iblock');
Loader::registerAutoLoadClasses(null, [
    'Local\\Orm\\DoctorsTable' => '/local/php_interface/lib/Orm/DoctorsTable.php',
]);

$doctors = DoctorsTable::getList([
    'select' => ['ID', 'NAME'],
    'filter' => ['IBLOCK_ID' => 16, 'ACTIVE' => 'Y'],
    'order' => ['NAME' => 'ASC'],
])->fetchAll();
?>

    <style>
        .cards-list {
            display:flex;
            flex-wrap:wrap;
            width:100%;
            max-width:1024px;
            margin:40px auto;
            justify-content:flex-start;
            align-items:center;
        }
        .card {
            background: #f2f6f7;
            border-radius: 6px;
            min-height: 80px;
            width:200px;
            padding:12px;
            margin:12px;
            filter: drop-shadow(6px 6px 3px #4444dd);
            display:flex;
            flex-direction:column;
            justify-content:center;
            align-items:center;
            text-align:center;
            font-size:16px;
            cursor:pointer;
            position: relative;
        }
        .card:hover {
            filter: drop-shadow(6px 6px 3px #6666ff);
        }
        h1 {
            font: 26px/26px Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-weight: 300;
        }
        section.doctors {
            padding:40px;
            display:flex;
            flex-direction:column;
            align-items:center;
            font-size:18px;
        }
        .doctor-details {
            margin-top: 10px;
            background: #fff;
            padding: 10px;
            border: 1px solid #ccc;
            width: 100%;
            font-size: 14px;
        }
        .add-buttons {
            display:flex;
            justify-content:flex-start;
            width:100%;
            max-width:1024px;
            margin-top:40px;
        }
        .add-buttons button {
            padding:6px 12px;
            margin-right:16px;
            height:32px;
            border:1px solid #D2CBBD;
            border-radius:6px;
        }
        .add-buttons button:hover {
            filter: drop-shadow(6px 6px 3px #4444dd);
        }
        .doctor-details li {
            text-align: left;
        }
    </style>

    <section class="doctors">
        <h1>Список врачей</h1>

        <div class="add-buttons">
            <a href="/doctors/add_doctor.php"><button>➕ Добавить врача</button></a>
            <a href="/doctors/add_procedure.php"><button>➕ Добавить процедуру</button></a>
        </div>

        <div class="cards-list" id="doctors-list">
            <?php foreach ($doctors as $doctor): ?>
                <div class="card" onclick="loadDoctorDetails(<?= $doctor['ID'] ?>)">
                    <?= htmlspecialchars($doctor['NAME']) ?>
                    <div class="doctor-details" id="doctor-details-<?= $doctor['ID'] ?>" style="display:none;"></div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <script>
        function loadDoctorDetails(id) {
            const container = document.getElementById('doctor-details-' + id);

            if (container.dataset.loaded === 'true') {
                container.style.display = (container.style.display === 'none') ? 'block' : 'none';
                return;
            }

            fetch('./ajax/load_doctor.php?id=' + id)
                .then(response => response.text())
                .then(html => {
                    container.innerHTML = html;
                    container.style.display = 'block';
                    container.dataset.loaded = 'true';
                })
                .catch(err => {
                    container.innerHTML = '<p>Ошибка загрузки</p>';
                    container.style.display = 'block';
                });
        }
    </script>

<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>