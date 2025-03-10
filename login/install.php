<?php include("backEnd/appHeader.php"); ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalación de aplicaciones</title>
    <!-- Se usa Bootstrap 5 desde CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        /* Estética global similar a la del formulario de backup */
        body { 
            background-color: #f8f9fa; 
        }
        .container { 
            max-width: 600px; 
            margin-top: 50px; 
        }
        .form-container { 
            background: white; 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
        }
        .btn-primary { 
            width: 100%; 
        }
        /* Estilos para selección de logos */
        .hidden { 
            display: none; 
        }
        .logo-option { 
            cursor: pointer; 
            border: 2px solid transparent; 
            padding: 10px; 
            width: 100px; 
            transition: border-color 0.3s ease;
        }
        .logo-option.selected { 
            border-color: #007bff; 
        }
        .logo-container { 
            text-align: center; 
        }
        .logo-label { 
            display: block; 
            margin-top: 5px; 
            font-weight: bold; 
        }
        /* Modal y demás estilos se mantienen */
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.1) !important;
            backdrop-filter: blur(4px);
            z-index: 1050;
        }
        .modal-dialog {
            display: flex;
            align-items: center;
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <!-- Navbar responsiva -->
    <?php include("view/nav.php"); ?>

    <div class="container mt-5">
        <div class="form-container">
            <h2 class="text-center">Configuración del servidor</h2>
            <form id="installForm" action="do.php" method="POST" class="needs-validation" novalidate>
                <div class="row text-center my-4 justify-content-center">
                    <div class="col-6 col-md-3 logo-container">
                        <img src="img/lamp.png" class="img-fluid logo-option" data-option="lamp">
                        <span class="logo-label">LAMP</span>
                    </div>
                    <div class="col-6 col-md-3 logo-container">
                        <img src="img/wp.png" class="img-fluid logo-option" data-option="wordpress">
                        <span class="logo-label">WordPress</span>
                    </div>
                    <div class="col-6 col-md-3 logo-container">
                        <img src="img/nc.png" class="img-fluid logo-option" data-option="nextcloud">
                        <span class="logo-label">NextCloud</span>
                    </div>
                    <div class="col-6 col-md-3 logo-container">
                        <img src="img/moodle.png" class="img-fluid logo-option" data-option="moodle">
                        <span class="logo-label">Moodle</span>
                    </div>
                </div>

                <!-- Campos comunes con iconos en cada input -->
                <div id="common-fields" class="hidden">
                    <h4>Datos FTP y MySQL</h4>
                    <div class="mb-3">
                        <label for="ftp_user" class="form-label">Usuario FTP</label>
                        <div class="input-group">
                            <span class="input-group-text">👤</span>
                            <input type="text" id="ftp_user" name="ftp_user" class="form-control" placeholder="Usuario FTP" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="ftp_pass" class="form-label">Contraseña FTP</label>
                        <div class="input-group">
                            <span class="input-group-text">🔑</span>
                            <input type="password" id="ftp_pass" name="ftp_pass" class="form-control" placeholder="Contraseña FTP" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="mysql_root" class="form-label">Contraseña MySQL root</label>
                        <div class="input-group">
                            <span class="input-group-text">🗄</span>
                            <input type="password" id="mysql_root" name="mysql_root" class="form-control" placeholder="Contraseña MySQL root" required>
                        </div>
                    </div>
                </div>

                <!-- Campos dinámicos por opción, con iconos -->
                <div id="dynamic-fields"></div>
                
                <div class="text-center mt-5">
                    <button type="submit" class="btn btn-primary w-25">Instalar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Confirmación -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar instalación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Por favor, verifica los datos antes de continuar:</strong></p>
                    <ul id="confirmationList"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="confirmInstall" class="btn btn-primary">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            setTimeout(() => $('body').addClass('show'), 100);

            $('.logo-option').click(function() {
                $(this).toggleClass('selected');
                let selectedOptions = $('.logo-option.selected');
                $('#common-fields').toggle(selectedOptions.length > 0);
                updateDynamicFields();
            });

            function updateDynamicFields() {
                $('#dynamic-fields').html('');
                $('.logo-option.selected').each(function() {
                    let option = $(this).data('option');
                    let html = `<div class='mt-3'>
                        <h4>${option.toUpperCase()}</h4>
                        <div class='mb-3'>
                            <label class='form-label'>Dominio</label>
                            <div class='input-group'>
                                <span class='input-group-text'>🌐</span>
                                <input type='text' name='${option}_domain' class='form-control domain-field' placeholder='Dominio' required>
                            </div>
                        </div>
                        <div class='mb-3'>
                            <label class='form-label'>Usuario BD</label>
                            <div class='input-group'>
                                <span class='input-group-text'>👤</span>
                                <input type='text' name='${option}_db_user' class='form-control' placeholder='Usuario BD' required>
                            </div>
                        </div>
                        <div class='mb-3'>
                            <label class='form-label'>Contraseña BD</label>
                            <div class='input-group'>
                                <span class='input-group-text'>🔑</span>
                                <input type='password' name='${option}_db_pass' class='form-control' placeholder='Contraseña BD' required>
                            </div>
                        </div>
                    </div>`;
                    $('#dynamic-fields').append(html);
                });

                $('.domain-field').on('input', function() {
                    let domain = $(this).val().trim();
                    let domains = [];
                    $('.domain-field').each(function() {
                        let d = $(this).val().trim();
                        if(d) {
                            domains.push(d);
                        }
                    });
                    if (domain && domains.filter(d => d === domain).length > 1) {
                        alert('⚠ El dominio ' + domain + ' ya está en uso. Por favor, elige otro.');
                        $(this).val('');
                    }
                });
            }

            $('#installForm').submit(function(event) {
                event.preventDefault();

                let domains = [];
                let valid = true;
                $('.domain-field').each(function() {
                    let domain = $(this).val().trim();
                    if (!/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(domain) || domains.includes(domain)) {
                        valid = false;
                        alert('Dominios inválidos o repetidos.');
                        return false;
                    }
                    domains.push(domain);
                });

                if (valid && this.checkValidity()) {
                    let confirmationList = $("#confirmationList").empty();
                    $(this).serializeArray().forEach(item => {
                        confirmationList.append(`<li><strong>${item.name}:</strong> ${item.value}</li>`);
                    });

                    $('#confirmationModal').modal('show');
                }
            });

            $('#confirmInstall').click(() => $('#installForm').off('submit').submit());
        });
    </script>
</body>
</html>