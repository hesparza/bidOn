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
</head><body>
<div class="wrapper-container">
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/websiteBidOn/encabezado.php'; ?>
  <div class="wrapper-content">
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/websiteBidOn/carrusel.php'; ?>
    <div class="content" id="userZone">
      <h2>Zona de usuarios</h2>
      <ul class="ingreso">
        <li class="short">
          <h3>Si olvidó sus datos de acceso,<br>
				no hay problema.</h3>
        </li>
        <li>
          <h3>Siga las instrucciones para acceder nuevamente</h3>
          <p>Por favor ingrese su dirección de correo para recibir sus datos de acceso.</p>
          <p>Debe ingresar la dirección de correo electrónico registrada en el momento de abrir la cuenta.</p>
          <form name="form1" method="post" action="">
            <label>Correo electrónico registrado
            <input type="text" name="email" id="email" maxlength="60"></label>
            <input type="submit" name="login" id="login" value="Validar">
          </form>
        </li>
      </ul>
    </div>
  </div>
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/websiteBidOn/pie.php'; ?>
</div>
</body>
</html>
