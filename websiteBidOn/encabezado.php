<?php
session_start();
?>
<div class="wrapper-header">
    <div class="header"> <a href="#"><img src="img/logoBidOn.png" hspace="15" vspace="10" border="0" /></a>
      <ul class="mainmenu">
        <li><a href="index.php">Inicio</a></li>
        <li><a href="#">Categoría</a></li>
        <li><a href="#">Producto</a></li>
        <li><a href="opiniones.php">Opiniones</a></li>
        <li><a href="contacto.php">Contacto</a></li>
      </ul>
      <ul class="submainmenu">
        <?php 
	        if(isset($_SESSION["nomUsuario"])) {
	      		echo '<li><u><a href="#">'. $_SESSION["nomUsuario"] .'</a></u></li>';
	        } else {
	        	echo '<li><a href="inicioSesion.php">Ingresar</a></li>';
	        }	        	
        ?>        
        <li><a href="destruirSesion.php">Salir</a></li>
      </ul>
    </div>
  </div>