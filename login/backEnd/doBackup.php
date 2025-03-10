<?php
session_start();
include("../config/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db_user = escapeshellarg($_POST["db_user"]);
    $db_pass = escapeshellarg($_POST["db_pass"]);
    $db_name = escapeshellarg($_POST["db_name"]);

    $timestamp = date("Ymd_His");
    $backup_file = BACKUP_PATH . "/wpBCK_$timestamp.zip";
    $sql_file = BACKUP_PATH . "/wordpress_backup_$timestamp.sql";

    // 1. Realizar la copia de seguridad de la base de datos con mysqldump
    $dump_command = "mysqldump -u$db_user -p$db_pass $db_name > $sql_file";
    exec($dump_command, $output, $return_var);

    if ($return_var !== 0) {
        header("Location: ../main.php?msg=export_db_error");
        exit();
    }

    // 2. Crear el archivo ZIP con la base de datos y los archivos de WordPress
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

        // Guardar la ruta del archivo en sesión para su descarga
        $_SESSION["backup_file"] = $backup_file;

        // Redirigir al script de descarga
        header("Location: download.php");
        exit();
    } else {
        header("Location: ../main.php?msg=export_zip_error");
        exit();
    }
}
?>
