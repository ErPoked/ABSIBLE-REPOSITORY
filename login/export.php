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

    <div class="container">
        <div class="form-container">
            <h2 class="text-center">🔄 Generar Backup</h2>
            <form id="backupForm">
                <div class="mb-3">
                    <label class="form-label">👤 Usuario DB:</label>
                    <input type="text" class="form-control" name="db_user" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">🔑 Contraseña DB:</label>
                    <input type="password" class="form-control" name="db_pass" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">📁 Nombre DB:</label>
                    <input type="text" class="form-control" name="db_name" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <span class="spinner-border spinner-border-sm"></span> Iniciar Backup
                </button>
            </form>
            <div id="statusMessage" class="mt-3"></div>
            <button id="downloadBtn" class="btn btn-success mt-3" style="display:none;">⬇ Descargar Backup</button>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#backupForm').submit(function(e) {
                e.preventDefault();
                let btn = $('button[type="submit"]');
                btn.prop('disabled', true);
                $('.spinner-border').show();
                $('#statusMessage').html('<div class="alert alert-info">⏳ Iniciando backup...</div>');
                
                $.post('backup.php', $(this).serialize(), function(response) {
                    btn.prop('disabled', false);
                    $('.spinner-border').hide();

                    if (response.status === 'success') {
                        $('#statusMessage').html('<div class="alert alert-success">✅ Backup completado.</div>');
                        $('#downloadBtn').show().click(function() {
                            window.location.href = 'download.php?file=' + encodeURIComponent(response.file);
                        });
                    } else {
                        $('#statusMessage').html('<div class="alert alert-danger">❌ ' + response.message + '</div>');
                    }
                }, 'json');
            });
        });
    </script>

</body>
</html>
