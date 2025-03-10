<?php
session_start();
header('Content-Type: application/json');

// Configuración de seguridad
define('MAX_FILE_SIZE', 536870912); // 512MB en bytes
define('ALLOWED_MIME', 'application/zip');

// Directorios
$uploadDir = '/var/www/web/backup/';
$extractPath = '/var/www/web/test/';

$response = ['status' => 'error', 'message' => ''];

try {
    // 1. Validar método y autenticación
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido', 405);
    }
    // 2. Validar archivo
    if (!isset($_FILES['backup_file'])) {
        throw new Exception('No se recibió ningún archivo', 400);
    }

    $file = $_FILES['backup_file'];
    
    // Validar tamaño
    if ($file['size'] > MAX_FILE_SIZE) {
        throw new Exception('Archivo demasiado grande. Límite: 512MB', 413);
    }
    
    // Validar tipo MIME real
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if ($mime !== ALLOWED_MIME) {
        throw new Exception('Tipo de archivo no permitido. Solo se aceptan ZIP', 415);
    }


    // 4. Mover archivo temporal
    $zipPath = $uploadDir . 'restaurar' . '.zip';
    if (!move_uploaded_file($file['tmp_name'], $zipPath)) {
        throw new Exception('Error al guardar el archivo subido', 500);
    }
    // borrar la carpeta test
    $command = "sudo rm -rf /var/www/web/test";
    exec(
        $command,
        $output,
        $returnCode
    );
    // Extraer contenido
    $zip = new ZipArchive();
    $zip->open($zipPath);
    $zip->extractTo($extractPath);
    $zip->close();

    $db_user = escapeshellarg($_POST["db_user"]);
    $db_pass = escapeshellarg($_POST["db_pass"]);
    $db_name = escapeshellarg($_POST["db_name"]);
    // Buscar archivo .sql
    $command = "find /var/www/web/test -type f -name '*.sql'";
    $sqlFile = shell_exec($command);
    // Comando MySQL
    $command = "mysql -u {$db_user} -p{$db_pass} < {$sqlFile} 2>&1";

    exec(
        $command,
        $output,
        $returnCode
    );
    
    $response = [
        'status' => 'success',
        'message' => 'Backup restaurado exitosamente'
    ];
    echo json_encode($response);
    exit;

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    $response['message'] = $e->getMessage();
    echo json_encode($response);
    exit;
}
