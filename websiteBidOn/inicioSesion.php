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
<script type="text/javascript" src="js/inicioSesion.js"></script>
</head><body>
<div class="wrapper-container">
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/websiteBidOn/encabezado.php'; ?>
  <div class="wrapper-content">
    <div class="content" id="login">
      <h2>Inicio de sesi&oacute;n</h2>
      <ul class="ingreso">
        <li>
          <form id="FormaInicioSesion" name="form1" method="post" action="">
            <label>Usuario
            <input type="text" name="username" id="nomUsuario" maxlength="30"></label>
            <label>Contraseña
            <input type="password" name="password" id="contrasena" maxlength="30"></label>
            <input type="submit" name="login" id="aceptar" value="Enviar">
          </form>
          <br />
          <br />
          <a href="olvido.php">Olvid&eacute; mi contrase&ntilde;a</a>
        </li>
      </ul>
    </div>
  </div>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/websiteBidOn/pie.php'; ?>
</div>
</body>
</html>
