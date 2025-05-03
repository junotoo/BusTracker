<?php

session_start();
include '../inc/db.inc.php'; 
$userQuery = "SELECT u.username, t.desc AS type_desc 
              FROM users u
              LEFT JOIN types t ON u.type = t.id";
$userResult = $conn->query($userQuery);
if ($userResult) {
    while ($row = $userResult->fetch_assoc()) {
        $users[] = $row;
    }
} else {
    die("Error fetching users: " . $conn->error);
}

$query = "SELECT p.OrderId, p.OrderNumber, p.SparePart, t.desc AS TypeDesc, l.desc AS LocationDesc, p.location AS LocationId 
          FROM pickingList p
          LEFT JOIN locations l ON p.location = l.id
          LEFT JOIN locations t ON p.type = t.id;";
$requests = $conn->query($query);
$types = [];
$typeQuery = "SELECT * FROM types";
$typeResult = $conn->query($typeQuery);
if ($typeResult) {
    while ($row = $typeResult->fetch_assoc()) {
        $types[] = $row;
    }
} else {
    die("Error fetching types: " . $conn->error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['create_account'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $type_id = trim($_POST['type_id']);

    if (empty($username) || empty($password) || empty($confirm_password) || empty($type_id)) {
        $error_message = 'Todos os campos são obrigatórios!';
    } elseif ($password !== $confirm_password) {
        $error_message = 'As senhas não coincidem!';
    } else {
        $stmt = $conn->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $error_message = 'Nome de usuário já existe!';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('INSERT INTO users (username, passwd, type) VALUES (?, ?, ?)');
            $stmt->bind_param('ssi', $username, $hashed_password, $type_id);
            if ($stmt->execute()) {
                $success_message = 'Conta criada com sucesso!';
                if ($type_id==1) $tp="Técnico";else $tp="Logística";
                logAction("New account created: username=$username, type=$tp");
            } else {
                $error_message = 'Erro ao criar conta!';
            }
        }
    }
}
?><!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Olympus - Interface do Logístico</title>
  <link rel="stylesheet" href="../css/styles.css">
  <!-- Font Awesome CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    /* Modal styles modernos, elegantes em azul e branco */
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 50, 0.6);
      backdrop-filter: blur(4px);
      justify-content: center;
      align-items: center;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.3s ease;
    }
    .modal.show {
      display: flex;
      opacity: 1;
      pointer-events: auto;
    }
    .modal-content {
      background: #fff;
      border-radius: 10px;
      padding: 30px;
      width: 400px;
      box-shadow: 0 8px 16px rgba(0, 0, 50, 0.2);
      border-top: 4px solid #1E90FF;
      text-align: center;
      animation: slideDown 0.4s ease;
    }
    @keyframes slideDown {
      from {
        transform: translateY(-20px);
        opacity: 0;
      }
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }
    .modal-content h3 {
      margin-bottom: 20px;
      color: #1E90FF;
    }
    .modal-content label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
      color: #333;
      text-align: left;
    }
    .modal-content input[type="text"],
    .modal-content input[type="password"],
    .modal-content select {
      width: 100%;
      padding: 8px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 5px;
      box-sizing: border-box;
    }
    .modal-content button {
      padding: 10px 20px;
      margin: 5px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
      transition: background-color 0.3s ease;
    }
    .btn-primary {
      background-color: #1E90FF;
      color: #fff;
    }
    .btn-primary:hover {
      background-color: #187bcd;
    }
    .btn-secondary {
      background-color: #fff;
      color: #1E90FF;
      border: 1px solid #1E90FF;
    }
    .btn-secondary:hover {
      background-color: #e6f2ff;
    }
    .error-message {
      color: #D8000C;
      margin-bottom: 10px;
    }
    .success-message {
      color: #4F8A10;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>

  <nav class="navbar">
    <img src="img/logo.png" alt="Olympus Logo" class="logo">
    <div class="nav-right">

      <span class="user-name">ADMIN</span>
    </div>
  </nav>

  <div class="container" style="position: relative;margin-top:750px">
    <div class="header-container">
        <h2><i class="fa-solid fa-table-list"></i> Lista de Encomendas</h2>
    </div>

    <button id="createAccountBtn" onclick="document.getElementById('accountModal').classList.add('show')"class="action-button" style="background: #1E90FF; color: white; margin-top: 10px;">Criar Conta</button>
    <button id="exportCsvBtn" class="action-button" style="background: #28a745; color: white; margin-top: 10px;">Exportar para CSV</button>
    <div id="accountModal" class="modal">
      <div class="modal-content">
        <h3>Criar Conta</h3>
        <?php if (isset($error_message)): ?>
          <div class="error-message"><?= $error_message ?></div>
        <?php endif; ?>
        <?php if (isset($success_message)): ?>
          <div class="success-message"><?= $success_message ?></div>
        <?php endif; ?>
        <form method="POST">
          <label for="username">Nome de Usuário:</label>
          <input type="text" id="username" name="username" class="form-control" required>
          <label for="password">Senha:</label>
          <input type="password" id="password" name="password" class="form-control" required>
          <label for="confirm_password">Confirmar Senha:</label>
          <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
          <label for="type_id">Tipo:</label>
          <select id="type_id" name="type_id" class="form-control" required>
            <option value="">Selecione um tipo</option>
            <?php foreach ($types as $type): ?>
              <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['desc']) ?></option>
            <?php endforeach; ?>
          </select>
          <button type="submit" name="create_account" class="btn-primary">Criar Conta</button>
          <button type="button" class="btn-secondary" onclick="closeAccountModal()">Cancelar</button>
        </form>
      </div>
    </div>

    <!-- Tabela de Encomendas -->
    <table id = "exp">
      <thead>
        <tr>
          <th>Nº de Ordem</th>
          <th>Tipo de Ordem</th>
          <th>Spare Parts</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($request = $requests->fetch_assoc()):
          $actionHtml = "";
          $actionType = "";
          if ($request["LocationDesc"] == "Picking") {
              $actionType = "authorize";
              $actionHtml = "Picking";
          } elseif ($request["LocationDesc"] == "Stock Out") {
              $actionType = "confirm";
              $actionHtml = "Stock Out";
          } else {
              $actionHtml = "Arrived";
          }
        ?>
        <tr>
          <td><?= htmlspecialchars($request["OrderNumber"]) ?></td>
          <td><?= htmlspecialchars($request["TypeDesc"]) ?></td>
          <td><?= htmlspecialchars($request["SparePart"]) ?></td>
          <td><?= $request["LocationDesc"] ?? $request["LocationId"] ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <h3 style="margin-top: 20px;"><i class="fa-solid fa-users"></i> Lista de Utilizadores</h3>
    <table class="user-table">
      <thead>
        <tr>
          <th>Nome de Utilizador</th>
          <th>Tipo</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
          <td><?= htmlspecialchars($user['username']) ?></td>
          <td><?= htmlspecialchars($user['type_desc']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <script>
    document.getElementById('createAccountBtn').addEventListener('click', function() {
      document.getElementById('accountModal').classList.add('show');
    });

    function closeAccountModal() {
      document.getElementById('accountModal').classList.remove('show');
    }
    document.addEventListener('DOMContentLoaded', function () {
  // Existing code for the "Criar Conta" button
  const createAccountBtn = document.getElementById('createAccountBtn');
  if (createAccountBtn) {
    createAccountBtn.addEventListener('click', function () {
      document.getElementById('accountModal').classList.add('show');
    });
  }

  // Existing code for the "Cancelar" button
  const cancelBtn = document.querySelector('.btn-secondary');
  if (cancelBtn) {
    cancelBtn.addEventListener('click', function () {
      document.getElementById('accountModal').classList.remove('show');
    });
  }

  // New code for the "Exportar para CSV" button
  const exportCsvBtn = document.getElementById('exportCsvBtn');
  if (exportCsvBtn) {
    exportCsvBtn.addEventListener('click', function () {
      exportTableToCsv('table', 'encomendas.csv');
    });
  }

  
  function exportTableToCsv(tableId, filename) {
    tableId =  "exp";
    filename = "estados.csv";
    const table = document.getElementById(tableId);
    if (!table) {
      console.error('Table not found!');
      return;
    }

    // Get table rows
    const rows = table.querySelectorAll('tr');

    // Extract headers
    const headers = [];
    const headerCells = rows[0].querySelectorAll('th');
    headerCells.forEach(cell => {
      headers.push(cell.innerText.trim());
    });

    // Extract data rows
    const data = [];
    for (let i = 1; i < rows.length; i++) {
      const row = [];
      const cells = rows[i].querySelectorAll('td');
      cells.forEach(cell => {
        row.push(cell.innerText.trim());
      });
      data.push(row);
    }

    // Convert to CSV format
    let csvContent = headers.join(',') + '\n'; // Add headers
    data.forEach(row => {
      csvContent += row.join(',') + '\n'; // Add data rows
    });

    // Create a Blob and trigger download
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }
});
  </script>
</body>
</html>