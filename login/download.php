<?php
session_start();
if (!isset($_GET['file']) || empty($_SESSION['backup_file'])) {
    die("Archivo no encontrado.");
}

$file = $_SESSION['backup_file'];

if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . basename($file) . '"');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    unlink($file);
    session_destroy();
    exit();
} else {
    die("Archivo no disponible.");
}
?>