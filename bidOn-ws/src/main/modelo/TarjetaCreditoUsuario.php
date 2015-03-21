<?php
class TarjetaCreditoUsuario {
	var $tarjetaCreditoId;
	var $usuarioId;

	function obtenerTarjetaCreditoId() { return $this->tarjetaCreditoId; }
	function obtenerUsuarioId() { return $this->usuarioId; }

	function establecerTarjetaCreditoId($tarjetaCreditoId) { $this->tarjetaCreditoId = $tarjetaCreditoId; }
	function establecerUsuarioId($usuarioId) { $this->usuarioId = $usuarioId; }
}
?>
