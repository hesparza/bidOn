<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/bidOn-ws/src/main/modelo/Articulo.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/bidOn-ws/src/main/modelo/Calificacion.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/bidOn-ws/src/main/modelo/Categoria.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/bidOn-ws/src/main/modelo/Direccion.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/bidOn-ws/src/main/modelo/Envio.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/bidOn-ws/src/main/modelo/EstadoSubasta.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/bidOn-ws/src/main/modelo/EstadoUsuario.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/bidOn-ws/src/main/modelo/Imagen.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/bidOn-ws/src/main/modelo/Mensaje.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/bidOn-ws/src/main/modelo/Oferta.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/bidOn-ws/src/main/modelo/Pago.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/bidOn-ws/src/main/modelo/Rol.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/bidOn-ws/src/main/modelo/Subasta.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/bidOn-ws/src/main/modelo/TarjetaCredito.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/bidOn-ws/src/main/modelo/TarjetaCreditoUsuario.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/bidOn-ws/src/main/modelo/TipoEnvio.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/bidOn-ws/src/main/modelo/TipoPago.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/bidOn-ws/src/main/modelo/TipoSubasta.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/bidOn-ws/src/main/modelo/Usuario.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/bidOn-ws/src/main/modelo/UsuarioDireccion.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/bidOn-ws/src/main/recursos/Configuracion.php';

class LecturaMySql {
	private $_conn;
	
    const NOMBRE_METODO = 'establecer';
    
    function seleccionar($objeto, $clase) {
        $consultaCol = 'SELECT ';
        $consultaVal = '';
        $consultaWhere = ' WHERE ';
        
        $arr = get_class_methods($clase);
        $metodosEstablecer = array_values(array_filter($arr, function($var){return preg_match('/' . self::NOMBRE_METODO . '/', $var);}));
        $parametrosNecesarios = preg_replace('/' . self::NOMBRE_METODO . '/', '', $metodosEstablecer);        
        $parametrosNecesarios = array_map(function($word) { return lcfirst($word); }, $parametrosNecesarios);        
//         echo '$parametrosNecesarios = '; print_r($parametrosNecesarios);
        	//Hay parametros en particular para hacer la consulta?
        	if (isset($objeto)) {
        		foreach (get_object_vars($objeto) as $llave => $valor) {
        			$objTmp= new $clase();
        			$comillas = "";
        			if (gettype ($llave) == "string") {
        				$comillas = "'";
        			} 

					if(property_exists($objTmp, $llave))
					{
// 						echo '   valor= ' . $valor .'   ';
						$consultaWhere .= $this->aFormatoDeBD($llave) . ' = ' . $comillas . $this->aFormatoDeBD($valor) . $comillas . ' and ';
					} else {
		                return 'El objeto recibido no es correcto';
		            }		            
        		}
        		foreach ($parametrosNecesarios as $llave => $valor) {
        			$consultaCol .= $this->aFormatoDeBD($valor) . ',';
        		}        		
        		$consultaCol = $this->eliminarComaAlFinal($consultaCol) ; //Remover la ultima coma
        		$consultaWhere = $this->eliminarAndAlFinal($consultaWhere); //Remover la ultima coma
        		$consulta = $consultaCol . ' FROM ' . $clase . '' . $consultaWhere . ' ;';
            } else {
            	foreach ($parametrosNecesarios as $llave => $valor) {
	            	$consultaCol .= $this->aFormatoDeBD($valor) . ',';
// 	            	$nObjeto->$metodosEstablecer[$llave]($objeto->{$valor});            	
            	}
            	$consultaCol = $this->eliminarComaAlFinal($consultaCol); //Remover la ultima coma
            	$consulta = $consultaCol . ' FROM ' . $this->aFormatoDeBD($clase) . ';';
            }
// 		echo '===== CONSULTA =====> ' . $consulta . '           ';
        $this->abrirConexion();
		if ($resultado = $this->_conn->query($consulta)) {
			if ($resultado->num_rows > 0) {
				while ($columnaInfo = $resultado->fetch_field()){
					$arrColumnaTipo[] = $columnaInfo->type;
				}
	// 			printf (" \n");
				$i = 0;
				$nObjeto = new $clase();
				while ($tupla = $resultado->fetch_array(MYSQLI_ASSOC)) {
					foreach ($parametrosNecesarios as $llave => $valor) {					
	// 					printf (" | %s  (%d)| ", $tupla[$this->aFormatoDeBD($valor)], $arrColumnaTipo[$i]);
						switch ($arrColumnaTipo[$i]) {
							case 3:
								settype($tupla[$this->aFormatoDeBD($valor)], 'int');
								$nObjeto->$valor = $tupla[$this->aFormatoDeBD($valor)];
								break;
							case 5:
								settype($tupla[$this->aFormatoDeBD($valor)], 'float');
								$nObjeto->$valor = $tupla[$this->aFormatoDeBD($valor)];
								break;
							default:
								$nObjeto->$valor = $tupla[$this->aFormatoDeBD($valor)];
								break;
						}
						
						$i++;
					}
					$i = 0;				
	// 				printf (" \n");
					$objResultado[] = $nObjeto;
					$nObjeto = new $clase();
				}
	// 			print_r($objResultado);
			} else {
				$this->cerrarConexion();
				return 'No se encontraron resultados para la consulta';
			}
		} else {
			$error = $this->_conn->error;
			$this->cerrarConexion();
			return 'Error al realizar consulta: ' . $error;
		}	

		$this->cerrarConexion();
		return sizeof($objResultado) == 1 ? $objResultado[0] : $objResultado;  
    }
    
    /**
     * Elimina la ultima coma al final del string $cadena
     */
    private function eliminarComaAlFinal($cadena) {
    	return preg_replace('/,$/','', $cadena);
    }
    
    /**
     * Elimina ultimo "and"
     */
    private function eliminarAndAlFinal($cadena) {
    	return preg_replace('/and $/','', $cadena);
    }
    
    /**
     * Convierte el string $cadena al formato utilizado en la base de datos
     * que es: todas las letras minusculas y guiones bajos en lugar de espacios.
     */
    private function aFormatoDeBD($cadena) {
    	return ucfirst(preg_replace('/\B([A-Z])/', '_$1', $cadena));
    }
    
    private function abrirConexion() {
    	$this->_conn = mysqli_connect(Configuracion::URL, Configuracion::USUARIO, Configuracion::PASSWD, Configuracion::BASEDEDATOS);
    	if ($this->_conn->connect_errno) {
    		return ("Falló la conexión: " . $this->_conn->connect_error);
    	}    	
    }

    private function cerrarConexion() {
        mysqli_close($this->_conn);
    }

	/**
	Obtener todos los datos
	**/
	function seleccionarArticulos() {
		return $this->seleccionar(NULL, 'Articulo');
	}
	function seleccionarCalificaciones() {
		return $this->seleccionar(NULL, 'Calificacion');
	}
	function seleccionarCategorias() {
		return $this->seleccionar(NULL, 'Categoria');
	}
	function seleccionarDirecciones() {
		return $this->seleccionar(NULL, 'Direccion');
	}
	function seleccionarEnvios() {
		return $this->seleccionar(NULL, 'Envio');
	}
	function seleccionarEstadoSubastas() {
		return $this->seleccionar(NULL, 'EstadoSubasta');
	}
	function seleccionarEstadoUsuarios() {
		return $this->seleccionar(NULL, 'EstadoUsuario');
	}
	function seleccionarImagenes() {
		return $this->seleccionar(NULL, 'Imagen');
	}
	function seleccionarMensajes() {
		return $this->seleccionar(NULL, 'Mensaje');
	}
	function seleccionarOfertas() {
		return $this->seleccionar(NULL, 'Oferta');
	}
	function seleccionarPagos() {
		return $this->seleccionar(NULL, 'Pago');
	}
	function seleccionarRoles() {
		return $this->seleccionar(NULL, 'Rol');
	}
	function seleccionarSubastas() {
		return $this->seleccionar(NULL, 'Subasta');
	}
	function seleccionarTarjetaCreditos() {
		return $this->seleccionar(NULL, 'TarjetaCredito');
	}
	function seleccionarTarjetaCreditoUsuarios() {
		return $this->seleccionar(NULL, 'TarjetaCreditoUsuario');
	}
	function seleccionarTipoEnvios() {
		return $this->seleccionar(NULL, 'TipoEnvio');
	}
	function seleccionarTipoPagos() {
		return $this->seleccionar(NULL, 'TipoPago');
	}
	function seleccionarTipoSubastas() {
		return $this->seleccionar(NULL, 'TipoSubasta');
	}
	function seleccionarUsuarios() {
		return $this->seleccionar(NULL, 'Usuario');
	}
	function seleccionarUsuarioDirecciones() {
		return $this->seleccionar(NULL, 'UsuarioDireccion');
	}


	/**
	Obtener todos los datos por indices
	**/
	function seleccionarArticuloPorId($id) {
		return $this->seleccionar($id,'Articulo');
	}
	function seleccionarCalificacionPorId($id) {
		return $this->seleccionar($id,'Calificacion');
	}
	function seleccionarCalificacionPorSubastaId($subastaId) {
		return $this->seleccionar($subastaId,'Calificacion');
	}
	function seleccionarCalificacionPorUsuarioCalificaId($usuarioCalificaId) {
		return $this->seleccionar($usuarioCalificaId,'Calificacion');
	}
	function seleccionarCalificacionPorUsuarioCalificadoId($usuarioCalificadoId) {
		return $this->seleccionar($usuarioCalificadoId,'Calificacion');
	}
	function seleccionarCategoriaPorId($id) {
		return $this->seleccionar($id,'Categoria');
	}
	function seleccionarDireccionPorId($id) {
		return $this->seleccionar($id,'Direccion');
	}
	function seleccionarEnvioPorId($id) {
		return $this->seleccionar($id,'Envio');
	}
	function seleccionarEnvioPorDireccionId($direccionId) {
		return $this->seleccionar($direccionId,'Envio');
	}
	function seleccionarEnvioPorSubastaId($subastaId) {
		return $this->seleccionar($subastaId,'Envio');
	}
	function seleccionarEnvioPorTipoEnvioId($tipoEnvioId) {
		return $this->seleccionar($tipoEnvioId,'Envio');
	}
	function seleccionarEstadoSubastaPorId($id) {
		return $this->seleccionar($id,'EstadoSubasta');
	}
	function seleccionarEstadoUsuarioPorId($id) {
		return $this->seleccionar($id,'EstadoUsuario');
	}
	function seleccionarImagenPorId($id) {
		return $this->seleccionar($id,'Imagen');
	}
	function seleccionarImagenPorArticuloId($articuloId) {
		return $this->seleccionar($articuloId,'Imagen');
	}
	function seleccionarMensajePorId($id) {
		return $this->seleccionar($id,'Mensaje');
	}
	function seleccionarMensajePorSubastaId($subastaId) {
		return $this->seleccionar($subastaId,'Mensaje');
	}
	function seleccionarMensajePorUsuarioId($usuarioId) {
		return $this->seleccionar($usuarioId,'Mensaje');
	}
	function seleccionarOfertaPorId($id) {
		return $this->seleccionar($id,'Oferta');
	}
	function seleccionarOfertaPorSubastaId($subastaId) {
		return $this->seleccionar($subastaId,'Oferta');
	}
	function seleccionarOfertaPorUsuarioId($usuarioId) {
		return $this->seleccionar($usuarioId,'Oferta');
	}
	function seleccionarPagoPorId($id) {
		return $this->seleccionar($id,'Pago');
	}
	function seleccionarPagoPorSubastaId($subastaId) {
		return $this->seleccionar($subastaId,'Pago');
	}
	function seleccionarPagoPorTipoPagoId($tipoPagoId) {
		return $this->seleccionar($tipoPagoId,'Pago');
	}
	function seleccionarRolPorId($id) {
		return $this->seleccionar($id,'Rol');
	}
	function seleccionarSubastaPorId($id) {
		return $this->seleccionar($id,'Subasta');
	}
	function seleccionarSubastaPorArticuloId($articuloId) {
		return $this->seleccionar($articuloId,'Subasta');
	}
	function seleccionarSubastaPorCategoriaId($categoriaId) {
		return $this->seleccionar($categoriaId,'Subasta');
	}
	function seleccionarSubastaPorEstadoId($estadoId) {
		return $this->seleccionar($estadoId,'Subasta');
	}
	function seleccionarSubastaPorFechaInicio($fechaInicio) {
		return $this->seleccionar($fechaInicio,'Subasta');
	}
	function seleccionarSubastaPorTipoSubastaId($tipoSubastaId) {
		return $this->seleccionar($tipoSubastaId,'Subasta');
	}
	function seleccionarSubastaPorUsuarioAprobo($usuarioAprobo) {
		return $this->seleccionar($usuarioAprobo,'Subasta');
	}
	function seleccionarSubastaPorUsuarioCreo($usuarioCreo) {
		return $this->seleccionar($usuarioCreo,'Subasta');
	}
	function seleccionarSubastaPorUsuarioGanador($usuarioGanador) {
		return $this->seleccionar($usuarioGanador,'Subasta');
	}
	function seleccionarTarjetaCreditoPorId($id) {
		return $this->seleccionar($id,'TarjetaCredito');
	}
	function seleccionarTarjetaCreditoUsuarioPorTarjetaCreditoId($tarjetaCreditoId) {
		return $this->seleccionar($tarjetaCreditoId,'TarjetaCreditoUsuario');
	}
	function seleccionarTarjetaCreditoUsuarioPorUsuarioId($usuarioId) {
		return $this->seleccionar($usuarioId,'TarjetaCreditoUsuario');
	}
	function seleccionarTipoEnvioPorId($id) {
		return $this->seleccionar($id,'TipoEnvio');
	}
	function seleccionarTipoPagoPorId($id) {
		return $this->seleccionar($id,'TipoPago');
	}
	function seleccionarTipoSubastaPorId($id) {
		return $this->seleccionar($id,'TipoSubasta');
	}
	function seleccionarUsuarioPorId($id) {
		return $this->seleccionar($id,'Usuario');
	}
	function seleccionarUsuarioPorNomUsuario($nomUsuario) {
		return $this->seleccionar($nomUsuario,'Usuario');
	}
	function seleccionarUsuarioPorEstadoUsuarioId($estadoUsuarioId) {
		return $this->seleccionar($estadoUsuarioId,'Usuario');
	}
	function seleccionarUsuarioPorRolId($rolId) {
		return $this->seleccionar($rolId,'Usuario');
	}
	function seleccionarUsuarioDireccionPorDireccionId($direccionId) {
		return $this->seleccionar($direccionId,'UsuarioDireccion');
	}
	function seleccionarUsuarioDireccionPorUsuarioId($usuarioId) {
		return $this->seleccionar($usuarioId,'UsuarioDireccion');
	}
}
