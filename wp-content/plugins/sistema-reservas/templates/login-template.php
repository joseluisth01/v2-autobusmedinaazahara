<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Reservas - Login</title>
</head>
<body>
    <div class="login-container">
        <h2>Sistema de Reservas</h2>

        <?php if (isset($_GET['error'])): ?>
            <div class="error">
                <?php 
                $dashboard = new ReservasDashboard();
                echo $dashboard->get_error_message($_GET['error']); 
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="success">
                Login correcto. Redirigiendo...
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['logout']) && $_GET['logout'] == 'success'): ?>
            <div class="success">
                Sesión cerrada correctamente.
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="username">Usuario:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-login">Iniciar Sesión</button>
        </form>

        <div class="info-box">
            <p><strong>Usuario inicial:</strong> administrador</p>
            <p><strong>Contraseña inicial:</strong> busmedina</p>
            <p><em>Cambia estas credenciales después del primer acceso</em></p>
        </div>
    </div>
</body>
</html>