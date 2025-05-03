<?php
    session_start();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $requestId = $_POST['requestId'];

        if (isset($requestId)){
            $query = "UPDATE pickingList SET location = ? WHERE OrderId = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $bancada, $requestId); 
            $stmt->execute();
            logAction("Technician $technicianId updated order location: OrderId=$requestId, NewLocation=$bancada");
            $stmt->close();
        }
    }

    $query = "SELECT p.OrderId, p.OrderNumber, p.SparePart, t.desc AS TypeDesc, p.location AS LocationId, l.desc AS LocationDesc 
              FROM pickingList p
              LEFT JOIN locations l ON p.location = l.id
              LEFT JOIN locations t ON p.type = t.id
              WHERE tec = $user AND NOT p.location = 200";
    $requests = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-translate="pageTitle">Olympus - Encomendas</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .confirm-checkbox {
            appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid #1E3A8A;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease-in-out;
            background-color: white;
        }
        .confirm-checkbox:checked {
            background-color: #1E3A8A;
            border-color: #163172;
            position: relative;
        }
        .confirm-checkbox:checked::after {
            content: '\2713'; 
            font-size: 14px;
            color: white;
            font-weight: bold;
        }
        .confirm-checkbox:disabled {
            cursor: not-allowed;
            opacity: 0.5;
        }
        .checkbox-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }
        .bench-info {
            text-align: center;
            margin-top: 0px;
            font-size: 22px;
            font-weight: bold;
            color: #1E3A8A;
        }
  
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            justify-content: center;
            align-items: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease-in-out;
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            width: 350px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transform: translateY(-20px);
            transition: transform 0.3s ease-in-out;
        }
        .modal h3 {
            margin-bottom: 10px;
            color: #1E3A8A;
        }
        .modal p {
            color: #333;
            font-size: 16px;
        }
        .modal-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .modal-buttons button {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease-in-out;
        }
        #confirmYes {
            background: #1E3A8A;
            color: white;
        }
        #confirmYes:hover {
            background: #163172;
        }
        #confirmNo {
            background: none;
            color: #1E3A8A;
            border: 1px solid #1E3A8A;
        }
        #confirmNo:hover {
            background: #f0f0f0;
        }
        .modal.show {
            display: flex;
            opacity: 1;
            pointer-events: auto;
        }
        .modal.show .modal-content {
            transform: translateY(0);
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <img src="img/logo.png" alt="Olympus Logo" class="logo">
        <div class="nav-right">
            <button class="lang-switch">
                <img src="img/World.png" alt="Idioma"> Eng/Pt
            </button>
            <form action="/bancadaID.php">
                <button type="submit" value="Trocar Bancada" data-translate="changeBench">Trocar Bancada</button>
            </form>

            <span class="user-name"><?= $username?></span>
        </div>
    </nav>

    <div class="container">
        <div class="bench-info">
            <span data-translate="benchInfo"></span><span id="benchValue"><?= $bancada?></span>
        </div>
        <h2 data-translate="listOrders">Lista de Encomendas</h2>
        <table>
            <thead>
                <tr>
                    <th data-translate="orderId">Nº de Ordem</th>
                    <th data-translate="orderType">Tipo de Ordem</th>
                    <th data-translate="spareParts">Spare Parts</th>
                    <th data-translate="status">Estado</th>
                    <th data-translate="confirm">Confirmar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $request): ?>
                    <?php if (!empty($request["LocationDesc"])): ?>
                        <tr>
                            <td><?= $request["OrderId"] ?></td>
                            <td><?= $request["TypeDesc"] ?></td>
                            <td><?= $request["SparePart"] ?></td>
                            <td><?= $request["LocationDesc"] ?></td>
                            <td class="checkbox-container">
                                <input type="checkbox" class="confirm-checkbox" name="<?= $request["OrderId"] ?>" onclick="getLinkId(this)" 
                                <?php
                                if ($request["LocationDesc"] == "Stock Out") {
                                    echo "disabled";
                                }
                                ?>>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div id="confirmModal" class="modal">
        <form method="POST" id="confirm">
        <div class="modal-content">
            <h3 data-translate="confirmReception">Confirmar Recepção</h3>
            <p data-translate="deliveryReceived">A entrega foi recebida?</p>
            <div class="modal-buttons">
                <input id="requestId" name="requestId" value="" type="hidden">
                <button type="submit" id="confirmYes" data-translate="yes">Sim</button>
                <button id="confirmNo" data-translate="cancel">Cancelar</button>
            </div>
        </div>
        </form>
    </div>

    <script>
        function getLinkId(checkbox) {
            let link = document.getElementById('requestId'); 
            link.value = checkbox.name;
            console.log(checkbox.name);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const confirmModal = document.getElementById('confirmModal');
            const confirmYes = document.getElementById('confirmYes');
            const confirmNo = document.getElementById('confirmNo');
            let currentCheckbox = null;

            const checkboxes = document.querySelectorAll('.confirm-checkbox:not(:disabled)');
            checkboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', function(event) {
                    if (event.target.checked) {
                        currentCheckbox = event.target;
                        event.target.checked = false;
                        confirmModal.classList.add('show');
                    }
                });
            });

            confirmYes.addEventListener('click', function() {
                const form = document.getElementById('confirm');
                form.submit();

                if (currentCheckbox) {
                    currentCheckbox.checked = true;
                }
                confirmModal.classList.remove('show');
                currentCheckbox = null;
            });

            confirmNo.addEventListener('click', function(event) {
                event.preventDefault();
                confirmModal.classList.remove('show');
                currentCheckbox = null;
            });

            confirmModal.addEventListener('click', function(event) {
                if (event.target === confirmModal) {
                    confirmModal.classList.remove('show');
                    currentCheckbox = null;
                }
            });
        });

        const translations = {
            pt: {
                pageTitle: "Olympus - Encomendas",
                benchInfo: "Bancada: ",
                listOrders: "Lista de Encomendas",
                orderId: "Nº de Ordem",
                orderType: "Tipo de Ordem",
                spareParts: "Spare Parts",
                status: "Estado",
                confirm: "Confirmar",
                confirmReception: "Confirmar Recepção",
                deliveryReceived: "A entrega foi recebida?",
                yes: "Sim",
                cancel: "Cancelar",
                changeBench: "Trocar Bancada",
                langButton: "Eng/Pt"
            },
            eng: {
                pageTitle: "Olympus - Orders",
                benchInfo: "Bench: ",
                listOrders: "Orders List",
                orderId: "Order ID",
                orderType: "Order Type",
                spareParts: "Spare Parts",
                status: "Status",
                confirm: "Confirm",
                confirmReception: "Confirm Reception",
                deliveryReceived: "Has the delivery been received?",
                yes: "Yes",
                cancel: "Cancel",
                changeBench: "Change Bench",
                langButton: "Eng/Pt"
            }
        };

        let currentLang = "pt";

        function updateTranslations() {
            document.title = translations[currentLang].pageTitle;
            document.querySelectorAll("[data-translate]").forEach(el => {
                let key = el.getAttribute("data-translate");
                el.innerText = translations[currentLang][key];
            });
        }
        document.addEventListener("DOMContentLoaded", function() {
            updateTranslations();

            const langSwitch = document.querySelector(".lang-switch");
            langSwitch.addEventListener("click", function() {
                currentLang = (currentLang === "pt") ? "eng" : "pt";
                updateTranslations();
            });
        });
    </script>

</body>
</html>
