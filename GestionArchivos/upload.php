<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Subir múltiples archivos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <style>
        body {
            font-family: 'Times New Roman', serif;
            background: linear-gradient(to right, #4a90e2, #8e44ad);
            color: white;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
        h3.title {
            color: #fff;
        }
        p {
            color: #fff;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <?php
    define("PATH", "archivos");

    // Verificar que la matriz asociativa $_FILES["archivos"] haya sido definida
    if (isset($_FILES["archivos"])) {
        // Obtener la cantidad de archivos en $_FILES["archivos"]
        $total = count($_FILES["archivos"]["name"]);

        // Recorrer la matriz $_FILES
        for ($i = 0; $i < $total; $i++) {
            // Propiedades de cada archivo
            $tmp_name = $_FILES["archivos"]["tmp_name"][$i];
            $name = $_FILES["archivos"]["name"][$i];
            $size = $_FILES["archivos"]["size"][$i];

            // Verificar si el tamaño del archivo es mayor al permitido
            if ($size > 2621440) {
                echo "<h3>El tamaño del archivo es superior al admitido por el servidor</h3><br>";
                echo "<a href=\"uploadfile.html\">Intentar de nuevo</a>";
                continue; // Salir de este ciclo para evitar mostrar detalles del archivo
            }

            // Mostrar detalles del archivo
            echo "<h3 class=\"title\">Archivo " . ($i + 1) . ":</h3>";
            echo "<b>El nombre original:</b> " . htmlspecialchars($name) . "<br />";
            echo "<b>El nombre temporal:</b> " . htmlspecialchars($tmp_name) . "<br />";
            echo "<b>El tamaño del archivo:</b> " . number_format($size, 2) . " bytes<br />";

            // Verificar si la carpeta de destino existe, si no, crearla
            if (!file_exists(PATH)) {
                if (!mkdir(PATH, 0777, true)) {
                    die('No se ha podido crear el directorio');
                }
            }

            // Mover el archivo al directorio de destino
            if (move_uploaded_file($tmp_name, PATH . "/" . utf8_decode($name))) {
                echo "Se ha cargado correctamente el archivo <a href=\"archivos/" . urldecode($name) . "\" target=\"_blank\">" . htmlspecialchars($name) . "</a> en el servidor.<br />";
            } else {
                // Manejo de errores
                switch ($_FILES['archivos']['error'][$i]) {
                    case UPLOAD_ERR_OK:
                        echo "<p>Se ha producido un problema con la carga del archivo.</p>";
                        break;
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        echo "<p>El archivo es demasiado grande, no se puede cargar.</p>";
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        echo "<p>Sólo se ha cargado una parte del archivo.</p>";
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        echo "<p>Debe elegir un archivo para cargar.</p>";
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        echo "<p>Problema con el directorio temporal. Parece que no existe.</p>";
                        break;
                    default:
                        echo "<p>Se ha producido un problema al intentar mover el archivo " . htmlspecialchars($name) . "</p>";
                        break;
                }
            }
        }
    } else {
        echo "<h3>No se han seleccionado archivos.</h3>";
    }
    ?>
</div>

</body>
</html>
