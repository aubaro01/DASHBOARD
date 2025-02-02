<?php
require '/App/database/config.php'; // Inclui a configuração da sessão
require_once '/App/database/db.php';

// Verifica se o usuário está logado, caso contrário, redireciona para a página de login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../index.php');
    exit;
}

// Obtém os dados do usuário
function getUserData($db, $userId) {
    $sql = "SELECT USER_name, USER_email, USER_obs FROM user WHERE USER_id = ?";
    $stmt = $db->send2db($sql, [$userId]);

    if ($stmt === false) {
        die('Erro ao executar a consulta.');
    }

    return $stmt->fetch_assoc();
}

// Atualiza os dados do usuário
function updateUserData($db, $userId, $userName, $userObs) {
    $sql = "UPDATE user SET USER_name = ?, USER_obs = ? WHERE USER_id = ?";
    $stmt = $db->send2db($sql, [$userName, $userObs, $userId]);

    if ($stmt === false) {
        die('Erro ao executar a consulta de atualização.');
    }

    return $stmt;
}

// Verifica se o método de requisição é POST (ou seja, se o formulário foi submetido)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new DB(); // Instancia a classe de conexão com o banco de dados

    // Obtém os dados do formulário
    $userId = $_SESSION['id'];
    $userName = $_POST['USER_name'];
    $userObs = $_POST['USER_obs'];

    // Chama a função para atualizar os dados do usuário
    updateUserData($db, $userId, $userName, $userObs);

    // Redireciona de volta para a mesma página após a execução das operações
    header('Location: profile.php');
    exit;
}

// Obtém os dados do usuário logado
$db = new DB();
$userData = getUserData($db, $_SESSION['USER_id']);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Usuário</title>
    <link rel="stylesheet" href="/css/styledas.css">
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
                <span class="link-name">Products</span>
            </a></li>
            <li><a href="Vendas.php">
                <i class="uil uil-comments"></i>
                <span class="link-name">Sells</span>
            </a></li>
            <li><a href="#" id="USER_link">
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
        <img src="../img/people.png" alt="">
    </div>

    <div class="dash-content">
        <div class="profile-container">
            <h2>Perfil do Usuário</h2>
            <form method="post">
                <label for="USER_name">Nome:</label>
                <input type="text" id="USER_name" name="USER_name" value="<?php echo htmlspecialchars($userData['USER_name']); ?>" required>
                
                <label for="USER_email">Email:</label>
                <input type="email" id="USER_email" name="USER_email" value="<?php echo htmlspecialchars($userData['USER_email']); ?>" required>
                
                <label for="USER_pass">Senha:</label>
                <input type="password" id="USER_pass" name="USER_pass" value="<?php echo htmlspecialchars($userData['USER_pass']); ?>" required>
                
                <label for="USER_obs">Observação:</label>
                <textarea id="USER_obs" name="USER_obs" required><?php echo htmlspecialchars($userData['USER_obs']); ?></textarea>
                
                <button type="submit">Atualizar</button>
            </form>
        </div>
    </div>
</section>

<script src="/js/click.js"></script>
<script src="/js/script.js"></script>
</body>
</html>