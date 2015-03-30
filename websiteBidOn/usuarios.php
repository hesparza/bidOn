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
<script type="text/javascript" src="js/usuarios.js"></script>
</head>
<body>
<div class="wrapper-container">
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/websiteBidOn/encabezado.php'; ?>  
  <div class="wrapper-content">  
        <div class="content">
          <h2>Administración de usuarios</h2>
          <div class="comentarios">
          <div class="editarUsuario">
            <table id="usuariosAdmin" width="100%" border="0" cellspacing="0" cellpadding="10" class="userslist">
              <thead>
              <tr>
                <th>Usuario</th>
                <th>Nombre</th>
                <th>Apellido Paterno</th>
                <th>Apellido Materno</th>
                <th>Correo</th>
                <th>Estado</th>
                <th>Rol</th>
                <th>Actualizar</th>                
              </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
            </div>
          </div>
    </div>
  </div>
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/websiteBidOn/pie.php'; ?>
</div>
</body>
</html>
