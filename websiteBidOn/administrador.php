<?php
error_reporting(E_ERROR | E_PARSE);
session_start();
echo '<input type="hidden" id="NOM_USUARIO" name="NOM_USUARIO" value="'.$_SESSION["nomUsuario"].'" />';
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
<script type="text/javascript" src="js/administrador.js"></script>
</head>
<body>
<div class="wrapper-container">
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/websiteBidOn/encabezado.php'; ?>  
  <div class="wrapper-content">  
        <div class="content">
          <h2>Administrar Subastas</h2>
          <div class="comentarios">       
            <table id="subastasAdmin" width="100%" border="0" cellspacing="0" cellpadding="10" class="userslist">
              <thead>
              <tr>
                <th>Nombre</th>
                <th>Estado</th>
                <th>Fecha de finalizaci&oacute;n</th>
                <th>Acci&oacute;n</th>
              </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
        </div>        
    </div>
  </div>
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/websiteBidOn/pie.php'; ?>
</div>
</body>
</html>
