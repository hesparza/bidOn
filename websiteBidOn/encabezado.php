<?php
error_reporting(E_ERROR | E_PARSE);
session_start();
?>
<div class="wrapper-header">
    <div class="header"> <a href="#"><img src="img/logoBidOn.png" hspace="15" vspace="10" border="0" /></a>
      <ul class="mainmenu">
        <li><a href="index.php">Inicio</a></li>
        <?php 
	        if(isset($_SESSION["nomUsuario"])) {
	      		if (isset($_SESSION["rol"]) && isset($_SESSION["estadoUsuario"]) &&
	      			strcmp($_SESSION["rol"],'Usuario') == 0 && 
	      			strcmp($_SESSION["estadoUsuario"],'Activo') == 0) {
	      			echo '<li><a href="nuevaSubasta.php">Crear Subasta</a></li>';
	      			echo '<li><a href="misSubastas.php">Mis Subastas</a></li>';
	      		} elseif(isset($_SESSION["rol"]) && isset($_SESSION["estadoUsuario"]) &&
	      			strcmp($_SESSION["rol"],'Administrador') == 0 && 
	      			strcmp($_SESSION["estadoUsuario"],'Activo') == 0) {
	      			echo '<li><a href="administrador.php">Administrar Subastas</a></li>';
	      			echo '<li><a href="#">Administrar Usuarios</a></li>';	      			
	      		}
	        } else {
	        	echo '<li><a href="opiniones.php">Opiniones</a></li>';
	        	echo '<li><a href="contacto.php">Contacto</a></li>';
	        }
	        
	    ?>
      </ul>
      <ul class="submainmenu">
        <?php 
	        if(isset($_SESSION["nomUsuario"])) {
	      		echo '<li><u><a href="#">'. $_SESSION["nomUsuario"] .'</a></u></li>';
	      		echo '<li><a href="destruirSesion.php">Salir</a></li>';
	      		if (isset($_SESSION["rol"]) && strcmp($_SESSION["rol"],'Activo') == 0) {
	      			
	      		}
	        } else {
	        	echo '<li><a href="registro.php">Registrarse!</a></li>';
	        	echo '<li><a href="inicioSesion.php">Ingresar</a></li>';
	        }	        	
        ?>                
      </ul>
    </div>
  </div>