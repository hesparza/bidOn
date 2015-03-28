<?php
error_reporting ( E_ERROR | E_PARSE );
session_start ();
echo '<input type="hidden" id="NOM_USUARIO" name="NOM_USUARIO" value="'.$_SESSION["nomUsuario"].'" />';
echo '<input type="hidden" id="ID_SUBASTA" name="ID_SUBASTA" value="'.$_GET["id"].'" />';
?>
<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- SlidesJS Required (if responsive): Sets the page width to the device width. -->
<meta name="viewport" content="width=device-width">
<!-- End SlidesJS Required -->
<title>BidOn - Portal Social de Subastas en Línea</title>
<link href='http://fonts.googleapis.com/css?family=Ubuntu:400,500,700'
	rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="css/styles.css" />
<link rel="stylesheet" href="css/font-awesome.min.css">
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/configuracion.js"></script>
<script type="text/javascript" src="js/funcionesComunes.js"></script>
<script type="text/javascript" src="js/pago.js"></script>
</head>
<body>
	<div class="wrapper-container">
		<div class="wrapper-header">
	<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/websiteBidOn/encabezado.php'; ?>
  <div class="wrapper-content">  
	<?php //include_once $_SERVER['DOCUMENT_ROOT'] . '/websiteBidOn/carrusel.php'; ?>
  	<div class="content">
					<h2>Pago</h2>
					<div class="comentarios" id="comentarios">
						<form name="FormaPago" method="post" action="" id="FormaPago">
							<table width="100%" border="0" cellspacing="10" cellpadding="0">
								<tr>
									<td><output id="tipoSubasta">Tipo de subasta:</output></td>
									<td><output id="cantidad">Cantidad a pagar: $__.__</output></td>
									<td></td>
								</tr>
								<tr>
									<td colspan="3"><label>Datos de pago:</label></td>									
								</tr>								
								<tr>
									<td>
										<label>Forma de pago: 
											<select name="formaPago" id="formaPago">
													<option id="categoriaDefault" value="0">Tarjeta de cr&eacute;dito</option>
											</select>
										</label>
									</td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td><label>Nombre <input type="text" name="nombre" id="nombre"></label></td>
									<td><label>N&uacute;mero de tarjeta<input type="text" name="numeroTarjeta" id="numeroTarjeta"></label></td>
									<td></td>
								</tr>
								<tr>
									<td><label>CVV <input type="text" name="cvv" id="cvv"></label></td>
									<td><label>Fecha de expiraci&oacute;n<input name="fechaexpiracion" type="date" id="fechaexpiracion" value=""></label></td>
									<td></td>
								</tr>
								<tr>
									<td colspan="3"><label>Datos de env&iacute;o:</label></td>
								</tr>								
								<tr>
									<td><label>Calle <input type="text" name="calle" id="calle"></label></td>
									<td><label>N&uacute;mero exterior<input type="text" name="numeroExterior" id="numeroExterior"></label></td>
									<td><label>N&uacute;mero interior<input name="numeroInterior" type="text" id="numeroInterior" value=""></label></td>
								</tr>
								<tr>
									<td><label>Colonia<input type="text" name="colonia" id="colonia"></label></td>
									<td><label>C&oacute;digo postal<input type="text" name="codigoPostal" id="codigoPostal"></label></td>
									<td><label>Ciudad<input name="ciudad" type="text" id="ciudad" value=""></label></td>
								</tr>
								<tr>
									<td><label>Estado<input type="text" name="estado" id="estado"></label></td>
									<td><label>Pa&iacute;s<input type="text" name="pais" id="pais"></label></td>
									<td></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td><input type="submit" name="pagar" id="pagar" value="Pagar"></td>
									<td><input type="reset" name="cancel" id="cancel"value="Cancelar"></td>
								</tr>								
							</table>												
						</form>
					</div>
				</div>
			</div>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/websiteBidOn/pie.php'; ?>
</body>
</html>
