<?php
// procesar.php

require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // === RECEPCIÓN DE DATOS ===
    $nombre           = trim($_POST['nombre']);
    $apellidoPaterno  = trim($_POST['apellidoPaterno']);
    $apellidoMaterno  = trim($_POST['apellidoMaterno']);
    $fechaNacimiento  = trim($_POST['fechaNacimiento']);
    $lugarNacimiento  = trim($_POST['lugarNacimiento']);
    $direccionActual  = trim($_POST['direccionActual']);
    $sexo             = trim($_POST['sexo']);
    $correo           = trim($_POST['correo']);
    $curp             = trim($_POST['curp']);
    $rfc              = trim($_POST['rfc']);
    $fechaRegistroStr = trim($_POST['fechaRegistro']); // Formato DD/MM/YYYY HH:mm:ss

    // === VALIDACIÓN DE DATOS ===
    $errores = [];

    if (empty($nombre)) {
        $errores[] = "El nombre es obligatorio.";
    }

    if (empty($apellidoPaterno)) {
        $errores[] = "El apellido paterno es obligatorio.";
    }

    if (empty($apellidoMaterno)) {
        $errores[] = "El apellido materno es obligatorio.";
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Correo electrónico inválido.";
    }

    // Validación de CURP: 16 o 18 caracteres alfanuméricos
    if (!preg_match("/^[A-Z0-9]{16,18}$/", $curp)) {
        $errores[] = "La CURP tiene un formato inválido.";
    }

    // Validación de RFC: 12 o 13 caracteres alfanuméricos
    if (!preg_match("/^[A-Z0-9]{12,13}$/", $rfc)) {
        $errores[] = "El RFC tiene un formato inválido.";
    }

    // Validación de fecha_registro: DD/MM/YYYY HH:mm:ss
    if (!preg_match("/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/\d{4} (\d{2}:\d{2}:\d{2})$/", $fechaRegistroStr)) {
        $errores[] = "La fecha de registro no tiene el formato válido (DD/MM/YYYY HH:mm:ss)";
    }

    if (!empty($errores)) {
        mostrarRespuesta(false, "Errores en el formulario:", $errores);
        exit;
    }

    // === CONVERTIR FECHA A FORMATO MYSQL DATETIME ===
    list($fechaParte, $horaParte) = explode(' ', $fechaRegistroStr);
    list($dia, $mes, $anio) = explode('/', $fechaParte);
    $fechaRegistroMySQL = "$anio-$mes-$dia $horaParte"; // YYYY-MM-DD HH:mm:ss

    try {
        // === PREPARAR LA INSERCIÓN ===
        $stmt = $pdo->prepare("
            INSERT INTO visitante (
                nombre,
                apellido_paterno,
                apellido_materno,
                fecha_nacimiento,
                lugar_nacimiento,
                direccion_actual,
                sexo,
                correo_electronico,
                curp,
                rfc,
                fecha_registro
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        // Ejecutar consulta
        $stmt->execute([
            $nombre,
            $apellidoPaterno,
            $apellidoMaterno,
            $fechaNacimiento,
            $lugarNacimiento,
            $direccionActual,
            $sexo,
            $correo,
            $curp,
            $rfc,
            $fechaRegistroMySQL
        ]);

        // === MOSTRAR MENSAJE DE ÉXITO CON BOTONES ADICIONALES ===
        mostrarRespuesta(true, "¡Registro exitoso!", [
            "Nombre: $nombre",
            "CURP: $curp",
            "RFC: $rfc",
            "Fecha y hora de registro: $fechaRegistroStr"
        ]);

    } catch (PDOException $e) {
        mostrarRespuesta(false, "Error al guardar los datos:", ["No se pudo insertar el registro en la base de datos."]);
    }
}

/**
 * Muestra una respuesta amigable al usuario
 */
function mostrarRespuesta($exito, $titulo, $mensajes) {
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <title>" . ($exito ? "Éxito" : "Error") . "</title>
        <style>
            body { font-family: Arial, sans-serif; padding: 40px; background-color: #f4f4f4; text-align: center; }
            h2 { color: " . ($exito ? "#27ae60" : "#e74c3c") . "; }
            ul { list-style-type: none; padding: 0; margin-top: 20px; display: inline-block; text-align: left; }
            li { margin: 5px 0; }
            .acciones {
                margin-top: 30px;
            }
            .btn {
                display: inline-block;
                padding: 10px 20px;
                margin: 0 10px;
                background-color: #007BFF;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                transition: background-color 0.3s ease;
            }
            .btn:hover {
                background-color: #0056b3;
            }
            .btn-error {
                background-color: #e74c3c;
            }
            .btn-error:hover {
                background-color: #c0392b;
            }
        </style>
    </head>
    <body>
        <h2>$titulo</h2>
        <ul>";

    foreach ($mensajes as $mensaje) {
        echo "<li>$mensaje</li>";
    }

    echo "</ul><div class='acciones'>";
    echo "<a href='index.html' class='btn'>VOLVER AL FORMULARIO</a>";

    if ($exito) {
        echo "<a href='listar.php' class='btn'>VER LISTADO DE VISITANTES</a>";
    } else {
        echo "<a href='index.html' class='btn btn-error'>Regresar y corregir</a>";
    }

    echo "</div></body></html>";
    exit;
}