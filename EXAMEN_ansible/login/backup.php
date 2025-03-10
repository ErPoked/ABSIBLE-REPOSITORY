<?php
session_start();
include("config/config.php");

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Proceso no iniciado.'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db_user = escapeshellarg($_POST["db_user"]);
    $db_pass = escapeshellarg($_POST["db_pass"]);
    $db_name = escapeshellarg($_POST["db_name"]);

    $timestamp = date("Ymd_His");
    $backup_file = BACKUP_PATH . "/wpBCK_$timestamp.zip";
    $sql_file = BACKUP_PATH . "/wordpress_backup_$timestamp.sql";

    $dump_command = "mysqldump -u $db_user -p$db_pass --databases $db_name > $sql_file 2>&1";
    exec($dump_command, $output, $return_var);

    if ($return_var !== 0) {
        $response['message'] = 'Error al exportar la base de datos: ' . implode("\n", $output);
        echo json_encode($response);
        exit();
    }

    $zip = new ZipArchive();
    if ($zip->open($backup_file, ZipArchive::CREATE) === TRUE) {
        $zip->addFile($sql_file, basename($sql_file));

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(WP_PATH, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen(WP_PATH) + 1);
            $zip->addFile($filePath, "wordpress_files/$relativePath");
        }

        $zip->close();
        unlink($sql_file);

        $_SESSION['backup_file'] = $backup_file;
        $response = ['status' => 'success', 'message' => 'Backup generado con éxito.', 'file' => $backup_file];
    } else {
        $response['message'] = 'Error al comprimir el backup';
    }
}

echo json_encode($response);
exit();
