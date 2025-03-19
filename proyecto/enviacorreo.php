<?php
require "funciones/conecta.php";

// Función para enviar correo
function send_email($subject, $body, $to_email) {
    $from_email = "webtrabajos0@gmail.com";
    $from_password = "oits qeyf hygn zhoz";  // Contraseña de la aplicación

    // Crear el mensaje
    $msg = new \PHPMailer\PHPMailer\PHPMailer();
    $msg->setFrom($from_email, 'Web Trabajos');
    $msg->addAddress($to_email);
    $msg->Subject = $subject;
    $msg->Body    = $body;

    // Conexión al servidor SMTP
    try {
        $msg->isSMTP();
        $msg->Host = 'smtp.gmail.com';
        $msg->SMTPAuth = true;
        $msg->Username = $from_email;
        $msg->Password = $from_password;
        $msg->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $msg->Port = 587;

        // Enviar el correo
        $msg->send();
        echo "Correo enviado a " . $to_email . "<br>";
    } catch (Exception $e) {
        echo "Error al enviar el correo: " . $e->getMessage() . "<br>";
    }
}

// Función para obtener correos no vistos de la base de datos y enviar correos
function send_emails_from_db() {
    // Conexión a la base de datos
    $con = conecta();  // Usamos la función conecta para obtener la conexión

    // Obtener correos no vistos
    $query = "SELECT correo FROM contacto WHERE visto = 0";
    $result = $con->query($query);

    if ($result->num_rows > 0) {
        // Enviar correo a cada correo no visto
        while ($row = $result->fetch_assoc()) {
            // Enviar correo
            $subject = "Estamos en contacto contigo";
            $body = "Gracias por ponerte en contacto con nosotros. Te responderemos lo antes posible.";
            send_email($subject, $body, $row['correo']);

            // Actualizar el estado de "visto" a 1
            $update_query = "UPDATE contacto SET visto = 1 WHERE correo = ?";
            $stmt = $con->prepare($update_query);
            $stmt->bind_param("s", $row['correo']);
            $stmt->execute();
        }
        echo "Se enviaron correos a todos los destinatarios no vistos.";
    } else {
        echo "No hay correos pendientes por enviar.";
    }

    // Cerrar la conexión
    $con->close();
}

// Ejecutar la función
send_emails_from_db();
?>
