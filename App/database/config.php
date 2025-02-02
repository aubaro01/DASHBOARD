<?php
// Verifique se a sessão já está ativa
if (session_status() == PHP_SESSION_NONE) {
    // Configurações de segurança da sessão
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.use_strict_mode', 1);

    // Inicie a sessão
    session_start();
}

$inactivityLimit = 1800; // 30 minutos

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $inactivityLimit) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}
$_SESSION['last_activity'] = time();
?>