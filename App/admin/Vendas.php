<?php
require '/App/database/config.php'; // Inclui a configuração da sessão
require_once '/App/database/db.php';
// Verifica se o usuário está logado, caso contrário, redireciona para a página de login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../index.php');
    exit;
}

// Função para editar venda
function editSale($db, $id, $cliente_fk, $valor, $obs, $dataVenda) {
    $sql = "UPDATE vendas SET Cliente_fk=?, Valor=?, Obs=?, dataVenda=? WHERE Num_Venda=?";
    $stmt = $db->send2db($sql, [$cliente_fk, $valor, $obs, $dataVenda, $id]);

    // Verifica se a execução foi bem-sucedida
    if ($stmt === false) {
        die('Erro ao executar a consulta de edição.');
    }

    return $stmt;
}

// Função para excluir venda
function deleteSale($db, $id) {
    $sql = "DELETE FROM vendas WHERE Num_Venda=?";
    $stmt = $db->send2db($sql, [$id]);

    // Verifica se a execução foi bem-sucedida
    if ($stmt === false) {
        die('Erro ao executar a consulta de exclusão.');
    }

    return $stmt;
}

// Função para adicionar venda
function addSale($db, $cliente_fk, $valor, $obs, $dataVenda) {
    $sql = "INSERT INTO vendas (Cliente_fk, Valor, Obs, dataVenda) VALUES (?, ?, ?, ?)";
    $stmt = $db->send2db($sql, [$cliente_fk, $valor, $obs, $dataVenda]);

    // Verifica se a execução foi bem-sucedida
    if ($stmt === false) {
        die('Erro ao executar a consulta de adição.');
    }

    return $stmt;
}

// Função para calcular vendas mensais
function getMonthlySales($db) {
    $sql = "SELECT DATE_FORMAT(dataVenda, '%Y-%m') as mes, SUM(Valor) as total FROM vendas GROUP BY mes ORDER BY mes DESC";
    $r = $db->send2db($sql);

    $result = [];
    while ($row = $r->fetch_assoc()) {
        $result[] = $row;
    }

    return $result;
}

// Função para calcular vendas anuais
function getYearlySales($db) {
    $sql = "SELECT DATE_FORMAT(dataVenda, '%Y') as ano, SUM(Valor) as total FROM vendas GROUP BY ano ORDER BY ano DESC";
    $r = $db->send2db($sql);

    $result = [];
    while ($row = $r->fetch_assoc()) {
        $result[] = $row;
    }

    return $result;
}

// Verifica se o método de requisição é POST (ou seja, se o formulário foi submetido)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new DB(); // Instancia a classe de conexão com o banco de dados

    if ($_POST['action'] === 'edit') {
        // Obtém os dados do formulário
        $id = $_POST['id'];
        $cliente_fk = $_POST['cliente_fk'];
        $valor = $_POST['valor'];
        $obs = $_POST['obs'];
        $dataVenda = $_POST['dataVenda'];

        // Chama a função para editar a venda
        editSale($db, $id, $cliente_fk, $valor, $obs, $dataVenda);
    } elseif ($_POST['action'] === 'delete') {
        // Obtém o ID da venda a ser excluída
        $id = $_POST['id'];
        
        // Chama a função para excluir a venda
        deleteSale($db, $id);
    } elseif ($_POST['action'] === 'add') {
        // Obtém os dados do formulário
        $cliente_fk = $_POST['cliente_fk'];
        $valor = $_POST['valor'];
        $obs = $_POST['obs'];
        $dataVenda = $_POST['dataVenda'];

        // Chama a função para adicionar a venda
        addSale($db, $cliente_fk, $valor, $obs, $dataVenda);
    }

    // Redireciona de volta para a mesma página após a execução das operações
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Verifica se o método de requisição é GET e se é uma requisição para análise
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['analysis_type'])) {
    $db = new DB(); // Instancia a classe de conexão com o banco de dados

    if ($_GET['analysis_type'] === 'monthly') {
        $result = getMonthlySales($db);
    } elseif ($_GET['analysis_type'] === 'yearly') {
        $result = getYearlySales($db);
    }

    echo json_encode($result);
    exit;
}

// Função para obter e exibir as vendas
function getSales($db) {
    $sql = "SELECT Num_Venda, Cliente_fk, Valor, Obs, dataVenda FROM vendas";
    $r = $db->send2db($sql);

    // Verifica se há vendas para exibir
    if ($r->num_rows > 0) {
        echo '<div class="sales-list">';
        echo '<table class="sales-table">';
        echo '<thead><tr><th>Número Venda</th><th>Cliente</th><th>Valor</th><th>Observação</th><th>Data da Venda</th><th>Ações</th></tr></thead>';
        echo '<tbody>';
        while ($row = $r->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['Num_Venda']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Cliente_fk']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Valor']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Obs']) . '</td>';
            echo '<td>' . htmlspecialchars($row['dataVenda']) . '</td>';
            echo '<td>';
            echo '<button type="button" class="btn btn-edit" onclick="openEditModal(' . $row['Num_Venda'] . ', \'' . addslashes($row['Cliente_fk']) . '\', \'' . $row['Valor'] . '\', \'' . addslashes($row['Obs']) . '\', \'' . $row['dataVenda'] . '\')">Editar</button>';
            echo '<form method="post" style="display:inline-block;">';
            echo '<input type="hidden" name="action" value="delete">';
            echo '<input type="hidden" name="id" value="' . $row['Num_Venda'] . '">';
            echo '<button type="submit" class="btn btn-delete">Excluir</button>';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>'; // Fechar a div sales-list
    } else {
        echo '<p>No sales found.</p>';
    }
}

// Função para buscar clientes com base no termo
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search_term'])) {
    $db = new DB();
    $search_term = $_GET['search_term'];
    $sql = "SELECT Cliente_id, Nome FROM clientes WHERE Nome LIKE ?";
    $r = $db->send2db($sql, ["%$search_term%"]);

    $clientes = [];
    while ($row = $r->fetch_assoc()) {
        $clientes[] = $row;
    }

    echo json_encode($clientes);
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Vendas</title>
    <link rel="stylesheet" href="/css/styledas.css">
    <link rel="shortcut icon" href="../img/logo_icon.png">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
</head>
<body>
<nav>
    <div class="logo-name">
        <div class="logo-image">
           <img src="../img/logo_icon.png" alt="">
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
                <span class="link-name">Produc  ts</span>
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
            <input type="text" placeholder="Search here...">
        </div>
        
        <img src="../img/people.png" alt="">
    </div>

    <div class="dash-content">

        <div class="button-container">
            <button id="addSaleBtn" class="btn-btn-addP">Adicionar Venda</button>
            <button id="monthlyAnalysisBtn" class="btn-btn-analysis">Análise Mensal</button>
            <button id="yearlyAnalysisBtn" class="btn-btn-analysis">Análise Anual</button>
        </div>

        <?php
        $db = new DB(); // Instancia a classe de conexão com o banco de dados
        getSales($db); // Chama a função para exibir as vendas
        ?>
    </div>
    
    <div id="analysisResults" class="analysis-results"></div>
</section>

<!-- Adicionar Venda Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('addModal').style.display='none'">&times;</span>
        <h2>Adicionar Venda</h2>
        <form method="post">
            <input type="hidden" name="action" value="add">
            <label for="cliente_fk">Cliente:</label>
            <input type="text" id="cliente_fk" name="cliente_fk" required>
            <div id="clienteSuggestions"></div><br>
            <label for="valor">Valor:</label>
            <input type="text" id="valor" name="valor" required><br>
            <label for="obs">Observação:</label>
            <input type="text" id="obs" name="obs" required><br>
            <label for="dataVenda">Data da Venda:</label>
            <input type="date" id="dataVenda" name="dataVenda" required><br>
            <button type="submit" class="btn-btn-add">Adicionar</button>
        </form>
    </div>
</div>

<!-- Editar Venda Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('editModal').style.display='none'">&times;</span>
        <h2>Editar Venda</h2>
        <form method="post">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" id="edit-id" name="id">
            <label for="edit-cliente_fk">Cliente:</label>
            <input type="text" id="edit-cliente_fk" name="cliente_fk" required>
            <div id="editClienteSuggestions"></div><br>
            <label for="edit-valor">Valor:</label>
            <input type="text" id="edit-valor" name="valor" required><br>
            <label for="edit-obs">Observação:</label>
            <input type="text" id="edit-obs" name="obs" required><br>
            <label for="edit-dataVenda">Data da Venda:</label>
            <input type="date" id="edit-dataVenda" name="dataVenda" required><br>
            <button type="submit" class="btn btn-edit">Salvar</button>
        </form>
    </div>
</div>

<script>
// Filtra a tabela de vendas
document.addEventListener('DOMContentLoaded', function() {
    const searchBox = document.querySelector('.search-box input[type="text"]');
    searchBox.addEventListener('keyup', function() {
        const searchText = this.value.trim().toLowerCase();
        const rows = document.querySelectorAll('.sales-table tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchText)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Função para abrir o modal de adicionar venda
    document.getElementById('addSaleBtn').onclick = function() {
        document.getElementById('addModal').style.display = 'block';
    }

    // Função para abrir o modal de edição com os dados da venda
    window.openEditModal = function(id, cliente_fk, valor, obs, dataVenda) {
        document.getElementById('edit-id').value = id;
        document.getElementById('edit-cliente_fk').value = cliente_fk;
        document.getElementById('edit-valor').value = valor;
        document.getElementById('edit-obs').value = obs;
        document.getElementById('edit-dataVenda').value = dataVenda;
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

    // Função para buscar clientes enquanto digita no campo de adicionar venda
    document.getElementById('cliente_fk').addEventListener('input', function() {
        const search_term = this.value.trim();
        if (search_term.length > 2) {
            fetch('/Vendas.php?search_term=' + search_term)
                .then(response => response.json())
                .then(data => {
                    let suggestions = '';
                    data.forEach(cliente => {
                        suggestions += `<div onclick="selectCliente('${cliente.Cliente_id}', '${cliente.Nome}')">${cliente.Nome}</div>`;
                    });
                    document.getElementById('clienteSuggestions').innerHTML = suggestions;
                });
        } else {
            document.getElementById('clienteSuggestions').innerHTML = '';
        }
    });

    // Função para buscar clientes enquanto digita no campo de editar venda
    document.getElementById('edit-cliente_fk').addEventListener('input', function() {
        const search_term = this.value.trim();
        if (search_term.length > 2) {
            fetch('/Vendas.php?search_term=' + search_term)
                .then(response => response.json())
                .then(data => {
                    let suggestions = '';
                    data.forEach(cliente => {
                        suggestions += `<div onclick="selectEditCliente('${cliente.Cliente_id}', '${cliente.Nome}')">${cliente.Nome}</div>`;
                    });
                    document.getElementById('editClienteSuggestions').innerHTML = suggestions;
                });
        } else {
            document.getElementById('editClienteSuggestions').innerHTML = '';
        }
    });

    // Função para selecionar cliente ao adicionar venda
    window.selectCliente = function(id, nome) {
        document.getElementById('cliente_fk').value = nome;
        document.getElementById('clienteSuggestions').innerHTML = '';
    }

    // Função para selecionar cliente ao editar venda
    window.selectEditCliente = function(id, nome) {
        document.getElementById('edit-cliente_fk').value = nome;
        document.getElementById('editClienteSuggestions').innerHTML = '';
    }
});
</script>

<script src="/js/click.js"></script>
<script src="/js/script.js"></script>
</body>
</html>
