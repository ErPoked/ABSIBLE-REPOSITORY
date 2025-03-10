<?php
// (Opcional) session_start(); si usas sesión para el idioma
// session_start();

// Detectar el idioma desde GET, Sesión o Configuración Predeterminada
$default_lang = 'es'; // Español por defecto
$lang = isset($_GET['lang']) ? $_GET['lang'] : (isset($_SESSION['lang']) ? $_SESSION['lang'] : $default_lang);

// Validar que el idioma existe, si no, usar español
$available_languages = ['es', 'en'];
if (!in_array($lang, $available_languages)) {
    $lang = $default_lang;
}

// Cargar el diccionario de mensajes
$messages = include "view/messages_$lang.php";

// Obtener el mensaje (ahora cada mensaje es un array: [0] => texto, [1] => tipo)
$msg = (isset($_GET['msg']) && array_key_exists($_GET['msg'], $messages))
    ? $messages[$_GET['msg']]
    : [];
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login & Registro</title>
  
  <!-- Bootstrap 5 CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet" />
  
  <!-- Estilos personalizados -->
  <style>
    body {
      margin: 0;
      padding: 0;
      min-height: 100vh;
      overflow-x: hidden;
      background: #f8f9fa;
    }
    /* Panel Izquierdo con imagen de fondo */
    .left-side {
      background: url('img/fondo.png') no-repeat center center;
      background-size: cover;
      color: #000;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start; /* Alineado hacia arriba */
      text-align: center;
      padding: 2rem;
      padding-top: 4rem; /* Más espacio arriba */
    }
    .left-side img {
      width: 120px;
      margin-bottom: 1rem;
    }
    .left-side h1 {
      font-size: 2rem;
      margin-bottom: 1rem;
    }
    .left-side p {
      font-size: 1.1rem;
      max-width: 350px;
      margin: 0 auto;
    }
    /* Alertas personalizadas en el panel izquierdo */
    .custom-alert-success {
      background-color: rgba(0,123,255,0.7) !important;
      border-color: rgba(0,123,255,0.8) !important;
      color: #fff;
    }
    .custom-alert-error {
      background-color: rgba(220,53,69,0.7) !important;
      border-color: rgba(220,53,69,0.8) !important;
      color: #fff;
    }
    .custom-alert-alert {
      background-color: rgba(255,193,7,0.7) !important;
      border-color: rgba(255,193,7,0.8) !important;
      color: #212529;
    }
    /* Panel Derecho */
    .right-side {
      background: #f8f9fa; /* Gris claro */
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
    }
    .custom-card {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      padding: 2rem;
      width: 100%;
      max-width: 400px;
    }
    /* Pestañas: usamos los colores primary de Bootstrap */
    .nav-tabs .nav-link.active {
      background-color: var(--bs-primary);
      color: #fff;
    }
    .nav-tabs .nav-link {
      color: var(--bs-primary);
    }
    .left-side p {
  color: #000; /* Asegura que el texto sea oscuro */
  background: rgba(165, 200, 205, 0.5); /* Fondo semitransparente */
  padding: 10px; /* Añade espacio interno */
  border-radius: 5px; /* Bordes redondeados */
  box-shadow: 2px 2px 5px rgba(0,0,0,0.2); /* Sombras suaves */
  display: inline-block; /* Ajusta el fondo al contenido */
}

  </style>
  
  <!-- Bootstrap JS + jQuery -->
  <script src="js/jquery-3.6.0.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
  
  <script>
    $(document).ready(function () {
      // Recoger mensaje PHP (ahora usando índices 0 y 1)
      var phpMsgText = "<?php echo isset($msg[0]) ? addslashes($msg[0]) : ''; ?>";
      var phpMsgType = "<?php echo isset($msg[1]) ? addslashes($msg[1]) : ''; ?>";
      
      if (phpMsgText.length > 0) {
          // Ocultar el párrafo y mostrar el mensaje en su lugar
          $('#left-paragraph').hide();
          var alertClass = 'alert-secondary'; // valor por defecto
          if (phpMsgType === 'success') {
              alertClass = 'custom-alert-success';
          } else if (phpMsgType === 'error') {
              alertClass = 'custom-alert-error';
          } else if (phpMsgType === 'alert') {
              alertClass = 'custom-alert-alert';
          }
          $('#left-message').removeClass('d-none')
                            .removeClass('alert-secondary')
                            .addClass(alertClass)
                            .text(phpMsgText);
          
          // Tras 5 segundos, oculta el mensaje y vuelve a mostrar el párrafo
          setTimeout(function(){
              $('#left-message').addClass('d-none');
              $('#left-paragraph').fadeIn();
          }, 5000);
      }
      
      // Efecto de fadeIn inicial en la tarjeta
      $('.custom-card').hide().fadeIn(1500);
      
      // Validar que las contraseñas coincidan en el formulario de registro
      $('#register form').on('submit', function(e) {
          let pass = $('#registerPassword').val();
          let check = $('#checkPassword').val();
          if (pass !== check) {
              e.preventDefault();
              if ($('#reg-message-container .alert').length === 0) {
                  $('#reg-message-container').html(
                    '<div class="alert alert-danger text-center" role="alert">Las contraseñas no coinciden.</div>'
                  );
              }
              $('#registerPassword').focus();
              setTimeout(function(){
                  $('#reg-message-container .alert').fadeOut(5000, function(){ 
                      $(this).remove(); 
                  });
              }, 5000);
          }
      });
      
      // Al pulsar 'Create New', se activa la pestaña de registro
      $('#create-new-link').on('click', function(e){
          e.preventDefault();
          var registerTab = new bootstrap.Tab(document.querySelector('#register-tab'));
          registerTab.show();
      });
    });
  </script>
</head>
<body>
  <div class="container-fluid m-0 p-0">
    <div class="row g-0" style="min-height: 100vh;">
      
      <!-- Panel Izquierdo -->
      <div class="col-md-6 left-side">
        <!-- Logo -->
        <img src="img/logo.png" alt="Logo" />
        <h1>Aplicaciones Web</h1>
        
        <!-- Párrafo y contenedor de mensaje -->
        <p id="left-paragraph">
          <i>La mansedumbre del cordero no cambiará la voracidad del lobo; no te doblegues ante quienes te hostigan.</i>
        </p>
        <div id="left-message" class="alert text-center d-none" role="alert"></div>
      </div>
      
      <!-- Panel Derecho -->
      <div class="col-md-6 right-side">
        <div class="custom-card">
          
          <!-- Pestañas (Login / Registro) -->
          <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="login-tab" data-bs-toggle="tab"
                      data-bs-target="#login" type="button" role="tab">
                Login
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="register-tab" data-bs-toggle="tab"
                      data-bs-target="#register" type="button" role="tab">
                Registro
              </button>
            </li>
          </ul>
          
          <div class="tab-content mt-3" id="myTabContent">
            
            <!-- Formulario de Login -->
            <div class="tab-pane fade show active" id="login" role="tabpanel">
              <form action="backEnd/mngSession.php" method="post">
                <input type="hidden" name="op" value="ss">
                
                <div class="mb-3">
                  <label for="loginMail" class="form-label">Correo Electrónico</label>
                  <input type="email" class="form-control" id="loginMail" name="mail" required>
                </div>
                
                <div class="mb-3">
                  <label for="loginPassword" class="form-label">Contraseña</label>
                  <input type="password" class="form-control" id="loginPassword" 
                         name="pass" required autocomplete="off">
                </div>
                
                <button type="submit" class="btn btn-primary w-100">
                  Iniciar Sesión
                </button>
              </form>
              
              <!-- Enlace para crear nueva cuenta
              <div class="text-center mt-3">
                <span>Autenticación de usuario</span>
              </div>
              -->
            </div>
            
            <!-- Formulario de Registro -->
            <div class="tab-pane fade" id="register" role="tabpanel">
              <form action="backEnd/mngSession.php" method="post">
                <input type="hidden" name="op" value="ur">
                
                <div class="mb-3">
                  <label for="registerName" class="form-label">Nombre</label>
                  <input type="text" class="form-control" id="registerName" name="name" required>
                </div>
                
                <div class="mb-3">
                  <label for="registerMail" class="form-label">Correo Electrónico</label>
                  <input type="email" class="form-control" id="registerMail" name="mail" required>
                </div>
                
                <div class="mb-3">
                  <label for="registerPassword" class="form-label">Contraseña</label>
                  <input type="password" class="form-control" id="registerPassword" 
                         name="pass" required minlength="8" autocomplete="off">
                </div>
                
                <div class="mb-3">
                  <label for="checkPassword" class="form-label">Repita Contraseña</label>
                  <input type="password" class="form-control" id="checkPassword" 
                         name="check" required minlength="8" autocomplete="off">
                </div>
                
                <button type="submit" class="btn btn-primary w-100">
                  Registrarse
                </button>
              </form>
              
              <!-- Contenedor para mensajes de registro -->
              <div id="reg-message-container" class="mt-3"></div>
            </div>
            
          </div> <!-- /tab-content -->
        </div> <!-- /custom-card -->
      </div> <!-- /col-md-6 right-side -->
      
    </div> <!-- /row -->
  </div> <!-- /container-fluid -->
</body>
</html>
