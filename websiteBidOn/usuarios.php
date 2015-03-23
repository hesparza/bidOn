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
</head>
<body>
<div class="wrapper-container">
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/websiteBidOn/encabezado.php'; ?>  
  <div class="wrapper-content">  
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/websiteBidOn/carrusel.php'; ?>
        <div class="content">
          <h2>Administración de usuarios</h2>
          <div class="comentarios">
            <table width="100%" border="0" cellspacing="0" cellpadding="10" class="userslist">
              <thead>
              <tr>
                <th>Nombre</th>
                <th>Usuario</th>
                <th>Estado</th>
                <th>Rol</th>
              </tr>
              </thead>
              <tbody>
              <tr>
                <td><a href="#">Ejemplo</a></td>
                <td>Ejemplo01</td>
                <td>Activo</td>
                <td>Admin</td>
              </tr>
              <tr>
                <td><a href="#">Ejemplo</a></td>
                <td>Ejemplo02</td>
                <td>Activo</td>
                <td>Usuario</td>
              </tr>
              <tr>
                <td><a href="#">Ejemplo</a></td>
                <td>Ejemplo03</td>
                <td>Activo</td>
                <td>Usuario</td>
              </tr>
              <tr>
                <td><a href="#">Ejemplo</a></td>
                <td>Ejemplo04</td>
                <td>Activo</td>
                <td>Usuario</td>
              </tr>
              </tbody>
            </table>
          </div>
<div class="comentarios">
<form name="form1" method="post" action="">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>    <label>Nombres
      <input type="text" name="firstname" id="firstname">
      </label></td>
    <td><label>Usuario
        <input type="text" name="username" id="username">
    </label></td>
    <td><label>Estado
        <select name="status" id="status">
          <option>Seleccionar...</option>
          <option>Activo</option>
          <option>Inactivo</option>
        </select>
    </label></td>
  </tr>
  <tr>
    <td><label>Apellidos
        <input type="text" name="lastname" id="lastname">
    </label></td>
    <td><label>Contraseña
        <input type="password" name="password" id="password">
    </label></td>
    <td><label>Rol
        <select name="role" id="role">
          <option>Seleccionar...</option>
          <option>Administrador</option>
          <option>Usuario</option>
                </select>
    </label></td>
  </tr>
  
  <tr>
    <td><label>Correo electrónico
        <input type="text" name="email" id="email">
    </label></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="save" id="save" value="Guardar"></td>
    <td><input type="reset" name="cancel" id="cancel" value="Cancelar"></td>
  </tr>
</table>
  </form>
</div>
    </div>
  </div>
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/websiteBidOn/pie.php'; ?>
</div>
</body>
</html>
