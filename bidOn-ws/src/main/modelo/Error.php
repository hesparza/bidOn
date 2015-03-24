<?php
class Error {
	var $mensaje;
	var $error;

	function __construct($mensaje, $error) {
		$this->mensaje = $mensaje;
		$this->error = $error;
	}

	function obtenerMensaje() { return $this->mensaje; }
	function establecerMensaje($mensaje) { $this->mensaje = $mensaje; }
	function obtenerError() { return $this->error; }
	function establecerError($error) { $this->error = $error; }
}
?>