<?php
error_reporting ( E_ERROR | E_PARSE );
session_start ();
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
<script type="text/javascript" src="js/nuevaSubasta.js"></script>
</head>
<body>
	<div class="wrapper-container">
		<div class="wrapper-header">
	<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/websiteBidOn/encabezado.php'; ?>
  <div class="wrapper-content">  
	<?php //include_once $_SERVER['DOCUMENT_ROOT'] . '/websiteBidOn/carrusel.php'; ?>
  	<div class="content">
					<h2>Nueva subasta</h2>
					<div class="comentarios" id="comentarios">
						<form name="FormaNuevaSubasta" method="post" action="" id="FormaNuevaSubasta">
							<table width="100%" border="0" cellspacing="10" cellpadding="0">
								<tr>
									<td><label>Nombre de Artículo <input type="text" name="articulo" id="articulo">
									</label></td>
									<td><label>Precio <input type="text" name="precio" id="precio">
									</label></td>
									<td><label>Cantidad <input type="text" name="cantidad" id="cantidad">
									</label></td>
								</tr>
								<tr>
									<td>
										<label>Categoría 
											<select name="categoria" id="categoria">
													<option id="categoriaDefault" value="0">Seleccionar...</option>
											</select>
										</label>
									</td>
									<td>
										<label>Tipo de subasta 
											<select name="tipoSubasta" id="tipoSubasta">
													<option id="tipoSubastaDefault" value="0">Seleccionar...</option>
											</select>
										</label>									
									</td>
									<td></td>
								</tr>
								<tr>									
									<td colspan="3">
									<input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="300000" />
									<?php 									
									echo '<input type="hidden" id="NOM_USUARIO" name="NOM_USUARIO" value="'.$_SESSION["nomUsuario"].'" />';
									?>									
										<div>
											<label for="fileselect">Seleccione las imagenes:</label>
											<input type="file" id="fileselect" name="fileselect[]" multiple="multiple" />
											<div id="filedrag">o arrastre las imagenes en esta area</div>
										</div>
										
										<!-- 
										<label>Imagen 
											<input type="file" id="imagenes" name="files[]" multiple />											
										</label>
										 -->
										 <div id="progress"></div>

										 <div id="messages"><p></p></div>
									</td>
								</tr>
								<tr>
									<td><label>Fecha de inicio <input name="fechainicio"
											type="date" id="fechainicio" value="">
									</label></td>
									<td><label>Fecha de finalización <input name="fechafin"
											type="date" id="fechafin" value="">
									</label></td>
									<td>&nbsp;</td>
								</tr>

								<tr>
									<td colspan="3"><label>Descripción <textarea name="opinion"
												cols="" rows="5" class="descripcion" id="descripcion"></textarea>
									</label></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td><input type="submit" name="save" id="save" value="Guardar"></td>
									<td><input type="reset" name="cancel" id="cancel"
										value="Cancelar"></td>
								</tr>
							</table>
						</form>
					</div>
				</div>
			</div>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/websiteBidOn/pie.php'; ?>


</body>
</html>
