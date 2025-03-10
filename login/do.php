<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = $_POST;
    print_r($formData);
    error_log("Datos recibidos por POST: " . print_r($formData, true));

    $yamlFile = 'ansible/vars/vars.yml';

    if (!file_exists($yamlFile)) {
        die(json_encode(["status" => "error", "message" => "El archivo YAML no existe."]));
    }

    $yamlContent = file_get_contents($yamlFile);

    foreach ($formData as $key => $value) {
        $keyEscaped = preg_quote($key, '/');
        if (preg_match("/^{$keyEscaped}:\s*\"(.*)\"/m", $yamlContent)) {
            $yamlContent = preg_replace("/^{$keyEscaped}:\s*\"(.*)\"/m", "{$key}: \"{$value}\"", $yamlContent);
        } else {
            $yamlContent .= "\n{$key}: \"{$value}\"";
        }
    }

    file_put_contents($yamlFile, $yamlContent);
    error_log("YAML content updated successfully.");

    $wordpress_detectado = false;
    foreach ($formData as $key => $value) {
        if (strpos($key, 'wordpress_') === 0) {
            $wordpress_detectado = true;
            break;
        }
    }
    
    if ($wordpress_detectado) {
        error_log("✅ Se detectaron variables de WordPress, ejecutando Ansible...");
        $command = "sudo ansible-playbook -u www-data ansible/wordpress.yml 2>&1";
        
        // Abrir proceso en tiempo real
        $descriptorspec = [
            1 => ["pipe", "w"],  // Salida estándar
            2 => ["pipe", "w"]   // Salida de error
        ];
        $process = proc_open($command, $descriptorspec, $pipes);
    
        if (is_resource($process)) {
            header('Content-Type: text/plain');
            while (!feof($pipes[1])) {
                echo fgets($pipes[1]); // Imprime línea por línea
                flush();
                ob_flush(); // Asegura que se envíe la salida al navegador
            }
            fclose($pipes[1]);
            proc_close($process);
        }
        exit;
    }
    
    $nextcloud_detectado = false;
    foreach ($formData as $key => $value) {
        if (strpos($key, 'nextcloud_') === 0) {
            $nextcloud_detectado = true;
            break;
        }
    }
    
    if ($nextcloud_detectado) {
        error_log("✅ Se detectaron variables de Nextcloud, ejecutando Ansible...");
        $command = "sudo ansible-playbook -u www-data ansible/nextcloud.yml 2>&1";
        
        // Abrir proceso en tiempo real
        $descriptorspec = [
            1 => ["pipe", "w"],  // Salida estándar
            2 => ["pipe", "w"]   // Salida de error
        ];
        $process = proc_open($command, $descriptorspec, $pipes);
    
        if (is_resource($process)) {
            header('Content-Type: text/plain');
            while (!feof($pipes[1])) {
                echo fgets($pipes[1]); // Imprime línea por línea
                flush();
                ob_flush(); // Asegura que se envíe la salida al navegador
            }
            fclose($pipes[1]);
            proc_close($process);
        }
        exit;
    }

    echo json_encode(["status" => "success", "message" => "Datos procesados correctamente."]);
} else {
    echo json_encode(["status" => "error", "message" => "Método no permitido."]);
}
?>
