<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nombre' => $_POST['nombre'] ?? '',
        'email' => $_POST['email'] ?? '',
        'mensaje' => $_POST['mensaje'] ?? '',
        'fecha' => date('Y-m-d H:i:s')
    ];

    // Ruta al archivo JSON
    $file = __DIR__ . '/../data/contactos.json';

    // Leer el contenido actual (si existe)
    $contactos = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

    // Añadir nuevo mensaje
    $contactos[] = $data;

    // Guardar de nuevo el archivo
    file_put_contents($file, json_encode($contactos, JSON_PRETTY_PRINT));

    echo "<p>Gracias por contactarnos, " . htmlspecialchars($data['nombre']) . ".</p>";
    echo "<a href=\"/contact\">Volver al formulario</a>";
  
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contacto</title>
</head>
<body>
    <h1>Contáctanos</h1>
    <form method="POST" action="/contact">
        <label>Nombre: <input type="text" name="nombre" required></label><br>
        <label>Email: <input type="email" name="email" required></label><br>
        <label>Mensaje: <textarea name="mensaje" required></textarea></label><br>
        <button type="submit">Enviar</button>
    </form>
</body>
</html>

<hr>
<h2>Mensajes recibidos</h2>

<?php
$file = __DIR__ . '/../data/contactos.json';

if (file_exists($file)) {
    $mensajes = json_decode(file_get_contents($file), true);

    if (!empty($mensajes)) {
        echo "<ul>";
        foreach ($mensajes as $m) {
            echo "<li><strong>" . htmlspecialchars($m['nombre']) . "</strong> (" . htmlspecialchars($m['email']) . ") dijo:<br>";
            echo nl2br(htmlspecialchars($m['mensaje'])) . "<br><em>" . $m['fecha'] . "</em></li><br>";
        }
        echo "</ul>";
    } else {
        echo "<p>No hay mensajes aún.</p>";
    }
} else {
    echo "<p>No se ha recibido ningún mensaje todavía.</p>";
}
?>
