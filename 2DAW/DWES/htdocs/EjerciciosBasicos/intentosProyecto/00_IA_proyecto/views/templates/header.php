<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de tareas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <?php if (!empty($_GET['role'])): ?>
    <div class="d-flex justify-content-end mb-3">
      <a href="index.php?controller=Auth&action=logout" class="btn btn-outline-danger btn-sm">Cerrar sesión</a>
    </div>
  <?php endif; ?>
