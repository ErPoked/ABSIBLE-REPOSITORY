<?php
session_start();

if (!isset($_SESSION["backup_file"]) || !file_exists($_SESSION["backup_file"])) {
    $_SESSION["error"] = "No se encontró el archivo de copia de seguridad.";
    header("Location: index.php");
    exit();
}

$backup_file = $_SESSION["backup_file"];

// Configurar encabezados para la descarga del ZIP
header("Content-Type: application/zip");
header("Content-Disposition: attachment; filename=" . basename($backup_file));
header("Content-Length: " . filesize($backup_file));
readfile($backup_file);

// Eliminar el archivo ZIP después de la descarga
unlink($backup_file);
unset($_SESSION["backup_file"]);

// Redirigir a index.php después de la descarga
header("Location: ../main.php?msg=export_sucess");
exit();
?>
