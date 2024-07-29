<?php
require 'config.php'; // Inclui a configuração da sessão
session_start();
session_unset();
session_destroy();
header('Location: /index.php');
exit;
?>
