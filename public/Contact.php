<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Verifique se o endereço de e-mail é válido
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Endereço de email inválido.";
        exit;
    }

    // Obter a data e hora atual
    $currentDateTime = date("Y-m-d H:i:s");

    // Formatar os dados com a data e hora
    $data = "Date: $currentDateTime\nName: $name\nEmail: $email\nMessage: $message\n\n";
    file_put_contents('contacts.txt', $data, FILE_APPEND);

    $to = "dashpro360@gmail.com";
    $subject = "New Contact Message from $name";
    $headers = "From: $email";

    if (mail($to, $subject, $message, $headers)) {
        echo "Mensagem enviada com sucesso!";
    } else {
        echo "Erro ao enviar mensagem.";
    }

    header("Location: thans.php");
    exit;
} else {
    echo "Método de solicitação inválido.";
}
?>
