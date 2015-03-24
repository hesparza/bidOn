<?php
error_reporting(E_ERROR | E_PARSE);
session_start();
?>
<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- SlidesJS Required (if responsive): Sets the page width to the device width. -->
<meta name="viewport" content="width=device-width">
<!-- End SlidesJS Required -->
<title>BidOn - Portal Social de Subastas en Línea</title>
<link href='http://fonts.googleapis.com/css?family=Ubuntu:400,500,700' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="css/styles.css" />
<link rel="stylesheet" href="css/font-awesome.min.css">
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/configuracion.js"></script>
<script type="text/javascript" src="js/funcionesComunes.js"></script>
<script type="text/javascript" src="js/registro.js"></script>
</head><body>
<div class="wrapper-container">
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/websiteBidOn/encabezado.php'; ?>
  <div class="wrapper-content">
    <div class="content" id="userZone">
      <h2>Zona de usuarios</h2>
      <ul class="ingreso">
        <li class="short">
          <h3>¡Regístrese ahora mismo!</h3>
          <p>Acceda a una gran variedad de artículos con precios realmente asombrosos.</p>
        </li>
        <li>
          <h3>Datos de usuario</h3>
          <form name="FormaRegistro" id="FormaRegistro" method="post" action="">
            <label>Nombre
            <input type="text" name="nombre" id="nombre" maxlength="30"></label>
            <label>Apellido Paterno
            <input type="text" name="apellidoPaterno" id="apellidoPaterno" maxlength="30"></label>
            <label>Apellido Materno
            <input type="text" name="apellidoMaterno" id="apellidoMaterno" maxlength="30"></label>            
            <label>Correo electrónico
            <input type="text" name="correo" id="correo" required maxlength="60"></label>
            <label>Usuario
            <input type="text" name="nomUsuario" id="nomUsuario" maxlength="30"></label>
            <label>Contraseña
            <input type="password" name="contrasena" id="contrasena" class="required" maxlength="30"></label>
            <input type="submit" name="login" id="login" value="Enviar">
          </form>
        </li>
      </ul>
    </div>
  </div>
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/websiteBidOn/pie.php'; ?>
</div>
</body>
</html>
