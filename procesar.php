<?php
// procesar.php

require_once 'config.php';

// === RECEPCIÓN DE DATOS DEL FORMULARIO ===
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Obtener y limpiar los datos del formulario
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
    $fechaRegistro    = trim($_POST['fechaRegistro']);

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

    if (!preg_match("/^[A-Z]{4}[0-9]{6}[HL][A-Z]{2}[0-9]{3}[A-Z][0-9]$/", $curp)) {
        $errores[] = "La CURP tiene un formato inválido.";
    }

    if (!preg_match("/^[A-Z]{4}[0-9]{6}[A-Z]{3}$/", $rfc)) {
        $errores[] = "El RFC tiene un formato inválido.";
    }

    if (!preg_match("/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/\d{4} (?:[01]\d|2[0-3]):(?:[0-5]\d):(?:[0-5]\d)$/", $fechaRegistro)) {
        $errores[] = "La fecha de registro no tiene el formato correcto.";
    }

    if (!empty($errores)) {
        mostrarRespuesta(false, "Errores en el formulario:", $errores);
        exit;
    }

    // === CONVERTIR FECHA_REGISTRO A DATETIME ===
    list($fechaParte, $horaParte) = explode(' ', $fechaRegistro);
    list($dia, $mes, $anio) = explode('/', $fechaParte);
    $fechaRegistroMySQL = "$anio-$mes-$dia $horaParte";

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

        // === RESPUESTA EXITOSA ===
        mostrarRespuesta(true, "¡Registro exitoso!", [
            "Nombre: $nombre",
            "CURP: $curp",
            "RFC: $rfc"
        ]);

    } catch (PDOException $e) {
        mostrarRespuesta(false, "Error al guardar los datos:", ["No se pudo insertar el registro en la base de datos."]);
    }
}

function mostrarRespuesta($exito, $titulo, $mensajes) {
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <title>" . ($exito ? "Éxito" : "Error") . "</title>
        <style>
            body { font-family: Arial, sans-serif; padding: 40px; background-color: #f4f4f4; text-align: center; }
            h2 { color: " . ($exito ? "#27ae60" : "#e74c3c") . "; }
            ul { list-style-type: none; padding: 0; }
            li { margin: 5px 0; }
            a { display: inline-block; margin-top: 20px; text-decoration: none; color: white; background-color: #007BFF; padding: 10px 20px; border-radius: 5px; }
            a:hover { background-color: #0056b3; }
        </style>
    </head>
    <body>
        <h2>$titulo</h2>
        <ul>";
    foreach ($mensajes as $mensaje) {
        echo "<li>$mensaje</li>";
    }
    echo "</ul><a href='index.html'>Volver al formulario</a></body></html>";
    exit;
}