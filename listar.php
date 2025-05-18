<?php
// listar.php

require_once 'config.php';

try {
    // Consulta para obtener todos los registros
    $stmt = $pdo->query("SELECT * FROM visitante ORDER BY creado_en DESC");
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al consultar los datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Visitantes</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Contenedor específico para listado */
        .tabla-wrapper {
            background-color: white;
            margin: 40px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 95%;
            max-width: 1200px;
        }

        /* Tabla general */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th, td {
            text-align: left;
            word-wrap: break-word;
            white-space: normal;
            vertical-align: middle;
            padding: 10px;
        }

        /* Anchura específica de columnas */
        colgroup col:nth-child(1) { width: 3%; }   /* ID */
        colgroup col:nth-child(2) { width: 10%; }  /* Nombre */
        colgroup col:nth-child(3) { width: 12%; }  /* Apellido Paterno */
        colgroup col:nth-child(4) { width: 12%; }  /* Apellido Materno */
        colgroup col:nth-child(5) { width: 18%; }  /* CURP */
        colgroup col:nth-child(6) { width: 16%; }  /* RFC */
        colgroup col:nth-child(7) { width: 12%; }  /* Fecha Registro */
        colgroup col:nth-child(8) { width: 17%; }  /* Correo */

        /* Encabezado de tabla */
        th {
            background-color: #007BFF;
            color: white;
        }

        /* Filas pares e impares */
        tr:nth-child(even) td {
            background-color: #f9f9f9;
        }

        tr:hover td {
            background-color: #f1f1f1;
        }

        /* Botón centrado */
        .btn-regresar {
            display: block;
            width: max-content;
            margin: 30px auto 0 auto;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-regresar:hover {
            background-color: #0056b3;
        }

        h2 {
            margin-top: 0;
            text-align: center;
            color: #333;
        }

        @media (max-width: 768px) {
            .tabla-wrapper {
                padding: 10px;
                margin: 20px auto;
                width: 95%;
            }

            th, td {
                text-align: center;
            }

            colgroup col {
                width: auto !important;
            }
        }
    </style>
</head>
<body>

    <!-- Encabezado -->
    <header class="site-header">
        <h1>Listado de visitantes registrados</h1>
    </header>

    <!-- Sección principal del contenido -->
    <section class="tabla-seccion">
        <div class="tabla-wrapper">
            <h2>Registros almacenados</h2>

            <?php if ($registros): ?>
                <table>
                    <colgroup>
                        <col><col><col><col><col><col><col><col>
                    </colgroup>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellido Paterno</th>
                            <th>Apellido Materno</th>
                            <th>CURP</th>
                            <th>RFC</th>
                            <th>Fecha Registro</th>
                            <th>Correo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registros as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars($r['id']) ?></td>
                                <td><?= htmlspecialchars($r['nombre']) ?></td>
                                <td><?= htmlspecialchars($r['apellido_paterno']) ?></td>
                                <td><?= htmlspecialchars($r['apellido_materno']) ?></td>
                                <td><?= htmlspecialchars($r['curp']) ?></td>
                                <td><?= htmlspecialchars($r['rfc']) ?></td>
                                <td><?= date("d/m/Y H:i:s", strtotime($r['fecha_registro'])) ?></td>
                                <td><?= htmlspecialchars($r['correo_electronico']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; font-size: 1.1em; color: #888; margin-top: 20px;">
                    Aún no hay registros guardados.
                </p>
            <?php endif; ?>

            <a href="index.html" class="btn-regresar">REGRESAR AL FORMULARIO</a>
        </div>
    </section>

</body>
</html>