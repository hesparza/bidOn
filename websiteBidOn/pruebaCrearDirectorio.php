<?php
error_reporting ( E_ERROR | E_PARSE );
session_start ();
$fn = (isset($_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : false);
$path = $_SERVER['DOCUMENT_ROOT'] . '/websiteBidOn/imagenes_subastas/' . 'test' . '/tmp/';
if (!file_exists($path)) {
	mkdir($path, 0777, true);
}
echo 'Path: ' . $path . '';
echo '<br/> $_SESSION["nomUsuario"]= '. $_SESSION["nomUsuario"];
echo '<br /> fn= ' .$fn;
?>