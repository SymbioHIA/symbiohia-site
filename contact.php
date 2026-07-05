<?php
header('Content-Type: application/json; charset=utf-8');

$to = 'contacto@symbiohia.com.ar';
$replyTo = isset($_POST['email']) ? trim($_POST['email']) : '';
$subject = 'Nueva consulta desde el sitio web';

$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$empresa = isset($_POST['empresa']) ? trim($_POST['empresa']) : '';
$mensaje = isset($_POST['mensaje']) ? trim($_POST['mensaje']) : '';
$website = isset($_POST['website']) ? trim($_POST['website']) : '';

if (!empty($website)) {
    echo json_encode(['success' => false, 'message' => 'Solicitud inválida.']);
    exit;
}

if ($nombre === '' || $email === '' || $mensaje === '') {
    echo json_encode(['success' => false, 'message' => 'Por favor completá tu nombre, correo y mensaje.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'El correo ingresado no es válido.']);
    exit;
}

$body = "Nueva consulta recibida desde el sitio web\n\n";
$body .= "Nombre: $nombre\n";
$body .= "Correo: $email\n";
$body .= "Empresa: " . ($empresa !== '' ? $empresa : 'No informada') . "\n";
$body .= "Mensaje:\n$mensaje\n";
$body .= "\nFecha: " . date('Y-m-d H:i:s') . "\n";

$headers = [];
$headers[] = 'From: SymbioHIA <no-reply@symbiohia.com.ar>';
$headers[] = 'Reply-To: ' . $replyTo;
$headers[] = 'X-Mailer: PHP/' . phpversion();
$headers[] = 'Content-Type: text/plain; charset=UTF-8';

$sentToTeam = mail($to, $subject, $body, implode("\r\n", $headers));

if ($sentToTeam) {
    $autoReplySubject = 'Recibimos tu consulta en SymbioHIA';
    $autoReplyBody = "Hola $nombre,\n\n";
    $autoReplyBody .= "Gracias por escribirnos. Recibimos tu consulta y la estaremos revisando.\n";
    $autoReplyBody .= "Te responderemos dentro de las 24 horas siguientes a la recepción de tu mensaje.\n\n";
    $autoReplyBody .= "Saludos,\n";
    $autoReplyBody .= "Equipo SymbioHIA\n";
    $autoReplyBody .= "contacto@symbiohia.com.ar\n";

    $autoReplyHeaders = [];
    $autoReplyHeaders[] = 'From: SymbioHIA <contacto@symbiohia.com.ar>';
    $autoReplyHeaders[] = 'Reply-To: contacto@symbiohia.com.ar';
    $autoReplyHeaders[] = 'Content-Type: text/plain; charset=UTF-8';

    mail($email, $autoReplySubject, $autoReplyBody, implode("\r\n", $autoReplyHeaders));

    echo json_encode(['success' => true, 'message' => 'Tu consulta fue enviada correctamente. Te responderemos dentro de 24 horas.']);
} else {
    echo json_encode(['success' => false, 'message' => 'No pudimos enviar tu consulta en este momento.']);
}
?>