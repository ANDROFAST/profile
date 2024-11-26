<?php
session_start(); // Inicia una sesión para manejar el token CSRF

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si el token CSRF existe y es válido
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        echo "Solicitud rechazada por token CSRF inválido.";
        exit;
    }

    // Validar y sanitizar los datos del formulario
    $name = htmlspecialchars(trim($_POST['full-name']));
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        echo "Todos los campos son obligatorios.";
        exit;
    }

    // Limitar longitud de los campos
    if (strlen($name) > 50 || strlen($subject) > 100 || strlen($message) > 500) {
        echo "Los datos exceden el límite permitido.";
        exit;
    }

    // Configuración del correo
    $to = "alexcespedessanchez@gmail.com"; // Cambia a tu correo
    $headers = "From: $email" . "\r\n" .
               "Reply-To: $email" . "\r\n" .
               "X-Mailer: PHP/" . phpversion();

    $email_message = "Name: $name\n";
    $email_message .= "Email: $email\n";
    $email_message .= "Subject: $subject\n";
    $email_message .= "Message:\n$message\n";

    // Enviar el correo
    if (mail($to, $subject, $email_message, $headers)) {
        echo "Mensaje enviado con éxito.";
    } else {
        error_log("Error al enviar el correo desde: $email");
        echo "Error al enviar el mensaje. Intenta nuevamente.";
    }
} else {
    echo "Método de solicitud inválido.";
}
?>
