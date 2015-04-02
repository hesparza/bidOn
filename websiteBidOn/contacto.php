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
<script type="text/javascript" src="js/contacto.js"></script>
</head><body>
<div class="wrapper-container">
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/websiteBidOn/encabezado.php'; ?>
  </div>
  <div class="wrapper-content">
    <div class="content" id="userZone">
      <h2>Contacto</h2>
      <ul class="ingreso">
        <li class="short">
          <h3>Servicio de atención al cliente</h3>
        </li>
        <li>
          <h3>Estamos aquí para ayudarlo</h3>
          <form name="FormaContacto" id="FormaContacto" method="post" action="">
            <label>Correo electrónico
            <input type="text" name="correo" id="correo" required></label>
            <label>Asunto
            <input type="text" name="asunto" id="asunto" required></label>
            <label>Mensaje
            <textarea name="opinion" cols="" rows="8" required id="mensaje"></textarea>
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
