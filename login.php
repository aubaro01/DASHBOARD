<?php
session_start();

require_once 'db.php';
require_once 'funcs.php'; 

$db = new DB();

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        if (checkCredentials($db, $email, $password)) {
            $_SESSION['loggedin'] = true;
            $_SESSION['email'] = $email;

            // Obter o número do utilizador com base no email
            $sql = "SELECT USER_id FROM user WHERE USER_email = ?";
            $args = array($email);
            $result = $db->send2db($sql, $args);
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $_SESSION['USER_id'] = $row['USER_id'];
            }

            header('Location: dash.php');
            exit;
        } else {
            $error_message = "Credenciais inválidas. Por favor, tente novamente.";
        }
    } else {
        $error_message = "Por favor, preencha todos os campos.";
    }
}
?>