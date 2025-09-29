<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit;
}
?>
<h1>Bienvenido <?php echo $_SESSION['user_name']; ?></h1>
<p>Email: <?php echo $_SESSION['user_email']; ?></p>
<a href="logout.php">Cerrar sesión</a>
