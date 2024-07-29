<?php
session_start();
require 'config.php'; // Inclua sua configuração de sessão
require_once 'db.php';

// Verifica se o usuário está logado, caso contrário, redireciona para a página de login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../index.php');
    exit;
}

$db = new DB(); // Instancia a classe de conexão com o banco de dados

// Funções para manipulação dos produtos
function editProduct($db, $id, $nome, $quantidade, $preco) {
    $sql = "UPDATE produtos SET Nome=?, Quantidade=?, Preco=? WHERE Num_Produto=?";
    $stmt = $db->send2db($sql, [$nome, $quantidade, $preco, $id]);

    if ($stmt === false) {
        die('Erro ao executar a consulta de edição: ' . $db->error);
    }

    return $stmt;
}

function deleteProduct($db, $id) {
    $sql = "DELETE FROM produtos WHERE Num_Produto=?";
    $stmt = $db->send2db($sql, [$id]);

    if ($stmt === false) {
        die('Erro ao executar a consulta de exclusão: ' . $db->error);
    }

    return $stmt;
}

function addProduct($db, $nome, $quantidade, $preco) {
    $sql = "INSERT INTO produtos (Nome, Quantidade, Preco) VALUES (?, ?, ?)";
    $stmt = $db->send2db($sql, [$nome, $quantidade, $preco]);

    if ($stmt === false) {
        die('Erro ao executar a consulta de adição: ' . $db->error);
    }

    return $stmt;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica o tipo de ação solicitado
    switch ($_POST['action']) {
        case 'edit':
            $id = $_POST['id'];
            $nome = $_POST['nome'];
            $quantidade = $_POST['quantidade'];
            $preco = $_POST['preco'];
            editProduct($db, $id, $nome, $quantidade, $preco);
            break;
        case 'delete':
            $id = $_POST['id'];
            deleteProduct($db, $id);
            break;
        case 'add':
            $nome = $_POST['nome'];
            $quantidade = $_POST['quantidade'];
            $preco = $_POST['preco'];
            addProduct($db, $nome, $quantidade, $preco);
            break;
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

function getProducts($db) {
    $sql = "SELECT Num_Produto, Nome, Quantidade, Preco FROM produtos";
    $result = $db->send2db($sql);

    if ($result->num_rows > 0) {
        echo '<div class="products-list">';
        echo '<table class="products-table">';
        echo '<thead><tr><th>Nome</th><th>Quantidade (KG)</th><th>Preço (KG) </th><th>Ações</th></tr></thead>';
        echo '<tbody>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['Nome']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Quantidade']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Preco']) . '</td>';
            echo '<td>';
            echo '<button type="button" class="btn btn-edit" onclick="openEditModal(' . $row['Num_Produto'] . ', \'' . addslashes($row['Nome']) . '\', ' . $row['Quantidade'] . ', ' . $row['Preco'] . ')">Editar</button>';
            echo '<form method="post" style="display:inline-block;">';
            echo '<input type="hidden" name="action" value="delete">';
            echo '<input type="hidden" name="id" value="' . $row['Num_Produto'] . '">';
            echo '<button type="submit" class="btn btn-delete">Excluir</button>';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    } else {
        echo '<p>Nenhum produto encontrado.</p>';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../img/logo_icon.png">
    <link rel="stylesheet" href="/css/styledas.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <title>DasPro - Admin Panel</title>
</head>
<body>
    <nav>
        <div class="logo-name">
            <div class="logo-image">
               <img src="/img/logo_icon.png" alt="">
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
                <li><a href="#">
                    <i class="uil uil-shopping-cart-alt"></i>
                    <span class="link-name">Products</span>
                </a></li>
                <li><a href="Vendas.php">
                    <i class="uil uil-comments"></i>
                    <span class="link-name">Sells</span>
                </a></li>
                <li><a href="Profile.php" id="USER_link">
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
                <input type="text" placeholder="Nome Produto...">
            </div>
        </div>

        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h4>Products Table</h4>
                    <button id="addProductsBtn" class="btn-btn-add">Add Product</button>
                </div>
                <div class="card-body">
                    <?php getProducts($db); ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal para adicionar produtos -->
    <div id="addProductModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h4>Add New Product</h4>
            <form method="post">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <input type="text" id="nome" name="nome" placeholder="Name" required>
                </div>
                <div class="form-group">
                    <label for="quantidade">Quantidade:</label>
                    <input type="number" id="quantidade" name="quantidade" placeholder="Nome" required>
                </div>
                <div class="form-group">
                    <label for="preco">Preço:</label>
                    <input type="number" step="0.01" id="preco" name="preco" required>
                </div>
                <button type="submit" class="btn-btn-add">Add</button>
            </form>
        </div>
    </div>

    <!-- Modal para editar produtos -->
    <div id="editProductModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h4>Edit Product</h4>
            <form method="post">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" id="edit-id" name="id">
                <div class="form-group">
                    <input type="text" id="edit-nome" name="nome" placeholder="Nome" required>
                </div>
                <div class="form-group">
                    <input type="number" id="edit-quantidade" name="quantidade" placeholder="Quantidade" required>
                </div>
                <div class="form-group">
                    <input type="number" step="0.01" id="edit-preco" name="preco" placeholder="Preco" required>
                </div>
                <button type="submit" class="btn btn-edit">Salvar</button>
            </form>
        </div>
    </div>

    <script>
        // Função para abrir o modal de adicionar produto
        document.getElementById('addProductsBtn').onclick = function() {
            document.getElementById('addProductModal').style.display = 'block';
        };

        // Função para abrir o modal de editar produto
        function openEditModal(id, nome, quantidade, preco) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-nome').value = nome;
            document.getElementById('edit-quantidade').value = quantidade;
            document.getElementById('edit-preco').value = preco;
            document.getElementById('editProductModal').style.display = 'block';
        }

        // Função para fechar os modais
        document.querySelectorAll('.close').forEach(element => {
            element.onclick = function() {
                this.parentElement.parentElement.style.display = 'none';
            };
        });

        // Fecha o modal ao clicar fora do conteúdo
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        };
    </script>
    <script src="/js/click.js"></script>
    <script src="/js/script.js"></script>
</body>
</html>
