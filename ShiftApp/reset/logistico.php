<?php
session_start();


$technicians = [];
$techQuery = "SELECT id, username FROM users WHERE type = 1";
$techResult = $conn->query($techQuery);
if ($techResult) {
    while ($row = $techResult->fetch_assoc()) {
        $technicians[] = $row;
    }
}

$types = [];
$typeQuery = "SELECT * FROM locations WHERE id <200";
$typeResult = $conn->query($typeQuery);
if ($typeResult) {
    while ($row = $typeResult->fetch_assoc()) {
        $types[] = $row;
    }
} else {
    die("Error fetching types: " . $conn->error);
}

$selectedFilters = [];
if (isset($_GET['filter']) && is_array($_GET['filter'])) {
    $selectedFilters = $_GET['filter'];
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['orderNumber'])) {

  $orderId = $_POST['requestId'];
  $orderNumber = $_POST['orderNumber'];
  $type = $_POST['type'];
  $sparePart = $_POST['sparePart'];
  $tec = $_POST['tecnico'];

  $location = 200;

  $stmt = $conn->prepare("INSERT INTO pickingList (OrderId, OrderNumber, SparePart, type, tec, location) VALUES (?, ?, ?, ?, ?,200)");
  if ($stmt) {
      $stmt->bind_param("iissi", $orderId, $orderNumber, $sparePart, $type, $tec);
      if ($stmt->execute()) {
          logAction("New order: OrderId=$orderId, OrderNumber=$orderNumber, SparePart=$sparePart, Type=$type, Tecnico=$tec");
          echo "<script>alert('Registro adicionado com sucesso!');</script>";
      } else {
          echo "<script>alert('Erro ao adicionar registro: " . $stmt->error . "');</script>";
      }
      $stmt->close();
  } else {
      echo "<script>alert('Erro ao preparar a query: " . $conn->error . "');</script>";
  }
}

$typeMapping = [
    'Major'      => [100, 101, 102],
    'Medium'     => [103],
    'Minor'      => [104],
    'Surgical'   => [117],
    'Eletrical'  => [111]
];

$filterCondition = "";
if (!empty($selectedFilters) && !in_array("All", $selectedFilters)) {
    $values = [];
    foreach ($selectedFilters as $filter) {
        if (isset($typeMapping[$filter])) {
            $values = array_merge($values, $typeMapping[$filter]);
        }
    }
    if (!empty($values)) {
        $values = array_unique($values);
        $valueList = implode(",", $values);
        $filterCondition = " AND p.type IN ($valueList)";
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['orderId'])) {
        $orderId = $_POST['orderId'];
        if (!empty($orderId)) {
            $stmt = $conn->prepare("SELECT location, type FROM pickingList WHERE OrderId = ?");
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($order = $result->fetch_assoc()){
                $currentLocation = $order['location'];
                $newLocation = null;

                if ($currentLocation == 200) {
                    $newLocation = 201;
                    logAction("Order in StockOut: OrderId=$orderId");
                } elseif ($currentLocation == 201) {
                    logAction("Order arrived: OrderId=$orderId");
                    $newLocation = $order['type'];
                }

                if ($newLocation !== null) {
                    $update = $conn->prepare("UPDATE pickingList SET location = ? WHERE OrderId = ?");
                    $update->bind_param("ii", $newLocation, $orderId);
                    $update->execute();
                    $update->close();
                }
            }
            $stmt->close();
        }
    }
}

$query = "SELECT p.OrderId, p.OrderNumber, p.SparePart, t.desc AS TypeDesc, l.desc AS LocationDesc 
          FROM pickingList p
          LEFT JOIN locations l ON p.location = l.id
          LEFT JOIN locations t ON p.type = t.id
          WHERE NOT p.location >= 1000 
          AND l.id IS NOT NULL 
          $filterCondition";
$requests = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title data-translate="pageTitle">Olympus - Interface do Logístico</title>
  <link rel="stylesheet" href="css/styles.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
    integrity="sha512-pap6GJn7+U0O7vFAzJK/C8+ojy04V5sdjK7QbV5+tko1d4DY/9BGlgUJdT+S4C44r7+Dz7Vb6mRSePQssw5g+g==" 
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    
    .header-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
}

.header-container h2 {
  margin-top: 10%;
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 24px;
  margin: 0;
}

.filter-toggle {
  background: none !important;
  border: none !important;
  padding: 0 !important;
  width: 24px;
  height: 24px;
  cursor: pointer;
  margin-left: 10px; 
  transition: opacity 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.filter-toggle:hover {
  background: none !important;
  opacity: 0.8;
}

.filter-toggle:focus {
  outline: none !important;
  box-shadow: none !important;
}

.filter-toggle img {
  width: 100%;
  height: 100%;
  object-fit: cover; 
}
  
    .filter-dropdown {
      position: absolute;
      top: 60px;
      right: 20px;
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 5px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      padding: 15px;
      display: none;
      z-index: 1000;
    }
    .filter-dropdown label {
      display: block;
      margin-bottom: 8px;
      font-size: 14px;
      cursor: pointer;
    }
    .filter-dropdown input[type="checkbox"] {
      margin-right: 5px;
    }
    .filter-dropdown button {
      background: #1E3A8A;
      color: #fff;
      border: none;
      padding: 6px 10px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
      transition: background 0.3s ease;
      margin-top: 10px;
    }
    .filter-dropdown button:hover {
      background: #163172;
    }
 
    table {
      width: 100%;
      border-collapse: collapse;
    }
    table th, table td {
      padding: 10px;
      border: 1px solid #ddd;
      text-align: left;
    }
 
    .action-button {
      padding: 8px 12px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
      transition: background 0.3s ease;
    }
    .authorize {
      background: #1E3A8A;
      color: white;
    }
    .authorize:hover {
      background: #163172;
    }
    .confirm {
      background: #28a745;
      color: white;
    }
    .confirm:hover {
      background: #218838;
    }
    /* Modal de confirmação */
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      backdrop-filter: blur(5px);
      justify-content: center;
      align-items: center;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.3s ease-in-out;
    }
    .modal-content {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      text-align: center;
      width: 350px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
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
    #modalYes {
      background: #1E3A8A;
      color: white;
    }
    #modalYes:hover {
      background: #163172;
    }
    #modalNo {
      background: none;
      color: #1E3A8A;
      border: 1px solid #1E3A8A;
    }
    #modalNo:hover {
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
  
    #addRegisterModal .modal-content {
      width: 400px;
      padding: 20px;
      border-radius: 10px;
      background: #fff;
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
    #addRegisterModal h3 {
      margin-bottom: 20px;
      color: #1E3A8A;
      font-size: 20px;
      text-align: center;
    }
    #addRegisterModal label {
      display: block;
      margin-bottom: 8px;
      font-size: 14px;
      color: #333;
    }
    #addRegisterModal input[type="text"] {
      width: 100%;
      padding: 8px;
      margin-bottom: 15px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 14px;
      box-sizing: border-box;
    }
    #addRegisterModal input[type="text"]:focus {
      border-color: #1E3A8A;
      outline: none;
    }
    #addRegisterModal .modal-buttons {
      display: flex;
      justify-content: space-between;
      margin-top: 20px;
    }
    #saveRegister {
      background: #1E3A8A;
      color: white;
    }
    #saveRegister:hover {
      background: #163172;
    }
    #cancelRegister {
      background: #f0f0f0;
      color: #333;
      border: 1px solid #ddd;
    }
    #cancelRegister:hover {
      background: #ddd;
    }
    #addRegisterModal select {
      width: 100%;
      padding: 8px;
      margin-bottom: 15px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 14px;
      box-sizing: border-box;
      background-color: #fff;
      cursor: pointer;
    }
    #addRegisterModal select:focus {
      border-color: #1E3A8A;
      outline: none;
    }
  </style>
</head>
<body>

  <nav class="navbar">
    <img src="img/logo.png" alt="Olympus Logo" class="logo">
    <div class="nav-right">
      <button class="lang-switch" data-translate="langButton">
        <img src="img/World.png" alt="Idioma"> Eng/Pt
      </button>
      <span class="user-name"><?= $username ?></span>
    </div>
  </nav>

  <div class="container" style="position: relative;margin-top:100px">
    <div class="header-container">
        <h2><i class="fa-solid fa-table-list"></i> <span data-translate="listOrders">Lista de Encomendas</span></h2>
        <button class="filter-toggle" id="filterToggle">
            <img src="img/funnel.png">
        </button>
    </div>


    <div class="filter-dropdown" id="filterDropdown">
      <form method="GET" id="filterForm">
        <label>
          <input type="checkbox" name="filter[]" value="All" <?php if(empty($selectedFilters) || in_array("All", $selectedFilters)) echo "checked"; ?>>
          <span data-translate="filterAll">All</span>
        </label>
        <label>
          <input type="checkbox" name="filter[]" value="Major" <?php if(in_array("Major", $selectedFilters)) echo "checked"; ?>>
          <span data-translate="filterMajor">Major</span>
        </label>
        <label>
          <input type="checkbox" name="filter[]" value="Medium" <?php if(in_array("Medium", $selectedFilters)) echo "checked"; ?>>
          <span data-translate="filterMedium">Medium</span>
        </label>
        <label>
          <input type="checkbox" name="filter[]" value="Minor" <?php if(in_array("Minor", $selectedFilters)) echo "checked"; ?>>
          <span data-translate="filterMinor">Minor</span>
        </label>
        <label>
          <input type="checkbox" name="filter[]" value="Surgical" <?php if(in_array("Surgical", $selectedFilters)) echo "checked"; ?>>
          <span data-translate="filterSurgical">Surgical</span>
        </label>
        <label>
          <input type="checkbox" name="filter[]" value="Eletrical" <?php if(in_array("Eletrical", $selectedFilters)) echo "checked"; ?>>
          <span data-translate="filterEletrical">Eletrical</span>
        </label>
        <button type="submit" data-translate="filterButton">Filtrar</button>
      </form>
    </div>
    <empt></empt>
    <button id="addRegisterBtn" class="action-button" style="background: #4CAF50; color: white; margin-top: 10px;" data-translate="addRegisterButton">Adicionar Picking List</button>
    
    <!-- Modal para adicionar registro -->
    <div id="addRegisterModal" class="modal">
      <div class="modal-content">
          <h3 data-translate="addRegisterTitle">Adicionar Registro</h3>
          <form id="addRegisterForm" method="POST" action="">
              <label for="requestId" data-translate="requestIdLabel">Request ID:</label>
              <input type="text" id="requestId" name="requestId" required>
              
              <label for="orderNumber" data-translate="orderIdLabel">Order ID:</label>
              <input type="text" id="orderNumber" name="orderNumber" required>
              
              <label for="type" data-translate="orderTypeLabel">Tipo de Ordem:</label>
              <select id="type" name="type" required>
                  <option value="" data-translate="selectType">Selecione um tipo</option>
                  <?php foreach ($types as $type): ?>
                      <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['desc']) . " (" . $type['id'] . ")" ?></option>
                  <?php endforeach; ?>
              </select>
              
              <label for="sparePart" data-translate="sparePartsLabel">Spare Parts:</label>
              <input type="text" id="sparePart" name="sparePart" required>
              
              <label for="tecnico" data-translate="technicianLabel">Técnico:</label>
              <select id="tecnico" name="tecnico" required>
                  <option value="" data-translate="selectTechnician">Selecione um técnico</option>
                  <?php foreach ($technicians as $tech): ?>
                      <option value="<?= $tech['id'] ?>"><?= htmlspecialchars($tech['username']) ?></option>
                  <?php endforeach; ?>
              </select>
              
              <div class="modal-buttons">
                  <button type="submit" id="saveRegister" data-translate="saveRegister">Salvar</button>
                  <button type="button" id="cancelRegister" data-translate="cancelRegister">Cancelar</button>
              </div>
          </form>
      </div>
    </div>


    <table>
      <thead>
        <tr>
          <th data-translate="orderNumber">Nº de Ordem</th>
          <th data-translate="orderType">Tipo de Ordem</th>
          <th data-translate="spareParts">Spare Parts</th>
          <th data-translate="status">Estado</th>
          <th data-translate="action">Ação</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($request = $requests->fetch_assoc()):
          $actionHtml = "";
          $actionType = "";
          if ($request["LocationDesc"] == "Picking") {
              $actionType = "authorize";
              $actionHtml = '<button type="button" class="action-button authorize" data-orderid="' . $request["OrderId"] . '" data-action="authorize" data-translate="authorizeButton">Autorizar</button>';
          } elseif ($request["LocationDesc"] == "Stock Out") {
              $actionType = "confirm";
              $actionHtml = '<button type="button" class="action-button confirm" data-orderid="' . $request["OrderId"] . '" data-action="confirm" data-translate="confirmButton">Confirmar Arrival</button>';
          } else {
              $actionHtml = '<span data-translate="arrivedText">Arrived</span>';
          }
        ?>
        <tr>
          <td><?= htmlspecialchars($request["OrderNumber"]) ?></td>
          <td><?= htmlspecialchars($request["TypeDesc"]) ?></td>
          <td><?= htmlspecialchars($request["SparePart"]) ?></td>
          <td><?= htmlspecialchars($request["LocationDesc"]) ?></td>
          <td>
            <?php if ($actionType !== ""): ?>
              <form method="POST" class="action-form">
                <input type="hidden" name="orderId" value="<?= $request["OrderId"] ?>">
                <?= $actionHtml ?>
              </form>
            <?php else: ?>
              <?= $actionHtml ?>
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>


  <div id="confirmModal" class="modal">
    <div class="modal-content">
      <h3 id="modalTitle" data-translate="confirmActionTitle">Confirmar Ação</h3>
      <p id="modalMessage" data-translate="confirmActionMessage">Mensagem do modal</p>
      <div class="modal-buttons">
        <button id="modalYes" data-translate="yes">Sim</button>
        <button id="modalNo" data-translate="cancel">Cancelar</button>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
 
      const addRegisterBtn = document.getElementById('addRegisterBtn');
      const addRegisterModal = document.getElementById('addRegisterModal');
      const cancelRegister = document.getElementById('cancelRegister');
      const addRegisterForm = document.getElementById('addRegisterForm');

      addRegisterBtn.addEventListener('click', function() {
          addRegisterModal.classList.add('show');
      });

      cancelRegister.addEventListener('click', function() {
          addRegisterModal.classList.remove('show');
      });

      addRegisterForm.addEventListener('submit', function(event) {
          event.preventDefault(); 

        
          const orderId = document.getElementById('requestId');
          if (orderId.value === "") {
              alert("Por favor, insira o Request ID.");
              return;
          }

      
          const tecnico = document.getElementById('tecnico');
          if (tecnico.value === "") {
              alert("Por favor, selecione um técnico.");
              return;
          }

        
          addRegisterForm.submit();
      });

     
      const filterToggle = document.getElementById('filterToggle');
      const filterDropdown = document.getElementById('filterDropdown');
      const filterForm = document.getElementById('filterForm');
      const allCheckbox = filterForm.querySelector('input[value="All"]');
      const otherCheckboxes = filterForm.querySelectorAll('input[name="filter[]"]:not([value="All"])');

      filterToggle.addEventListener('click', function(e) {
          e.stopPropagation();
          filterDropdown.style.display = filterDropdown.style.display === 'block' ? 'none' : 'block';
      });
      
      document.addEventListener('click', function(e) {
          if (!filterDropdown.contains(e.target) && e.target !== filterToggle) {
              filterDropdown.style.display = 'none';
          }
      });

      allCheckbox.addEventListener('change', function() {
          const isChecked = allCheckbox.checked;
          otherCheckboxes.forEach(checkbox => checkbox.checked = isChecked);
      });

      otherCheckboxes.forEach(checkbox => {
          checkbox.addEventListener('change', function() {
              const allChecked = Array.from(otherCheckboxes).every(cb => cb.checked);
              allCheckbox.checked = allChecked;
          });
      });

      
      const modal = document.getElementById('confirmModal');
      const modalTitle = document.getElementById('modalTitle');
      const modalMessage = document.getElementById('modalMessage');
      const modalYes = document.getElementById('modalYes');
      const modalNo = document.getElementById('modalNo');
      let currentForm = null;

      const actionButtons = document.querySelectorAll('.action-button.authorize, .action-button.confirm');
      actionButtons.forEach(function(button) {
          button.addEventListener('click', function(event) {
              event.preventDefault();
              const actionType = this.getAttribute('data-action');
              currentForm = this.closest('form');
              
              if (actionType === 'authorize') {
                  modalTitle.textContent = translations[currentLang].confirmAuthorizationTitle;
                  modalMessage.textContent = translations[currentLang].confirmAuthorizationMessage;
              } else if (actionType === 'confirm') {
                  modalTitle.textContent = translations[currentLang].confirmArrivalTitle;
                  modalMessage.textContent = translations[currentLang].confirmArrivalMessage;
              }
              
              modal.classList.add('show');
          });
      });

      modalYes.addEventListener('click', function() {
          if (currentForm) {
              currentForm.submit();
          }
      });

      modalNo.addEventListener('click', function() {
          modal.classList.remove('show');
          currentForm = null;
      });

      modal.addEventListener('click', function(event) {
          if (event.target === modal) {
              modal.classList.remove('show');
              currentForm = null;
          }
      });
    });


    const translations = {
      pt: {
        pageTitle: "Olympus - Interface do Logístico",
        langButton: "Eng/Pt",
        listOrders: "Lista de Encomendas",
        filterAll: "All",
        filterMajor: "Major",
        filterMedium: "Medium",
        filterMinor: "Minor",
        filterSurgical: "Surgical",
        filterEletrical: "Eletrical",
        filterButton: "Filtrar",
        addRegisterButton: "Adicionar Picking List",
        addRegisterTitle: "Adicionar Registro",
        requestIdLabel: "Request ID:",
        orderIdLabel: "Order ID:",
        orderTypeLabel: "Tipo de Ordem:",
        selectType: "Selecione um tipo",
        sparePartsLabel: "Spare Parts:",
        technicianLabel: "Técnico:",
        selectTechnician: "Selecione um técnico",
        saveRegister: "Salvar",
        cancelRegister: "Cancelar",
        orderNumber: "Nº de Ordem",
        orderType: "Tipo de Ordem",
        spareParts: "Spare Parts",
        status: "Estado",
        action: "Ação",
        authorizeButton: "Autorizar",
        confirmButton: "Confirmar Arrival",
        arrivedText: "Arrived",
        confirmActionTitle: "Confirmar Ação",
        confirmActionMessage: "Mensagem do modal",
        yes: "Sim",
        cancel: "Cancelar",
        confirmAuthorizationTitle: "Confirmar Autorização",
        confirmAuthorizationMessage: "Deseja autorizar esta ordem?",
        confirmArrivalTitle: "Confirmar Chegada",
        confirmArrivalMessage: "Deseja confirmar a chegada da encomenda?"
      },
      eng: {
        pageTitle: "Olympus - Logistics Interface",
        langButton: "Eng/Pt",
        listOrders: "Orders List",
        filterAll: "All",
        filterMajor: "Major",
        filterMedium: "Medium",
        filterMinor: "Minor",
        filterSurgical: "Surgical",
        filterEletrical: "Eletrical",
        filterButton: "Filter",
        addRegisterButton: "Add Picking List",
        addRegisterTitle: "Add Record",
        requestIdLabel: "Request ID:",
        orderIdLabel: "Order ID:",
        orderTypeLabel: "Order Type:",
        selectType: "Select a type",
        sparePartsLabel: "Spare Parts:",
        technicianLabel: "Technician:",
        selectTechnician: "Select a technician",
        saveRegister: "Save",
        cancelRegister: "Cancel",
        orderNumber: "Order Number",
        orderType: "Order Type",
        spareParts: "Spare Parts",
        status: "Status",
        action: "Action",
        authorizeButton: "Authorize",
        confirmButton: "Confirm Arrival",
        arrivedText: "Arrived",
        confirmActionTitle: "Confirm Action",
        confirmActionMessage: "Modal message",
        yes: "Yes",
        cancel: "Cancel",
        confirmAuthorizationTitle: "Confirm Authorization",
        confirmAuthorizationMessage: "Do you want to authorize this order?",
        confirmArrivalTitle: "Confirm Arrival",
        confirmArrivalMessage: "Do you want to confirm the arrival of the order?"
      }
    };

    let currentLang = "pt";

    function updateTranslations() {
      document.title = translations[currentLang].pageTitle;
      document.querySelectorAll("[data-translate]").forEach(el => {
        const key = el.getAttribute("data-translate");
        if (translations[currentLang][key] !== undefined) {
          el.textContent = translations[currentLang][key];
        }
      });
    }

    document.addEventListener("DOMContentLoaded", function() {
      updateTranslations();
      const langSwitch = document.querySelector(".lang-switch");
      langSwitch.addEventListener("click", function() {
        currentLang = currentLang === "pt" ? "eng" : "pt";
        updateTranslations();
      });
    });
  </script>
</body>
</html>
