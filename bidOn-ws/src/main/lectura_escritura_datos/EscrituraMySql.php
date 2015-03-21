<?php
include_once 'src/main/modelo/Articulo.php';
include_once 'src/main/modelo/Calificacion.php';
include_once 'src/main/modelo/Categoria.php';
include_once 'src/main/modelo/Direccion.php';
include_once 'src/main/modelo/Envio.php';
include_once 'src/main/modelo/EstadoSubasta.php';
include_once 'src/main/modelo/EstadoUsuario.php';
include_once 'src/main/modelo/Imagen.php';
include_once 'src/main/modelo/Mensaje.php';
include_once 'src/main/modelo/Oferta.php';
include_once 'src/main/modelo/Pago.php';
include_once 'src/main/modelo/Rol.php';
include_once 'src/main/modelo/Subasta.php';
include_once 'src/main/modelo/TarjetaCredito.php';
include_once 'src/main/modelo/TarjetaCreditoUsuario.php';
include_once 'src/main/modelo/TipoEnvio.php';
include_once 'src/main/modelo/TipoPago.php';
include_once 'src/main/modelo/TipoSubasta.php';
include_once 'src/main/modelo/Usuario.php';
include_once 'src/main/modelo/UsuarioDireccion.php';

include_once 'src/main/recursos/Configuracion.php';
class EscrituraMySql {
	private $_conn;
	const NOMBRE_METODO = 'establecer';

	/**
	* Inserta un nuevo objeto de una determinada clase
	* @param unknown $objeto
	* @param unknown $clase
	* @return number|string
	*/
	function insertar($objeto, $clase) {
		$nObjeto = new $clase();
		$consultaCol = 'INSERT INTO ' . $this->aFormatoDeBD($clase) . ' (';
		$consultaVal = 'VALUES (';
		
		$arr = get_class_methods($clase);
		$metodosEstablecer = array_filter($arr, function($var){return preg_match('/' . self::NOMBRE_METODO . '/', $var);});
		$metodosEstablecer = array_values($metodosEstablecer);
		$parametrosNecesarios = preg_replace('/' . self::NOMBRE_METODO . '/', '', $metodosEstablecer);        
		$parametrosNecesarios = array_map(function($word) { return lcfirst($word); }, $parametrosNecesarios);
		foreach ($parametrosNecesarios as $llave => $valor) {
			if(property_exists($objeto, $valor)) {
				$consultaCol .= $this->aFormatoDeBD($valor) . ','; 
				$consultaVal .= "'" . $objeto->{$valor} . "'" . ',';
				$nObjeto->$metodosEstablecer[$llave]($objeto->{$valor});
			} else {
				return 'El objeto recibido no es correcto. La propiedad: ' . $valor . ' no se encuentra.';
			}
		}
		$consultaCol = $this->eliminarComaAlFinal($consultaCol) . ') '; //Remover la ultima coma
		$consultaVal = $this->eliminarComaAlFinal($consultaVal) . ');'; //Remover la ultima coma
		$consulta = $consultaCol . $consultaVal;
		
		$this->abrirConexion();
		if ($this->_conn->query($consulta) == FALSE) {			
			$error = $this->_conn->error;
			$this->cerrarConexion();
			return 'Error al insertar objeto: ' . $error;
		}
		$this->cerrarConexion();
		return json_encode($nObjeto);  
	}

	/**
	 * Actualizar una tupla de la base de datos a partir de un objeto
	 * @param unknown $objeto
	 * @param unknown $clase
	 * @return number|string
	 */
	function actualizar($objeto, $clase) {
		$nObjeto = new $clase();
		$consultaCol = 'UPDATE ' . $this->aFormatoDeBD($clase) . ' SET ';
		
		$arr = get_class_methods($clase);
		$metodosEstablecer = array_filter($arr, function($var){return preg_match('/' . self::NOMBRE_METODO . '/', $var);});
		$metodosEstablecer = array_values($metodosEstablecer);
		$parametrosNecesarios = preg_replace('/' . self::NOMBRE_METODO . '/', '', $metodosEstablecer);
		$parametrosNecesarios = array_map(function($word) { return lcfirst($word); }, $parametrosNecesarios);
		foreach ($parametrosNecesarios as $llave => $valor) {
			if(property_exists($objeto, $valor)) {
				if (strcmp($valor, 'id') != 0) {
					$consultaCol .= $this->aFormatoDeBD($valor) . ' = ' . $this->obtenerPorTipo($objeto->{$valor}) . ' ,';
				}
				$nObjeto->$metodosEstablecer[$llave]($objeto->{$valor});
			} else {
				return 'El objeto recibido no es correcto. La propiedad: ' . $valor . ' no se encuentra.';
			}
		}
		$consultaCol = $this->eliminarComaAlFinal($consultaCol); //Remover la ultima coma
		$consulta = $consultaCol . ' WHERE id = ' . $objeto->{'id'};
		$this->abrirConexion();
		if ($this->_conn->query($consulta) == FALSE) {
			$error = $this->_conn->error;
			$this->cerrarConexion();
			return 'Error al actualizar objeto: ' . $error;
		}
		$this->cerrarConexion();
		return json_encode($nObjeto);		
	}
	
	function borrar($objeto, $clase) {
		$consulta = 'DELETE FROM ' . $this->aFormatoDeBD($clase) . ' WHERE id=';
		
		if(property_exists($objeto, 'id')) {
			$consulta .= $objeto->{'id'};
		} 
		else {
			return 'El objeto recibido no es correcto. No se encuentra la propiedad id.';
		}
		
		$this->abrirConexion();		
		//Hay almenos una tupla que se puede eliminar?
		$resultado = $this->_conn->query('SELECT * FROM ' . $this->aFormatoDeBD($clase) . ' WHERE id='. $objeto->{'id'});		
		if ($resultado->num_rows < 1) {
			$this->cerrarConexion();
			return 'No se encontraron objetos a eliminar';
		}
		if ($this->_conn->query($consulta) == FALSE) {
			$error = $this->_conn->error;
			$this->cerrarConexion();
			return 'Error al eliminar objeto: ' . $error;
		}
		$this->cerrarConexion();
		return "Ok";
	}
	
	/**
	 * Devuelve la variable segun su tipo
	 */
	private function obtenerPorTipo($variable) {
		if (is_string($variable)){
			return '"'.$variable.'"';
		} else {
			return $variable;
		}
	}

	/**
	* Elimina la ultima coma al final del string $cadena
	*/
	private function eliminarComaAlFinal($cadena) {
// 		return preg_replace('/,$/','', $cadena);
// 		return rtrim($cadena, ',');
		return substr($cadena,0,-1);
	}
	
	/**
	* Convierte el string $cadena al formato utilizado en la base de datos
	* que es: todas las letras minusculas y guiones bajos en lugar de espacios.
	*/
	private function aFormatoDeBD($cadena) {
		return strtolower(preg_replace('/\B([A-Z])/', '_$1', $cadena));
	}
	
	private function abrirConexion() {
		$this->_conn = mysqli_connect(Configuracion::URL, Configuracion::USUARIO, Configuracion::PASSWD, Configuracion::BASEDEDATOS, Configuracion::PUERTO, Configuracion::SOCKET);
		if ($this->_conn->connect_errno) {
			return ("Falló la conexión: " . $this->_conn->connect_error);
		}    	
	}

	private function cerrarConexion() {
		mysqli_close($this->_conn);
	}

	/**
	Insertar datos
	**/
	function insertarArticulo($articulo) {
		return $this->insertar($articulo,'Articulo');
	}
	function insertarCalificacion($calificacion) {
		return $this->insertar($calificacion,'Calificacion');
	}
	function insertarCategoria($categoria) {
		return $this->insertar($categoria,'Categoria');
	}
	function insertarDireccion($direccion) {
		return $this->insertar($direccion,'Direccion');
	}
	function insertarEnvio($envio) {
		return $this->insertar($envio,'Envio');
	}
	function insertarEstadoSubasta($estadoSubasta) {
		return $this->insertar($estadoSubasta,'EstadoSubasta');
	}
	function insertarEstadoUsuario($estadoUsuario) {
		return $this->insertar($estadoUsuario,'EstadoUsuario');
	}
	function insertarImagen($imagen) {
		return $this->insertar($imagen,'Imagen');
	}
	function insertarMensaje($mensaje) {
		return $this->insertar($mensaje,'Mensaje');
	}
	function insertarOferta($oferta) {
		return $this->insertar($oferta,'Oferta');
	}
	function insertarPago($pago) {
		return $this->insertar($pago,'Pago');
	}
	function insertarRol($rol) {
		return $this->insertar($rol,'Rol');
	}
	function insertarSubasta($subasta) {
		return $this->insertar($subasta,'Subasta');
	}
	function insertarTarjetaCredito($tarjetaCredito) {
		return $this->insertar($tarjetaCredito,'TarjetaCredito');
	}
	function insertarTarjetaCreditoUsuario($tarjetaCreditoUsuario) {
		return $this->insertar($tarjetaCreditoUsuario,'TarjetaCreditoUsuario');
	}
	function insertarTipoEnvio($tipoEnvio) {
		return $this->insertar($tipoEnvio,'TipoEnvio');
	}
	function insertarTipoPago($tipoPago) {
		return $this->insertar($tipoPago,'TipoPago');
	}
	function insertarTipoSubasta($tipoSubasta) {
		return $this->insertar($tipoSubasta,'TipoSubasta');
	}
	function insertarUsuario($usuario) {
		return $this->insertar($usuario,'Usuario');
	}
	function insertarUsuarioDireccion($usuarioDireccion) {
		return $this->insertar($usuarioDireccion,'UsuarioDireccion');
	}


	/**
	Actualizar datos
	**/
	function actualizarArticulo($articulo) {
		return $this->actualizar($articulo,'Articulo');
	}
	function actualizarCalificacion($calificacion) {
		return $this->actualizar($calificacion,'Calificacion');
	}
	function actualizarCategoria($categoria) {
		return $this->actualizar($categoria,'Categoria');
	}
	function actualizarDireccion($direccion) {
		return $this->actualizar($direccion,'Direccion');
	}
	function actualizarEnvio($envio) {
		return $this->actualizar($envio,'Envio');
	}
	function actualizarEstadoSubasta($estadoSubasta) {
		return $this->actualizar($estadoSubasta,'EstadoSubasta');
	}
	function actualizarEstadoUsuario($estadoUsuario) {
		return $this->actualizar($estadoUsuario,'EstadoUsuario');
	}
	function actualizarImagen($imagen) {
		return $this->actualizar($imagen,'Imagen');
	}
	function actualizarMensaje($mensaje) {
		return $this->actualizar($mensaje,'Mensaje');
	}
	function actualizarOferta($oferta) {
		return $this->actualizar($oferta,'Oferta');
	}
	function actualizarPago($pago) {
		return $this->actualizar($pago,'Pago');
	}
	function actualizarRol($rol) {
		return $this->actualizar($rol,'Rol');
	}
	function actualizarSubasta($subasta) {
		return $this->actualizar($subasta,'Subasta');
	}
	function actualizarTarjetaCredito($tarjetaCredito) {
		return $this->actualizar($tarjetaCredito,'TarjetaCredito');
	}
	function actualizarTarjetaCreditoUsuario($tarjetaCreditoUsuario) {
		return $this->actualizar($tarjetaCreditoUsuario,'TarjetaCreditoUsuario');
	}
	function actualizarTipoEnvio($tipoEnvio) {
		return $this->actualizar($tipoEnvio,'TipoEnvio');
	}
	function actualizarTipoPago($tipoPago) {
		return $this->actualizar($tipoPago,'TipoPago');
	}
	function actualizarTipoSubasta($tipoSubasta) {
		return $this->actualizar($tipoSubasta,'TipoSubasta');
	}
	function actualizarUsuario($usuario) {
		return $this->actualizar($usuario,'Usuario');
	}
	function actualizarUsuarioDireccion($usuarioDireccion) {
		return $this->actualizar($usuarioDireccion,'UsuarioDireccion');
	}


	/**
	Borrar datos
	**/
	function borrarArticulo($articulo) {
		return $this->borrar($articulo,'Articulo');
	}
	function borrarCalificacion($calificacion) {
		return $this->borrar($calificacion,'Calificacion');
	}
	function borrarCategoria($categoria) {
		return $this->borrar($categoria,'Categoria');
	}
	function borrarDireccion($direccion) {
		return $this->borrar($direccion,'Direccion');
	}
	function borrarEnvio($envio) {
		return $this->borrar($envio,'Envio');
	}
	function borrarEstadoSubasta($estadoSubasta) {
		return $this->borrar($estadoSubasta,'EstadoSubasta');
	}
	function borrarEstadoUsuario($estadoUsuario) {
		return $this->borrar($estadoUsuario,'EstadoUsuario');
	}
	function borrarImagen($imagen) {
		return $this->borrar($imagen,'Imagen');
	}
	function borrarMensaje($mensaje) {
		return $this->borrar($mensaje,'Mensaje');
	}
	function borrarOferta($oferta) {
		return $this->borrar($oferta,'Oferta');
	}
	function borrarPago($pago) {
		return $this->borrar($pago,'Pago');
	}
	function borrarRol($rol) {
		return $this->borrar($rol,'Rol');
	}
	function borrarSubasta($subasta) {
		return $this->borrar($subasta,'Subasta');
	}
	function borrarTarjetaCredito($tarjetaCredito) {
		return $this->borrar($tarjetaCredito,'TarjetaCredito');
	}
	function borrarTarjetaCreditoUsuario($tarjetaCreditoUsuario) {
		return $this->borrar($tarjetaCreditoUsuario,'TarjetaCreditoUsuario');
	}
	function borrarTipoEnvio($tipoEnvio) {
		return $this->borrar($tipoEnvio,'TipoEnvio');
	}
	function borrarTipoPago($tipoPago) {
		return $this->borrar($tipoPago,'TipoPago');
	}
	function borrarTipoSubasta($tipoSubasta) {
		return $this->borrar($tipoSubasta,'TipoSubasta');
	}
	function borrarUsuario($usuario) {
		return $this->borrar($usuario,'Usuario');
	}
	function borrarUsuarioDireccion($usuarioDireccion) {
		return $this->borrar($usuarioDireccion,'UsuarioDireccion');
	}

}
?>
