<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/bidOn-ws/src/main/controlador/ProcesadorDeRespuestas.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/bidOn-ws/src/main/negocios/Negocios.php';
class Controlador {	
	private $_formato;
	private $_prefijo;
	private $_parametros;
	private $_procesadorDeRespuestas;
	private $_negocios;
	
	function __construct($parametros) {
		if((isset($_SERVER['HTTP_ACCEPT'])) && false !== strpos($_SERVER['HTTP_ACCEPT'], "html")) {
		    $this->_formato = "html";
		} else {
		    $this->_formato = "json";
		}
		
		switch($_SERVER['REQUEST_METHOD']) {
			case 'GET':
				$this->_prefijo = 'obtener';
				break;
			case 'POST':
				$this->_prefijo = 'agregar';
				break;
			case 'PUT':
				$this->_prefijo = 'editar';
				break;
			case 'DELETE':
				$this->_prefijo = 'remover';
				break;
			default:
				$this->_prefijo = 'obtener';
				break;
		}

		$this->_parametros = $parametros;		
		$this->procesadorDeRespuestas = new ProcesadorDeRespuestas();

		set_exception_handler(function ($e) use ($_formato) {
			http_response_code($e->getCode());
			$this->procesadorDeRespuestas->procesarRespuesta( $this->_formato, $e->getCode(), $e->getMessage(), '');			
		});

		$this->_negocios =  new Negocios();
	}

	/**
	 * Forma el complemento del nombre del metodo a llamar 
	 * dependiendo de los parametros enviados en el URL
	 */
	function complementarNombre() {
		if (strcmp($_SERVER['REQUEST_METHOD'], 'GET') != 0){
			for ($i = 2; $i < sizeof($this->_parametros); $i++) {
				if ($this->_parametros[$i] != '') {
					$resultado = $resultado . 'Por' .ucfirst($this->_parametros[$i]);
				}
			}
		} else {
			$esLlave = true;
			for ($i = 2; $i < sizeof($this->_parametros); $i++) {
				if ($this->_parametros[$i] != '' && $esLlave) {
					$resultado = $resultado . 'Por' .ucfirst($this->_parametros[$i]);
					$esLlave = false;
				} else {
					$esLlave = true;
				}					
			}
		}
		return $resultado;
	}
	
	/**
	 * Obtiene los parametros de la llamada dependiendo del tipo de llamada
	 */
	function obtenerParametros() {
		$cuerpo = file_get_contents('php://input');
		if (isset($cuerpo) == false && $cuerpo !== "") {
			return '';
		}			
		switch($_SERVER['REQUEST_METHOD']) {
			case 'GET':	
				$esLlave = true;
				$resultado = array();
				for ($i = 2; $i < sizeof($this->_parametros); $i++) {
					if ($esLlave) {
						$llave = $this->_parametros[$i];
						$esLlave = !$esLlave;
					} else {
						$resultado[$llave] = $this->_parametros[$i];
						$esLlave = !$esLlave;
					}
				}		
				$resultado = (object)$resultado;
				return $resultado;
				break;
			case 'POST':
			case 'PUT':
			case 'DELETE':
				return json_decode($cuerpo);
				break;
			default:
				return '';
				break;
		}
	}

	/**
	 * Determina el método que hay que llamar
	 */
	function rutearLlamada() {
		$count = sizeof($this->_parametros);
		$count = $count - 2;
		if (($count >= 1) || $this->_prefijo === 'agregar' || $this->_prefijo === 'editar' || $this->_prefijo === 'remover') {
			$hayParametros = TRUE;
		}
		// rutear la llamada a su metodo correspondiente
		switch($this->_parametros[1]) {
			case 'Articulo':
			case 'Calificacion':
			case 'Categoria':
			case 'Direccion':
			case 'Envio':
			case 'EstadoSubasta':
			case 'EstadoUsuario':
			case 'Imagen':
			case 'Mensaje':
			case 'Oferta':
			case 'Pago':
			case 'Rol':
			case 'Subasta':
			case 'TarjetaCredito':
			case 'TarjetaCredito_Usuario':
			case 'TipoEnvio':
			case 'TipoPago':
			case 'TipoSubasta':
			case 'Usuario':
			case 'UsuarioDireccion':
	        	$nombreMetodo = $this->_prefijo . $this->_parametros[1] . $this->complementarNombre();
	        	if(method_exists($this->_negocios, $nombreMetodo)) {
	        		$datos = $hayParametros ? $this->_negocios->$nombreMetodo($this->obtenerParametros()) : $this->_negocios->$nombreMetodo();
	        	} else {
	        		throw new Exception("Metodo no disponible", 405);
	        	}
        		break;
			case 'Articulos':
			case 'Calificaciones':
			case 'Categorias':
			case 'Direcciones':
			case 'Envios':
			case 'EstadoSubastas':
			case 'EstadoUsuarios':
			case 'Imagenes':
			case 'Mensajes':
			case 'Ofertas':
			case 'Pagos':
			case 'Roles':
			case 'Subastas':
			case 'TarjetaCreditos':
			case 'TarjetaCreditoUsuarios':
			case 'TipoEnvios':
			case 'TipoPagos':
			case 'TipoSubastas':
			case 'Usuarios':
			case 'UsuarioDirecciones':
				$nombreMetodo = $this->_prefijo . $this->_parametros[1];
				if(method_exists($this->_negocios, $nombreMetodo)) {
					$datos = $this->_negocios->$nombreMetodo();
				} else {
					throw new Exception("Metodo no disponible", 405);
				}				
				break;
			case 'InicioSesion':
			case 'RegistroNuevoUsuario':
			case 'RegistroNuevaSubasta':
			case 'DatosSubasta':
			case 'RegistroNuevaOferta':
			case 'SubastasActivas':
			case 'MisSubastas':
			case 'SubastasPendientes':
			case 'SubastasInactivas':
			case 'ActivarSubasta':
			case 'DesactivarSubasta':
			case 'ActualizarGanadores':
			case 'RealizarPago':
			case 'CantidadPago':
			case 'ObenerUsuariosCompletos':
				$nombreMetodo =  lcfirst($this->_parametros[1]);
				if(method_exists($this->_negocios, $nombreMetodo)) {
					$datos = $this->_negocios->$nombreMetodo($this->obtenerParametros());
				} else {
					throw new Exception("Metodo no disponible", 405);
				}				
				break;
		    default:
		        throw new Exception("URL desconocida", 404);
		        break;
		}
		$this->procesadorDeRespuestas->procesarRespuesta("json", 200, "Ok", $datos);
	}
}
?>
