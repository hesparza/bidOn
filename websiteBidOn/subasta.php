<?php
error_reporting ( E_ERROR | E_PARSE );
session_start ();
echo '<input type="hidden" id="ID_SUBASTA" name="ID_SUBASTA" value="'.$_GET["id"].'" />';
echo '<input type="hidden" id="NOM_USUARIO" name="NOM_USUARIO" value="'.$_SESSION["nomUsuario"].'" />';
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
<script type="text/javascript" src="js/jcarousel.js"></script>
<script type="text/javascript" src="js/subasta.js"></script>
</head>
<body>
	<div class="wrapper-container">
		<div class="wrapper-header">
	<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/websiteBidOn/encabezado.php'; ?>
  <div class="wrapper-content">  	
  	<div class="content">
					<h2>Subasta</h2>
					<div class="detalles">
							<table width="100%" border="0" cellpadding="10" cellspacing="0" id="tablaDetalles">
								<tr>
									<td colspan="2">
										<h3 id="nombre"></h3>
									</td>
								</tr>
								<tr>
									<td>
									<div class="jcarousel-wrapper">
						                <div data-jcarousel="true" data-wrap="circular" class="jcarousel">
						                    <ul>
					           		 			<output id="imagenes"></output>	
						                    </ul>
						                </div>
						                <a data-jcarousel-control="true" data-target="-=1" href="#" class="jcarousel-control-prev">&lsaquo;</a>
						                <a data-jcarousel-control="true" data-target="+=1" href="#" class="jcarousel-control-next">&rsaquo;</a>
					           		</div>
				
									</td>
									<td>
<!-- 										<output id="nomUsuario"><label></label></output> -->
										<output id="estado"><label></label></output>
										<output id="precioInicial"><label></label></output>
										<output id="tipo"><label></label></output>
										<output id="cantidad"><label></label></output>
										<output id="fechainicio"><label></label></output>
										<output id="fechafin"><label></label></output>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<output ><label>Descripcion</label></output>
									</td>
								</tr>								
								<tr>
									<td colspan="2">
										<output id="descripcion"><label></label></output>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<output><label id="ofertas">Ofertas:</label></output>
									</td>
								<tr>
									<td colspan="2">	
              						<table width="100%" border="0" align="right" cellpadding="10" cellspacing="0" id="tablaOfertas">
											<tbody>
												<!-- Aqui van las ofertas -->
											</tbody> 											
										</table>
									</td>
								</tr>
								<tr>
									<td>
										<output><label id="textoOfertar">Ofertar:</label></output>
									</td>
								</tr>																
								<tr>
									<td>
										<input type="text" name="cantidadOferta" id="cantidadOferta">
										<input type="button" id="btnOfertar" class="btnEnviar" value="Ofertar"/>
									</td>
								</tr>
							</table>
					</div>
				</div>
			</div>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/websiteBidOn/pie.php'; ?>
</body>
</html>