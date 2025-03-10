<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
    <div class="container-fluid">
        <!-- Nombre de usuario con icono -->
        <a class="navbar-brand d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#accountModal">
            <i class="fas fa-user-circle me-2"></i> <?php echo htmlspecialchars($_SESSION['name'] ?? 'Usuario'); ?>
        </a>

        <!-- Botón hamburguesa para móvil -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                data-bs-target="#navbarContent" aria-controls="navbarContent" 
                aria-expanded="false" aria-label="Alternar navegación">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                <!-- Inicio -->
                <li class="nav-item">
                    <a class="nav-link" href="main.php">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                </li>
                <!-- Instalación -->
                <li class="nav-item">
                    <a class="nav-link" href="install.php">
                        <i class="fas fa-download"></i> Instalación
                    </a>
                </li>
                <!-- Exportar -->
                <li class="nav-item">
                    <a class="nav-link" href="export.php">
                        <i class="fas fa-file-export"></i> Exportar
                    </a>
                </li>    
                <!-- Importar  --> 
                <li class="nav-item">
                    <a class="nav-link" href="import.php">
                        <i class="fas fa-file-import"></i> Importar
                    </a>
                </li>                 
                <!-- Cuenta (solo en main.php) -->
                <?php if (basename($_SERVER['PHP_SELF']) == 'main.php'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#accountModal">
                            <i class="fas fa-user"></i> Cuenta
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Cerrar sesión con icono -->
                <li class="nav-item">
                    <form action="backEnd/mngSession.php" method="post" class="d-flex align-items-center">
                        <input type="hidden" name="op" value="sc">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                        <button type="submit" class="btn btn-outline-light btn-sm ms-3" title="Cerrar sesión">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Incluir FontAwesome para iconos -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
