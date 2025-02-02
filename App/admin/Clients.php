<?php
require '/App/database/config.php'; // Inclui a configuração da sessão
require_once '/App/database/db.php';

// Verifica se o usuário está logado, caso contrário, redireciona para a página de login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../index.php');
    exit;
}

// Função para editar cliente
function editClient($db, $id, $nome, $contato, $contri) {
    $sql = "UPDATE clientes SET Nome=?, Contato=?, Contri=? WHERE Num_Cliente=?";
    $stmt = $db->send2db($sql, [$nome, $contato, $contri, $id]);

    // Verifica se a execução foi bem-sucedida
    if ($stmt === false) {
        die('Erro ao executar a consulta de edição.');
    }

    return $stmt;
}

// Função para excluir cliente
function deleteClient($db, $id) {
    $sql = "DELETE FROM clientes WHERE Num_Cliente=?";
    $stmt = $db->send2db($sql, [$id]);

    // Verifica se a execução foi bem-sucedida
    if ($stmt === false) {
        die('Erro ao executar a consulta de exclusão.');
    }

    return $stmt;
}

// Função para adicionar cliente
function addClient($db, $nome, $contato, $contri) {
    $sql = "INSERT INTO clientes (Nome, Contato, Contri) VALUES (?, ?, ?)";
    $stmt = $db->send2db($sql, [$nome, $contato, $contri]);

    // Verifica se a execução foi bem-sucedida
    if ($stmt === false) {
        die('Erro ao executar a consulta de adição.');
    }

    return $stmt;
}

// Verifica se o método de requisição é POST (ou seja, se o formulário foi submetido)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new DB(); // Instancia a classe de conexão com o banco de dados

    if ($_POST['action'] === 'edit') {
        // Obtém os dados do formulário
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $contato = $_POST['contato'];
        $contri = $_POST['contri'];

        // Chama a função para editar o cliente
        editClient($db, $id, $nome, $contato, $contri);
    } elseif ($_POST['action'] === 'delete') {
        // Obtém o ID do cliente a ser excluído
        $id = $_POST['id'];
        
        // Chama a função para excluir o cliente
        deleteClient($db, $id);
    } elseif ($_POST['action'] === 'add') {
        // Obtém os dados do formulário
        $nome = $_POST['nome'];
        $contato = $_POST['contato'];
        $contri = $_POST['contri'];

        // Chama a função para adicionar o cliente
        addClient($db, $nome, $contato, $contri);
    }

    // Redireciona de volta para a mesma página após a execução das operações
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Função para obter e exibir os clientes
function getClients($db) {
    $sql = "SELECT Num_Cliente, Nome, Contato, Contri FROM clientes";
    $r = $db->send2db($sql);

    // Verifica se há clientes para exibir
    if ($r->num_rows > 0) {
        echo '<div class="clients-list">';
        echo '<table class="clients-table">';
        echo '<thead><tr><th>Número Cliente</th><th>Nome</th><th>Contato</th><th>NIF</th><th>Ações</th></tr></thead>';
        echo '<tbody>';
        while ($row = $r->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['Num_Cliente']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Nome']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Contato']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Contri']) . '</td>';
            echo '<td>';
            echo '<button type="button" class="btn btn-edit" onclick="openEditModal(' . $row['Num_Cliente'] . ', \'' . addslashes($row['Nome']) . '\', \'' . addslashes($row['Contato']) . '\', \'' . addslashes($row['Contri']) . '\')">Editar</button>';
            echo '<form method="post" style="display:inline-block;">';
            echo '<input type="hidden" name="action" value="delete">';
            echo '<input type="hidden" name="id" value="' . $row['Num_Cliente'] . '">';
            echo '<button type="submit" class="btn btn-delete">Excluir</button>';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>'; // Fechar a div clients-list
    } else {
        echo '<p>No clients found.</p>';
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Clientes</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="/assest/css/styledas.css">
    <link rel="shortcut icon" href="/assest/img/logo_icon.png">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
</head>
<body>
<nav>
    <div class="logo-name">
        <div class="logo-image">
           <img src="/assest/img/logo_icon.png" alt="">
        </div>
        <span class="logo_name">DashPro</span>
    </div>

    <div class="menu-items">
        <ul class="nav-links">
            <li><a href="dash.php">
                <i class="uil uil-estate"></i>
                <span class="link-name">Home</span>
            </a></li>
            <li><a href="Clients.php">
                <i class="uil uil-users-alt"></i>
                <span class="link-name">Clients</span>
            </a></li>
            <li><a href="Produts.php">
                <i class="uil uil-shopping-cart-alt"></i>
                <span class="link-name">Products</span>
            </a></li>
            <li><a href="Vendas.php">
                <i class="uil uil-comments"></i>
                <span class="link-name">Sells</span>
            </a></li>
            <li><a href="Profile.php" id ="USER_link">
                <i class="uil uil-user"></i>
                <span class="link-name">Profile</span>
            </a></li>
        </ul>
        
        <ul class="logout-mode">
            <li><a href="logout.php">
                <i class="uil uil-signout"></i>
                <span class="link-name">Logout</span>
            </a></li>

            <li class="mode">
                <a href="#">
                    <i class="uil uil-moon"></i>
                <span class="link-name">Dark Mode</span>
            </a>

            <div class="mode-toggle">
              <span class="switch"></span>
            </div>
        </li>
        </ul>
    </div>
</nav>

<section class="dashboard">
    <div class="top">
        <i class="uil uil-bars sidebar-toggle"></i>

        <div class="search-box">
                <i class="uil uil-search"></i>
                <input type="text" placeholder="Nome Cliente...">
            </div>
    </div>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h4>Clients Table</h4>
                <!-- Botão para abrir o modal de adicionar cliente -->
                <button id="addClientBtn" class="btn-btn-add">Adicionar Cliente</button>
            </div>
            <div class="card-body">
                <?php
                $db = new DB();
                getClients($db);
                ?>
            </div>
        </div>
    </div>
</section>

<!-- Adicionar Cliente Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('addModal').style.display='none'">&times;</span>
        <h2>Adicionar Cliente</h2>
        <form method="post">
            <input type="hidden" name="action" value="add">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required><br>
            <label for="contato">Contato:</label>
            <input type="text" id="contato" name="contato" required><br>
            <label for="contri">NIF:</label>
            <input type="text" id="contri" name="contri" required><br>
            <button type="submit" class="btn btn-add">Adicionar</button>
        </form>
    </div>
</div>

<!-- Editar Cliente Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('editModal').style.display='none'">&times;</span>
        <h2>Editar Cliente</h2>
        <form method="post">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" id="edit-id" name="id">
            <label for="edit-nome">Nome:</label>
            <input type="text" id="edit-nome" name="nome" required><br>
            <label for="edit-contato">Contato:</label>
            <input type="text" id="edit-contato" name="contato" required><br>
            <label for="edit-contri">NIF:</label>
            <input type="text" id="edit-contri" name="contri" required><br>
            <button type="submit" class="btn btn-edit">Salvar</button>
        </form>
    </div>
</div>

<script>

document.addEventListener('DOMContentLoaded', function() {
    const searchBox = document.querySelector('.search-box input[type="text"]');
    searchBox.addEventListener('keyup', function() {
        const searchText = this.value.trim().toLowerCase();
        const rows = document.querySelectorAll('.clients-table tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchText)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });  });
    

    // Função para abrir o modal de adicionar cliente
    document.getElementById('addClientBtn').onclick = function() {
        document.getElementById('addModal').style.display = 'block';
    }

    // Função para abrir o modal de edição com os dados do cliente
    function openEditModal(id, nome, contato, nif) {
        document.getElementById('edit-id').value = id;
        document.getElementById('edit-nome').value = nome;
        document.getElementById('edit-contato').value = contato;
        document.getElementById('edit-contri').value = nif;
        document.getElementById('editModal').style.display = 'block';
    }

    // Fecha os modais ao clicar fora do conteúdo
    window.onclick = function(event) {
        var modals = document.getElementsByClassName('modal');
        for (var i = 0; i < modals.length; i++) {
            if (event.target == modals[i]) {
                modals[i].style.display = "none";
            }
        }
    }

    </script>
<script src="/assest/js/script.js"></script>
<script src="/assest/js/click.js"></script>
</body>
</html>