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
<script src="js\opiniones.js"></script>
<div class="wrapper-container">
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/websiteBidOn/encabezado.php'; ?>
  <div class="wrapper-content">  
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/websiteBidOn/carrusel.php'; ?>
        <div class="content">

          <h2>Opiniones</h2>
          <div class="comentarios">
          	<div class="item"><img src="img/productPreview01.jpg" width="200" height="200"><span class="itemname">Smart TV 4K</span></div>
            <div class="info">
                <div class="stars"><span class="tagname">Califica este producto</span><span class="starOn">&nbsp;</span><span class="starOn">&nbsp;</span><span class="starOn">&nbsp;</span><span class="starOff">&nbsp;</span><span class="starOff">&nbsp;</span></div>
                <h3>Comparte tu opinión</h3>

                <textarea name="opinion" id="opinion" cols="" rows="8"></textarea>
                <div class="buttons"><input name="submit" type="submit" value="Enviar"><input name="reset" type="reset" value="Borrar" onclick="BorraComments()"></div>
			</div>
            </div>
        </div>
  </div>
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/websiteBidOn/pie.php'; ?>
</div>
</body>
</html>
