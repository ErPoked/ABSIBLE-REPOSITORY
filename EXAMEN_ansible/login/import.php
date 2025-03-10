<?php 
include("backEnd/appHeader.php");
include("config/config.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup de WordPress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 600px; margin-top: 50px; }
        .form-container { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        .btn-primary { width: 100%; }
        .alert { display: flex; align-items: center; gap: 10px; }
        .spinner-border { display: none; }
    </style>
</head>
<body>

    <!-- Navbar -->
    <?php include("view/nav.php"); ?>

    <form id="restoreForm" enctype="multipart/form-data" class="form-container">
    <div class="mb-3">
        <label class="form-label">📂 Seleccionar ZIP de backup:</label>
        <input type="file" class="form-control" name="backup_file" accept=".zip" required>
        <small class="form-text text-muted">Tamaño máximo: 512MB</small>
    </div>
    
    <div class="mb-3">
        <label class="form-label">🔑 Credenciales MySQL:</label>
        <div class="row g-2">
            <div class="col">
                <input type="text" class="form-control" placeholder="Usuario" name="db_user" required>
            </div>
            <div class="col">
                <input type="password" class="form-control" placeholder="Contraseña" name="db_pass" required>
            </div>
            <div class="col">
                <input type="text" class="form-control" placeholder="Base de datos" name="db_name" required>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary btn-lg">
        <span class="upload-status">🔄 Restaurar Backup</span>
    </button>
</form>

<div id="progress" class="mt-3" style="display: none;">
    <div class="progress">
        <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 0%"></div>
    </div>
    <div class="text-center mt-2" id="progress-text">0%</div>
</div>

<script>
$(document).ready(function() {
    $('#restoreForm').submit(function(e) {
        e.preventDefault();
        const $btn = $(this).find('button[type="submit"]');
        const originalText = $btn.html();
        
        $btn.prop('disabled', true).html('<div class="spinner-border spinner-border-sm" role="status"></div> Procesando...');
        $('#progress').show();

        const formData = new FormData(this);
        
        $.ajax({
            url: 'restore.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        $('.progress-bar').css('width', percent + '%');
                        $('#progress-text').text(percent + '%');
                    }
                }, false);
                return xhr;
            },
            success: function(response) {
                try {
                    if(response.status === 'success') {
                        showAlert('success', response.message);
                    } else {
                        showAlert('danger', response.message);
                    }
                } catch(e) {
                    showAlert('danger', 'Respuesta inválida del servidor');
                }
            },
            error: function(xhr) {
                showAlert('danger', `Error HTTP ${xhr.status}: ${xhr.statusText}`);
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
                $('#progress').hide().find('.progress-bar').css('width', '0%');
            }
        });
    });

    function showAlert(type, message) {
        console.log(`Tipo de alerta: ${type}, Mensaje: ${message}`);
        const alert = $('<div class="alert alert-' + type + ' alert-dismissible fade show mt-3"></div>')
            .html(`
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                <strong>${type === 'success' ? '✅ Éxito!' : '⚠️ Error!'}</strong> ${message}
            `);
        $('#restoreForm').after(alert);
    }
});
</script>

</body>
</html>
