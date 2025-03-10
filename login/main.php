<?php include("backEnd/appHeader.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Menú de Usuario</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- Bootstrap 5 CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Alertas personalizadas: texto blanco para todos los tipos */
        .custom-alert-success {
            background-color: rgba(0,123,255,0.8) !important;
            border-color: rgba(0,123,255,0.8) !important;
            color: #fff;
        }
        .custom-alert-error {
            background-color: rgba(220,53,69,0.8) !important;
            border-color: rgba(220,53,69,0.8) !important;
            color: #fff;
        }
        .custom-alert-alert {
            background-color: rgba(255,193,7,0.8) !important;
            border-color: rgba(255,193,7,0.8) !important;
            color: #fff;
        }
        .custom-alert-app {
            background-color: rgba(51,102,153,0.8) !important;
            border-color: rgba(165,165,165,0.8) !important;
            color: #fff;
        }
        /* Contenedor principal: ocupa el alto descontando la navbar (56px) */
        .full-height {
            min-height: calc(100vh - 56px);
        }
    </style>
</head>
<body>
    <!-- Navbar responsiva -->
    <?php include("view/nav.php"); ?>

    <!-- Contenedor del mensaje centrado -->
    <div class="container full-height d-flex justify-content-center align-items-center">
        <div class="row w-100">
            <div class="col-12 col-md-6 mx-auto text-center">
                <!-- Mensaje (inicialmente oculto) -->
                <div id="left-message" class="alert d-none" role="alert"></div>
                <!-- Párrafo predeterminado -->
                <p id="left-paragraph" class="text-center text-secondary fs-4">
                    Autor: Víctor Zamora Lorente<br>
                    Modificado por: Andres Tenllado Perez
                </p>
            </div>
        </div>
    </div>

    <!-- Modal para gestionar cuenta -->
    <div class="modal fade" id="accountModal" tabindex="-1" aria-labelledby="accountModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="update_account.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="accountModalLabel">Gestionar cuenta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" 
                                aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Nombre de usuario -->
                        <div class="mb-3">
                            <label for="username" class="form-label">Nombre de usuario</label>
                            <input type="text" class="form-control" id="username" name="username"
                                   value="<?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>" required>
                        </div>
                        <!-- Contraseña (opcional) -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password"
                                   placeholder="Dejar en blanco para mantener la actual">
                        </div>
                        <!-- Idioma -->
                        <div class="mb-3">
                            <label for="language" class="form-label">Idioma</label>
                            <select class="form-select" id="language" name="language">
                                <option value="es" <?php echo ((($_SESSION['language'] ?? '') === 'es') ? 'selected' : ''); ?>>Español</option>
                                <option value="en" <?php echo ((($_SESSION['language'] ?? '') === 'en') ? 'selected' : ''); ?>>Inglés</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- jQuery y Bootstrap Bundle (Popper incluido) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    
    <!-- Script para mostrar el mensaje -->
    <script>
      $(document).ready(function(){
          const phpMsgText = "<?php echo isset($msg[0]) ? addslashes($msg[0]) : ''; ?>";
          const phpMsgType = "<?php echo isset($msg[1]) ? addslashes($msg[1]) : ''; ?>";

          if(phpMsgText){
              // Ocultar el párrafo y mostrar el mensaje
              $('#left-paragraph').hide();
              let alertClass = 'alert-secondary';

              switch(phpMsgType){
                  case 'app':
                      alertClass = 'custom-alert-app';
                      break;
                  case 'success':
                      alertClass = 'custom-alert-success';
                      break;
                  case 'error':
                      alertClass = 'custom-alert-error';
                      break;
                  case 'alert':
                      alertClass = 'custom-alert-alert';
                      break;
              }
              $('#left-message')
                  .removeClass('d-none')
                  .removeClass('alert-secondary')
                  .addClass(alertClass + ' fs-4')
                  .text(phpMsgText);

              // Tras 5s, ocultar el mensaje y mostrar el párrafo
              setTimeout(function(){
                  $('#left-message').addClass('d-none');
                  $('#left-paragraph').fadeIn();
              }, 5000);
          }
      });
    </script>
</body>
</html>
