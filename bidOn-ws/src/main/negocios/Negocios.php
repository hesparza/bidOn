<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/bidOn-ws/src/main/acceso_datos/MantenimientoDeDatos.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/bidOn-ws/src/main/acceso_datos/RecuperacionDeDatos.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/bidOn-ws/src/main/modelo/Error.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/bidOn-ws/src/main/recursos/Configuracion.php';
date_default_timezone_set('America/Mexico_City');
class Negocios {
	private $_mantenimientoDeDatos;
	private $_recuperacionDeDatos;
	function __construct() {
		$this->_mantenimientoDeDatos =  new MantenimientoDeDatos();
		$this->_recuperacionDeDatos =  new RecuperacionDeDatos();
	}

	/**
	 * Reglas de negocio
	 */
	function inicioSesion($usuario) {
		if (!property_exists($usuario, 'nomUsuario') || !property_exists($usuario, 'contrasena')) {
			return new Error('Es necesario proveer el nombre de usuario y contraseña','El objeto proporcionado debe de contener las propiedades nomUsuario y contrasena');
		}
		$_usuario = $this->obtenerUsuarioPorNomUsuario($usuario);
		if (property_exists($_usuario, 'error')) {
			return new Error('El nombre de usuario y contrasña son incorrectos.', $_usuario->obtenerError());
		} else {
			//Obtener el rol y el estado del usuario a partir de los IDs
			$objTmp['id']=$_usuario->rolId;
			$_rol = $this->obtenerRolPorId((object)$objTmp);
			if (property_exists($_rol, 'error')) {
				return new Error('Error al obtener el rol del usuario', $_rol->obtenerError());
			}

			//Obtener el estado del usuario
			$objTmp = null;
			$objTmp['id']=$_usuario->estadoUsuarioId;
			$_estadoUsuario = $this->obtenerEstadoUsuarioPorId((object)$objTmp);
			if (property_exists($_estadoUsuario, 'error')) {
				return new Error('Error al obtener el estado del usuario', $_estadoUsuario->obtenerError());
			}
			
			$_usuario = (array)$_usuario;
			$_usuario['rol'] = $_rol->nombre;
			$_usuario['estadoUsuario'] = $_estadoUsuario->nombre;
			$_usuario = (object)$_usuario;			
			return $_usuario;
		}
	}
	
	function registroNuevoUsuario($datos) {
		if (!property_exists($datos,'nombre') ||
			!property_exists($datos,'apellidoP') ||
			!property_exists($datos,'apellidoM') ||
			!property_exists($datos,'correo') ||
			!property_exists($datos,'nomUsuario') ||
			!property_exists($datos,'contrasena')) {
				return new Error('Es necesario proveer todos los datos en la forma de registro.','El objeto proporcionado no contiene las propiedades necesarias.');
			}
		//Checar si el nombre de usuario no existe
		$_nomUsuario['nomUsuario'] = $datos->nomUsuario;
		$_nomUsuario = (object)$_nomUsuario;
		$_usuario = $this->obtenerUsuarioPorNomUsuario($_nomUsuario);
		if (!property_exists($_usuario,'error')) {
			return new Error('El nombre de usuario ya se encuentra en uso, por favor selecciona uno disinto.', 'El nombre de usuario' . $_usuario->nomUsuario . ' ya se encuentra tomado por alguien mas.');
		} else {
			//Encontrar el id que representa a un usuario activo
			$estadoUsuarios = $this->obtenerEstadoUsuarios();
			$estadoUsuarioId = "";
			foreach ($estadoUsuarios as $llave => $valor) {
				if (strcmp($valor->nombre, 'Activo') == 0) {
					$estadoUsuarioId= $valor->id;
					break;
				}					
			}
			if (strcmp($estadoUsuarioId, "") == 0) {
				return new Error('Error al registrar nuevo usuario.', "No encontró un id que represente el estado activo para un usuario.");
			}
			
			//Encontrar el id que representa al rol Usuario
			$roles = $this->obtenerRoles();
			$rolId = "";
			foreach ($roles as $llave => $valor) {
				if (strcmp($valor->nombre, 'Usuario') == 0) {
					$rolId= $valor->id;
					break;
				}
			}
			if (strcmp($rolId, "") == 0) {
				return new Error('Error al registrar nuevo usuario.', "No encontró un id que represente un usuario activo.");
			}			
			
			$usuarios = $this->obtenerUsuarios();
			$usuarios = (array)$usuarios;
			
			$datos = (array)$datos;
			$datos['id'] = sizeof($usuarios) + 1;
			$datos['estadoUsuarioId'] = $estadoUsuarioId;
			$datos['rolId'] = $rolId;
			$datos['reputacion'] = 0.0;
			$datos = (object)$datos;
			
			$_usuario = $this->agregarUsuario($datos);
			
			if (property_exists($_usuario, 'error')) {
				return new Error('Error al registrar nuevo usuario.', $_usuario->obtenerError());
			} else {
				return $_usuario;
			}
		}			
	}
	
	function registroNuevaSubasta($subasta) {
		if (!property_exists($subasta,'articulo') ||
			!property_exists($subasta,'precio') ||
			!property_exists($subasta,'cantidad') ||
			!property_exists($subasta,'fechainicio') ||
			!property_exists($subasta,'fechafin') ||
			!property_exists($subasta,'descripcion') ||
			!property_exists($subasta,'imagenes') ||
			!property_exists($subasta,'categoria') ||
			!property_exists($subasta,'nomUsuario') ||
			!property_exists($subasta,'tipoSubasta')) {
				return new Error('Es necesario proveer todos los datos en la forma de registro de subasta.','El objeto proporcionado no contiene las propiedades necesarias.');
			}
			
			//Validar si las imagenes se subieron correctamente
			foreach ($subasta->imagenes as $llave => $valor) {
				$imagen = $_SERVER['DOCUMENT_ROOT'] . Configuracion::DIR_IMAGENES_SUBASTAS . $subasta->nomUsuario . '/tmp/' . $valor;
				if(!file_exists($imagen)) {
					return new Error('Las imagenes no se cargaron correctamente en el servidor, por favor intenta de nuevo.','La imagen ' . $valor . ' no se se encuentra en ' . $imagen);
				}
			}
			
			//Crear objeto articulo y guardarlo
			$articulos = $this->obtenerArticulos();
			$idArticulo = $this->obtenerId($articulos);
		
			 //= property_exists((object)$articulos, 'error') ? 1 : sizeof((array)$articulos) + 1;
			settype($subasta->precio, 'float');
			settype($subasta->cantidad, 'int');

			$articulo['id'] = $idArticulo;
			$articulo['nombre'] = $subasta->articulo;
			$articulo['descripcion'] = $subasta->descripcion;
			$articulo['precio'] = $subasta->precio;
			$articulo['cantidad'] = $subasta->cantidad;
			$articulo['fechaCreacion'] = date('Y-m-d');
			$articulo['fechaModificacion'] = date('Y-m-d');
			$articulo = (object)$articulo;

			$_articulo = $this->agregarArticulo($articulo);
			if (property_exists($_articulo, 'error')) {
				return new Error('Error al registrar nuevo articulo.', $_articulo->obtenerError());
			}
			
			//Mover imagenes a la nueva ruta
			foreach ($subasta->imagenes as $llave => $valor) {
				$imagen = $_SERVER['DOCUMENT_ROOT'] . Configuracion::DIR_IMAGENES_SUBASTAS . $subasta->nomUsuario . '/tmp/' . $valor;
				$nuevaRuta = $_SERVER['DOCUMENT_ROOT'] . Configuracion::DIR_IMAGENES_SUBASTAS . $subasta->nomUsuario . '/' . $idArticulo . '/';
				$nuevaImagen = $nuevaRuta. $valor;
				if (!file_exists($nuevaRuta)) {
					mkdir($nuevaRuta, 0777, true);
				}
				if(!rename($imagen, $nuevaImagen)) {
					return new Error('Erro fatal: no se encontraron las imagenes en el servidor. Por favor contacte al administrador.','Fallo al mover la imagen localizada en: ' . $imagen . ' a la ruta: ' . $nuevaImagen);
				}
			}
			
			//Crear objetos imagen y guardarlos
			$imagenes = $this->obtenerImagenes();
			$idImagen = $this->obtenerId($imagenes);
			//$idImagen = property_exists((object)$imagenes, 'error') ? 1 : sizeof((array)$imagenes) + 1;
			
			$arrImagen['id'] = $idImagen;
			$arrImagen['articuloId'] = $idArticulo;
			$arrImagen['descripcion'] = $subasta->descripcion;
			foreach ($subasta->imagenes as $llave => $valor) {
				$nuevaImagen = Configuracion::DIR_IMAGENES_SUBASTAS . $subasta->nomUsuario . '/' . $idArticulo . '/' . $valor;
				$arrImagen['nombre'] = $valor;
				$arrImagen['ruta'] = $nuevaImagen;
				$objTemp = (object)$arrImagen;
				$_objTemp = $this->agregarImagen($objTemp);
				if (property_exists($_objTemp, 'error')) {
					return new Error('Error al registrar nueva imagen.', $_objTemp->obtenerError());
				}
				$arrImagen['id'] = ++$idImagen;
			}

			//Crear objeto subasta y guardarlo
			$usuario['nomUsuario'] = $subasta->nomUsuario;
			$usuario = (object)$usuario;
			$_usuario = $this->obtenerUsuarioPorNomUsuario($usuario);
			if (property_exists($_usuario, 'error')) {
				return new Error('Error fatal: no se pudo obtener el usuario que esta creando la subasta. Por favor contacte a su administrador.', $_usuario->obtenerError());
			}
			
			$estadoSubastas = $this->obtenerEstadoSubastas();
			$estadoSubastaId = "";
			foreach ($estadoSubastas as $llave => $valor) {
				if (strcmp($valor->nombre, 'Pendiente') == 0) {
					$estadoSubastaId= $valor->id;
					break;
				}
			}
			
			$subastas = $this->obtenerSubastas();
			//$idSubasta = property_exists((object)$subastas, 'error') ? 1 : sizeof((array)$subastas) + 1;
			$idSubasta = $this->obtenerId($subastas);
			
			//Encontrar el id que representa al rol Usuario
			$usuarios = $this->obtenerUsuarios();
			//TODO improve this
			//$usuarioIndefinidoId = $this->obtenerId($usuarios);
			
			
			$nSubasta['id'] = $idSubasta;
			$nSubasta['tipoSubastaId'] = $subasta->tipoSubasta;
			$nSubasta['usuarioCreo'] = $_usuario->id;
			$nSubasta['usuarioGanador'] = 11;
			$nSubasta['usuarioAprobo'] = 11;
			$nSubasta['articuloId'] = $idArticulo;
			$nSubasta['categoriaId'] = $subasta->categoria;
			$nSubasta['estadoId'] = $estadoSubastaId;
			$nSubasta['fechaInicio'] = $subasta->fechainicio;
			$nSubasta['fechaFin'] = $subasta->fechafin;
			$nSubasta['fechaAprobacion'] = '00-00-00';
			$nSubasta['fechaCreacion'] = date('Y-m-d');
			$nSubasta['fechaModificacion'] = date('Y-m-d');
			$nSubasta = (object)$nSubasta;
			
			$_nSubasta = $this->agregarSubasta($nSubasta);
			if (property_exists($_nSubasta, 'error')) {
				return new Error('Error al registrar nueva subasta por favor intente de nuevo.', $_nSubasta->obtenerError());
			} else {
				return $_nSubasta;
			}			
	}
	
	function datosSubasta($datos) {
		if (!property_exists($datos,'id')) {
			return new Error('Error fatal: No se pudo identificar la subasta seleccionada. Por favor contacte al administrador.','El objeto proporcionado para obtener los datos de la subasta no contiene las propiedades necesarias.');
		}
		
		//Obtener subasta
		$_subasta['id'] = $datos->id;
		$_subasta = (object)$_subasta;
		$_subasta = $this->obtenerSubastaPorId($_subasta);
		if (property_exists($_subasta,'error')) {
			return new Error('Error fatal: No se pudo identificar la subasta seleccionada. Por favor contacte al administrador.', 'No se encontro una subasta con el id ' . $_subasta->id);
		}
		
		//Obtener articulo
		$_articulo['id'] = $_subasta->articuloId;
		$_articulo = (object)$_articulo;
		$_articulo = $this->obtenerArticuloPorId($_articulo);
		if (property_exists($_articulo,'error')) {
			return new Error('Error fatal: No se pudo identificar el articulo de la subasta seleccionada. Por favor contacte al administrador.', 'No se encontro el articulo con el id ' . $_articulo->id);
		}		
		
		//Obtener imagen(es)
		$_listaImagenes = array();
		$_imagenes['articuloId'] = $_articulo->id;
		$_imagenes = (object)$_imagenes;
		$_imagenes = $this->obtenerImagenPorArticuloId($_imagenes);
		if (!is_array($_imagenes) && property_exists($_imagenes,'error')) {
			return new Error('Error fatal: No se pudieron encontrar las imagenes de la subasta seleccionada. Por favor contacte al administrador.', 'No se encontraron imagenes para el articulo con el id ' . $_articulo->id);
		}
		if (!is_array($_imagenes)) {
			$_listaImagenes[] = $_imagenes;
		} else {
			$_listaImagenes = $_imagenes;
		}
		
		//Obtener tipo_subasta
		$_tipoSubasta['id'] = $_subasta->tipoSubastaId;
		$_tipoSubasta = (object)$_tipoSubasta;
		$_tipoSubasta = $this->obtenerTipoSubastaPorId($_tipoSubasta);
		if (property_exists($_tipoSubasta,'error')) {
			return new Error('Error fatal: No se pudo identificar el tipo de subasta de la subasta seleccionada. Por favor contacte al administrador.', 'No se encontro el tipo de subasta con el id ' . $_tipoSubasta->id);
		}		
		
		//Obtener estado_subasta
		$_estadoSubasta['id'] = $_subasta->estadoId;
		$_estadoSubasta = (object)$_estadoSubasta;
		$_estadoSubasta = $this->obtenerEstadoSubastaPorId($_estadoSubasta);
		if (property_exists($_estadoSubasta,'error')) {
			return new Error('Error fatal: No se pudo identificar el estado de la subasta seleccionada. Por favor contacte al administrador.', 'No se encontro el estado de la subasta con el id ' . $_estadoSubasta->id);
		}
		
		//Obtener ofertas
		$_ofertasUsuarios = array();
		$_listaOfertas = array();
		$_ofertas['subastaId'] = $_subasta->id;
		$_ofertas = (object)$_ofertas;
		$_ofertas = $this->obtenerOfertaPorSubastaId($_ofertas);

		if (!is_array($_ofertas) && !property_exists($_ofertas,'error')) {
				$_listaOfertas[] = $_ofertas;
		} else if (is_array($_ofertas)) {	
			$_listaOfertas= $_ofertas;			
		}
		
		//Obtener usuarios que ofertaron y agregarlos al objeto ofertasUsuarios
		foreach ($_listaOfertas as $llave => $oferta) {
			$_tmpUsuario = array();
			$_tmpUsuario['id'] = $oferta->usuarioId;
			$_tmpUsuario = (object)$_tmpUsuario;
			$tmp = $this->obtenerUsuarioPorId($_tmpUsuario);
			if (!property_exists($tmp,'error')) {
				$oferta = (array)$oferta;
				$oferta['nomUsuario'] = $tmp->nomUsuario;
				$oferta = (object)$oferta;
				$_ofertasUsuarios[] = $oferta;
			}
		}
		
		//Formar el objeto datosSubasta
		$datosSubasta['subasta'] = $_subasta;
		$datosSubasta['articulo'] = $_articulo;
		$datosSubasta['imagenes'] = $_listaImagenes;
		$datosSubasta['tipoSubasta'] = $_tipoSubasta;
		$datosSubasta['estadoSubasta'] = $_estadoSubasta;
		$datosSubasta['ofertasUsuarios'] = $_ofertasUsuarios;
		$datosSubasta = (object)$datosSubasta;
		return $datosSubasta;
	}
	
	function registroNuevaOferta($datos) {
		if (!property_exists($datos,'cantidad') ||
			!property_exists($datos,'nomUsuario') ||
			!property_exists($datos,'idSubasta')) {
				return new Error('Es necesario proveer todos los datos en la forma de registro de subasta.','El objeto proporcionado no contiene las propiedades necesarias.');
		}
		
		//Obtener el usuario
		$_nomUsuario['nomUsuario'] = $datos->nomUsuario;
		$_nomUsuario = (object)$_nomUsuario;
		$_usuario = $this->obtenerUsuarioPorNomUsuario($_nomUsuario);
		if (property_exists($_usuario,'error')) {
			return new Error('Error fatal: no se pudo obtener el usuario para realizar la oferta. Por favor contacte al administrador.', 'El nombre de usuario' . $_usuario->nomUsuario . ' no se encontro en la base de datos.');
		}
	
		//Obtener la subasta 
		$_idSubasta['id'] = $datos->idSubasta;
		$_idSubasta = (object)$_idSubasta;
		$_subasta = $this->obtenerSubastaPorId($_idSubasta);
		if (property_exists($_subasta,'error')) {
			return new Error('Error fatal: no se pudo obtener la subasta para realizar la oferta. Por favor contacte al administrador.', 'El id de subasta ' . $datos->idSubasta . ' no se encontro en la base de datos.');
		}
				
		//Crear objeto oferta y guardarlo
		$ofertas = $this->obtenerOfertas();
		$idOferta = $this->obtenerId($ofertas);
		
		$_oferta['id'] = $idOferta;
		$_oferta['usuarioId'] = $_usuario->id;
		$_oferta['subastaId'] = $_subasta->id;
		$_oferta['cantidad'] = $datos->cantidad;
		$_oferta['fecha'] = date('Y-m-d');		
		$_oferta = (object)$_oferta;
			
		$_nOferta = $this->agregarOferta($_oferta);
		if (property_exists($_nOferta, 'error')) {
			return new Error('Error al registrar nueva oferta por favor intente de nuevo.', $_nSubasta->obtenerError());
		} else {
			return $_nOferta;
		}
	}

	function subastasActivas() {
		//Obtener subastas
		$_subastas = $this->obtenerSubastas();
		if (!is_array($_subastas) && property_exists($_subasta,'error')) {
			return new Error('Error fatal: No se pudieron obtener las subastas. Por favor contacte al administrador.', 'Fallo al tratar de obtener todas las subastas');
		}
		
		//Obtener el id del estado de las subastas activas
		$estadoSubastas = $this->obtenerEstadoSubastas();
		$estadoSubastaId = "";
		foreach ($estadoSubastas as $llave => $valor) {
			if (strcmp($valor->nombre, 'Activa') == 0) {
				$estadoSubastaId= $valor->id;
				break;
			}
		}
		$_subastasActivas = array();
		foreach ($_subastas as $llave => $subasta) {
			if ($subasta->estadoId == $estadoSubastaId) {				
				//Traer el articulo de la subasta
				$_articulo = array();
				$_articulo['id'] = $subasta->id;
				$_articulo = $this->obtenerArticuloPorId((object)$_articulo);
				if (!property_exists($_articulo, 'error')) {					
					//Traer las imagenes del articulo
					$_listaImagenes = array();
					$_imagenes = array();
					$_imagenes['articuloId'] = $_articulo->id;
					$_imagenes = (object)$_imagenes;
					$_imagenes = $this->obtenerImagenPorArticuloId($_imagenes);										
					if (is_array($_imagenes) || !property_exists($_imagenes,'error')) {
						if(is_array($_imagenes)) {
							$_listaImagenes = $_imagenes;
						} else {
							$_listaImagenes[] = $_imagenes;
						}
						$subasta = (array)$subasta;
						$subasta['articulo'] = $_articulo;
						$subasta['imagenes'] = $_listaImagenes;
						$subasta = (object)$subasta;
						$_subastasActivas[] = $subasta;
					} 
				}			
			}
		}
		
		return $_subastasActivas;
	}
	
	function misSubastas($datos) {
		if (!property_exists($datos,'nomUsuario')) {
			return new Error('Es necesario proveer todos los datos en la forma de registro de subasta.','El objeto proporcionado no contiene las propiedades necesarias.');
		}

		//Obtener el usuario
		$_usuario['nomUsuario'] = $datos->nomUsuario;
		$_usuario = (object)$_usuario;
		$_usuario = $this->obtenerUsuarioPorNomUsuario($_usuario);
		if (property_exists($_usuario,'error')) {
			return new Error('Error fatal: no se pudo obtener el usuario. Por favor contacte al administrador.', 'El nombre de usuario' . $_usuario->nomUsuario . ' no se encontro en la base de datos.');
		}
		
		//Obtener subastas creadas por el usuario
		$_listaSubastasUsuario = array();
		$_subastas['usuarioCreo'] = $_usuario->id;
		$_subastas = (object)$_subastas;
		$_subastas = $this->obtenerSubastaPorUsuarioCreo($_subastas);
		if (!is_array($_subastas) && !property_exists($_subastas,'error')) {
			$_listaSubastasUsuario[] = $_subastas;
		} else if (is_array($_subastas)) {
			$_listaSubastasUsuario= $_subastas;
		}
		
		//Obtener ofertas para el usuario
		$_listaOfertas = array();
		$_ofertas['usuarioId'] = $_usuario->id;
		$_ofertas = (object)$_ofertas;
		$_ofertas = $this->obtenerOfertaPorUsuarioId($_ofertas);
		if (!is_array($_ofertas) && !property_exists($_ofertas,'error')) {
			$_listaOfertas[] = $_ofertas;
		} else if (is_array($_ofertas)) {
			$_listaOfertas= $_ofertas;
		}
		
		//Lista de ofertas depurada
		$_listaOfertasDepurada = array();
		$agregar = false;
		foreach ($_listaOfertas as $llave => $valor) {
			if (sizeof($_listaOfertasDepurada) > 0) {
				$agregar = true;
				foreach ($_listaOfertasDepurada as $ll =>$v )
				{
					if ($v->subastaId == $valor->subastaId) {
						$agregar = false;
						//Tomar la cantidad mas alta
						if ($v->cantidad < $valor->cantidad) {
							$v->cantidad = $valor->cantidad;
						}
					}
				}
				if ($agregar) {
					$_listaOfertasDepurada[] = $valor;
				}
			} else {
				$_listaOfertasDepurada[] = $valor;
			}
		}		
		
		//Obtener las subastas en las que ha ofertado
		$_listaSubastasOferta = array();
		foreach ($_listaOfertasDepurada as $llave => $oferta) {
			$_tmpSubasta = array();
			$_tmpSubasta['id'] = $oferta->subastaId;
			$_tmpSubasta = (object)$_tmpSubasta;
			$_tmpSubasta = $this->obtenerSubastaPorId($_tmpSubasta);
			if (!property_exists($_tmpSubasta,'error')) {				
				$_listaSubastasOferta[] = $_tmpSubasta;
			}
		}
		
		//Determinar si gano alguna subasta en la que oferto
		//Obtener las subastas finalizadas
		$_listaSubastasFinalizadas = array();
		foreach ($_listaSubastasOferta as $llave => $valor) {
			$fechaActual = date('Y-m-d h:i:s');
			$fechaFin = $valor->fechaFin;
			$segundos = $fechaFin - $fechaActual;
			$diasRestantes = $segundos/86400;			
			$valor = (array)$valor;
			$valor['finalizada'] = $diasRestantes < 0 ? true : false; 
			$valor = (object)$valor;			
			if ($diasRestantes < 0) {
				$_listaSubastasFinalizadas[] = $valor;
			}
		}
		
		//Recorrer subastas finalizadas, obtener sus ofertas y determinar si se ganaron o no
		foreach ($_listaSubastasFinalizadas as $llave => $valor) {
			$_tmpOfertas= array();
			$_tmpOfertas['subastaId'] = $valor->id;
			$_tmpOfertas = (object)$_tmpOfertas;
			$_tmpOfertas = $this->obtenerOfertaPorSubastaId($_tmpOfertas);
			if (!is_array($_tmpOfertas) && !property_exists($_tmpOfertas,'error')) {
				foreach ($_listaOfertasDepurada as $l => $v) {
					if ($_tmpOfertas->subastaId == $v->subastaId) {
						$tmpOferta = $v;
						$tmpOferta = (array)$tmpOferta;
						$tmpOferta['gano'] = $_tmpOfertas->cantidad < $v->cantidad ? true : false;
						$tmpOferta = (object)$tmpOferta;
						$_listaOfertasDepurada[$l] = $tmpOferta;
						foreach ($_listaSubastasOferta as $i => $sub) {
							if ($v->subastaId == $sub->id) {
								$tmp = (array)$sub;
								$tmp['gano'] = $_tmpOfertas->cantidad < $v->cantidad ? true : false;
								$tmp = (object)$tmp;
								$_listaSubastasOferta[$i] = $tmp;
							}
						}
					}
				}
			} elseif (is_array($_tmpOfertas)) {
				$_tmpMaxCantidad = 0;
				foreach ($_tmpOfertas as $ll => $vv) {
					$_tmpMaxCantidad = $vv->cantidad > $_tmpMaxCantidad ? $vv->cantidad : $_tmpMaxCantidad;
				}
				foreach ($_listaOfertasDepurada as $l => $v) {
					if ($valor->id == $v->subastaId) {
						$tmpOferta = $v;
						$tmpOferta = (array)$tmpOferta;
						$tmpOferta['gano'] = $_tmpMaxCantidad < $v->cantidad ? true : false;
						$tmpOferta = (object)$tmpOferta;
						$_listaOfertasDepurada[$l] = $tmpOferta;
						foreach ($_listaSubastasOferta as $i => $sub) {
							if ($v->subastaId == $sub->id) {
								$tmp = (array)$sub;
								$tmp['gano'] = $_tmpOfertas->cantidad < $v->cantidad ? true : false;
								$tmp = (object)$tmp;
								$_listaSubastasOferta[$i] = $tmp; 
							}
						}												
					}
				}
			}		
		}
		
		//Agregar Estado Usuario a las listas
		$_estadosSubasta = array();
		$_listaEstadosUsuario = $this->obtenerEstadoUsuarios();
		foreach ($_listaEstadosUsuario as $llave => $valor) {
			$_estadosSubasta[$valor->id] = $valor->nombre;
		}
		foreach ($_listaSubastasUsuario as $llave => $valor) {
			$tmp = (array)$valor;
			$tmp['estadoSubasta'] = $_estadosSubasta[$valor->estadoId];
			$tmp = (object)$tmp;
			$_listaSubastasUsuario[$llave] = $tmp;
		}
		foreach ($_listaSubastasOferta as $llave => $valor) {
			$tmp = (array)$valor;
			$tmp['estadoSubasta'] = $_estadosSubasta[$valor->estadoId];
			$tmp = (object)$tmp;
			$_listaSubastasOferta[$llave] = $tmp;
		}
		
		//Agregar Tipo Subasta a las listas
		$_tiposSubasta = array();
		$_listaTiposSubasta= $this->obtenerTipoSubastas();
		foreach ($_listaTiposSubasta as $llave => $valor) {
			$_tiposSubasta[$valor->id] = $valor->nombre;
		}
		foreach ($_listaSubastasUsuario as $llave => $valor) {
			$tmp = (array)$valor;
			$tmp['tipoSubasta'] = $_tiposSubasta[$valor->tipoSubastaId];
			$tmp = (object)$tmp;
			$_listaSubastasUsuario[$llave] = $tmp;
		}
		foreach ($_listaSubastasOferta as $llave => $valor) {
			$tmp = (array)$valor;
			$tmp['tipoSubasta'] = $_tiposSubasta[$valor->tipoSubastaId];
			$tmp = (object)$tmp;
			$_listaSubastasOferta[$llave] = $tmp;
		}
		
		//Agregar articulo a las listas
		foreach ($_listaSubastasUsuario as $llave => $valor) {
			//Obtener articulo
			$_articulo = array();
			$_articulo['id'] = $valor->articuloId;
			$_articulo = (object)$_articulo;
			$_articulo = $this->obtenerArticuloPorId($_articulo);
			if (!property_exists($_articulo,'error')) {
				$tmp = (array)$valor;
				$tmp['articulo'] = $_articulo;
				$tmp = (object)$tmp;
				$_listaSubastasUsuario[$llave] = $tmp;								
			}
		}
		foreach ($_listaSubastasOferta as $llave => $valor) {
			//Obtener articulo
			$_articulo = array();
			$_articulo['id'] = $valor->articuloId;
			$_articulo = (object)$_articulo;
			$_articulo = $this->obtenerArticuloPorId($_articulo);
			if (!property_exists($_articulo,'error')) {
				$tmp = (array)$valor;
				$tmp['articulo'] = $_articulo;
				$tmp = (object)$tmp;
				$_listaSubastasOferta[$llave] = $tmp;								
			}
		}		
			
		$_subastasUsuario = array();
		$_subastasUsuario['listaSubastasUsuario'] = $_listaSubastasUsuario;
		$_subastasUsuario['listaSubastasOferta'] = $_listaSubastasOferta;
		$_subastasUsuario = (object)$_subastasUsuario;
		return $_subastasUsuario;
	}
	
	private function obtenerId($arr) {
		if(is_array($arr)) {
			$id = 0;
			if (property_exists((object)$arr, 'error')) {
				$id = 1;
			} else {
				foreach ((array)$arr as $llave => $valor) {
					$tmp = (object)$valor;
					if ($tmp->id > $id) {
						$id = $tmp->id;
					}
				}
				$id++;
			}
		} else if (property_exists((object)$arr, 'error')) {
			$id = 1;
		} else {
			$id = 2;
		}

		return $id;		
	}
	
	/**
	 * Operaciones comunes
	 */
	/**
	Obtener todos los datos
	**/
	function obtenerArticulos() {
		return $this->_recuperacionDeDatos->leerArticulos();
	}
	function obtenerCalificaciones() {
		return $this->_recuperacionDeDatos->leerCalificaciones();
	}
	function obtenerCategorias() {
		return $this->_recuperacionDeDatos->leerCategorias();
	}
	function obtenerDirecciones() {
		return $this->_recuperacionDeDatos->leerDirecciones();
	}
	function obtenerEnvios() {
		return $this->_recuperacionDeDatos->leerEnvios();
	}
	function obtenerEstadoSubastas() {
		return $this->_recuperacionDeDatos->leerEstadoSubastas();
	}
	function obtenerEstadoUsuarios() {
		return $this->_recuperacionDeDatos->leerEstadoUsuarios();
	}
	function obtenerImagenes() {
		return $this->_recuperacionDeDatos->leerImagenes();
	}
	function obtenerMensajes() {
		return $this->_recuperacionDeDatos->leerMensajes();
	}
	function obtenerOfertas() {
		return $this->_recuperacionDeDatos->leerOfertas();
	}
	function obtenerPagos() {
		return $this->_recuperacionDeDatos->leerPagos();
	}
	function obtenerRoles() {
		return $this->_recuperacionDeDatos->leerRoles();
	}
	function obtenerSubastas() {
		return $this->_recuperacionDeDatos->leerSubastas();
	}
	function obtenerTarjetaCreditos() {
		return $this->_recuperacionDeDatos->leerTarjetaCreditos();
	}
	function obtenerTarjetaCreditoUsuarios() {
		return $this->_recuperacionDeDatos->leerTarjetaCreditoUsuarios();
	}
	function obtenerTipoEnvios() {
		return $this->_recuperacionDeDatos->leerTipoEnvios();
	}
	function obtenerTipoPagos() {
		return $this->_recuperacionDeDatos->leerTipoPagos();
	}
	function obtenerTipoSubastas() {
		return $this->_recuperacionDeDatos->leerTipoSubastas();
	}
	function obtenerUsuarios() {
		return $this->_recuperacionDeDatos->leerUsuarios();
	}
	function obtenerUsuarioDirecciones() {
		return $this->_recuperacionDeDatos->leerUsuarioDirecciones();
	}


	/**
	Obtener todos los datos por indices
	**/
	function obtenerArticuloPorId($id) {
		return $this->_recuperacionDeDatos->leerArticuloPorId($id);
	}
	function obtenerCalificacionPorId($id) {
		return $this->_recuperacionDeDatos->leerCalificacionPorId($id);
	}
	function obtenerCalificacionPorSubastaId($subastaId) {
		return $this->_recuperacionDeDatos->leerCalificacionPorSubastaId($subastaId);
	}
	function obtenerCalificacionPorUsuarioCalificaId($usuarioCalificaId) {
		return $this->_recuperacionDeDatos->leerCalificacionPorUsuarioCalificaId($usuarioCalificaId);
	}
	function obtenerCalificacionPorUsuarioCalificadoId($usuarioCalificadoId) {
		return $this->_recuperacionDeDatos->leerCalificacionPorUsuarioCalificadoId($usuarioCalificadoId);
	}
	function obtenerCategoriaPorId($id) {
		return $this->_recuperacionDeDatos->leerCategoriaPorId($id);
	}
	function obtenerDireccionPorId($id) {
		return $this->_recuperacionDeDatos->leerDireccionPorId($id);
	}
	function obtenerEnvioPorId($id) {
		return $this->_recuperacionDeDatos->leerEnvioPorId($id);
	}
	function obtenerEnvioPorDireccionId($direccionId) {
		return $this->_recuperacionDeDatos->leerEnvioPorDireccionId($direccionId);
	}
	function obtenerEnvioPorSubastaId($subastaId) {
		return $this->_recuperacionDeDatos->leerEnvioPorSubastaId($subastaId);
	}
	function obtenerEnvioPorTipoEnvioId($tipoEnvioId) {
		return $this->_recuperacionDeDatos->leerEnvioPorTipoEnvioId($tipoEnvioId);
	}
	function obtenerEstadoSubastaPorId($id) {
		return $this->_recuperacionDeDatos->leerEstadoSubastaPorId($id);
	}
	function obtenerEstadoUsuarioPorId($id) {
		return $this->_recuperacionDeDatos->leerEstadoUsuarioPorId($id);
	}
	function obtenerImagenPorId($id) {
		return $this->_recuperacionDeDatos->leerImagenPorId($id);
	}
	function obtenerImagenPorArticuloId($articuloId) {
		return $this->_recuperacionDeDatos->leerImagenPorArticuloId($articuloId);
	}
	function obtenerMensajePorId($id) {
		return $this->_recuperacionDeDatos->leerMensajePorId($id);
	}
	function obtenerMensajePorSubastaId($subastaId) {
		return $this->_recuperacionDeDatos->leerMensajePorSubastaId($subastaId);
	}
	function obtenerMensajePorUsuarioId($usuarioId) {
		return $this->_recuperacionDeDatos->leerMensajePorUsuarioId($usuarioId);
	}
	function obtenerOfertaPorId($id) {
		return $this->_recuperacionDeDatos->leerOfertaPorId($id);
	}
	function obtenerOfertaPorSubastaId($subastaId) {
		return $this->_recuperacionDeDatos->leerOfertaPorSubastaId($subastaId);
	}
	function obtenerOfertaPorUsuarioId($usuarioId) {
		return $this->_recuperacionDeDatos->leerOfertaPorUsuarioId($usuarioId);
	}
	function obtenerPagoPorId($id) {
		return $this->_recuperacionDeDatos->leerPagoPorId($id);
	}
	function obtenerPagoPorSubastaId($subastaId) {
		return $this->_recuperacionDeDatos->leerPagoPorSubastaId($subastaId);
	}
	function obtenerPagoPorTipoPagoId($tipoPagoId) {
		return $this->_recuperacionDeDatos->leerPagoPorTipoPagoId($tipoPagoId);
	}
	function obtenerRolPorId($id) {
		return $this->_recuperacionDeDatos->leerRolPorId($id);
	}
	function obtenerSubastaPorId($id) {
		return $this->_recuperacionDeDatos->leerSubastaPorId($id);
	}
	function obtenerSubastaPorArticuloId($articuloId) {
		return $this->_recuperacionDeDatos->leerSubastaPorArticuloId($articuloId);
	}
	function obtenerSubastaPorCategoriaId($categoriaId) {
		return $this->_recuperacionDeDatos->leerSubastaPorCategoriaId($categoriaId);
	}
	function obtenerSubastaPorEstadoId($estadoId) {
		return $this->_recuperacionDeDatos->leerSubastaPorEstadoId($estadoId);
	}
	function obtenerSubastaPorFechaInicio($fechaInicio) {
		return $this->_recuperacionDeDatos->leerSubastaPorFechaInicio($fechaInicio);
	}
	function obtenerSubastaPorTipoSubastaId($tipoSubastaId) {
		return $this->_recuperacionDeDatos->leerSubastaPorTipoSubastaId($tipoSubastaId);
	}
	function obtenerSubastaPorUsuarioAprobo($usuarioAprobo) {
		return $this->_recuperacionDeDatos->leerSubastaPorUsuarioAprobo($usuarioAprobo);
	}
	function obtenerSubastaPorUsuarioCreo($usuarioCreo) {
		return $this->_recuperacionDeDatos->leerSubastaPorUsuarioCreo($usuarioCreo);
	}
	function obtenerSubastaPorUsuarioGanador($usuarioGanador) {
		return $this->_recuperacionDeDatos->leerSubastaPorUsuarioGanador($usuarioGanador);
	}
	function obtenerTarjetaCreditoPorId($id) {
		return $this->_recuperacionDeDatos->leerTarjetaCreditoPorId($id);
	}
	function obtenerTarjetaCreditoUsuarioPorTarjetaCreditoId($tarjetaCreditoId) {
		return $this->_recuperacionDeDatos->leerTarjetaCreditoUsuarioPorTarjetaCreditoId($tarjetaCreditoId);
	}
	function obtenerTarjetaCreditoUsuarioPorUsuarioId($usuarioId) {
		return $this->_recuperacionDeDatos->leerTarjetaCreditoUsuarioPorUsuarioId($usuarioId);
	}
	function obtenerTipoEnvioPorId($id) {
		return $this->_recuperacionDeDatos->leerTipoEnvioPorId($id);
	}
	function obtenerTipoPagoPorId($id) {
		return $this->_recuperacionDeDatos->leerTipoPagoPorId($id);
	}
	function obtenerTipoSubastaPorId($id) {
		return $this->_recuperacionDeDatos->leerTipoSubastaPorId($id);
	}
	function obtenerUsuarioPorId($id) {
		return $this->_recuperacionDeDatos->leerUsuarioPorId($id);
	}
	function obtenerUsuarioPorNomUsuario($nomUsuario) {
		return $this->_recuperacionDeDatos->leerUsuarioPorNomUsuario($nomUsuario);
	}
	function obtenerUsuarioPorEstadoUsuarioId($estadoUsuarioId) {
		return $this->_recuperacionDeDatos->leerUsuarioPorEstadoUsuarioId($estadoUsuarioId);
	}
	function obtenerUsuarioPorRolId($rolId) {
		return $this->_recuperacionDeDatos->leerUsuarioPorRolId($rolId);
	}
	function obtenerUsuarioDireccionPorDireccionId($direccionId) {
		return $this->_recuperacionDeDatos->leerUsuarioDireccionPorDireccionId($direccionId);
	}
	function obtenerUsuarioDireccionPorUsuarioId($usuarioId) {
		return $this->_recuperacionDeDatos->leerUsuarioDireccionPorUsuarioId($usuarioId);
	}


	/**
	Agregar datos
	**/
	function agregarArticulo($articulo) {
		return $this->_mantenimientoDeDatos->escribirArticulo($articulo);
	}
	function agregarCalificacion($calificacion) {
		return $this->_mantenimientoDeDatos->escribirCalificacion($calificacion);
	}
	function agregarCategoria($categoria) {
		return $this->_mantenimientoDeDatos->escribirCategoria($categoria);
	}
	function agregarDireccion($direccion) {
		return $this->_mantenimientoDeDatos->escribirDireccion($direccion);
	}
	function agregarEnvio($envio) {
		return $this->_mantenimientoDeDatos->escribirEnvio($envio);
	}
	function agregarEstadoSubasta($estadoSubasta) {
		return $this->_mantenimientoDeDatos->escribirEstadoSubasta($estadoSubasta);
	}
	function agregarEstadoUsuario($estadoUsuario) {
		return $this->_mantenimientoDeDatos->escribirEstadoUsuario($estadoUsuario);
	}
	function agregarImagen($imagen) {
		return $this->_mantenimientoDeDatos->escribirImagen($imagen);
	}
	function agregarMensaje($mensaje) {
		return $this->_mantenimientoDeDatos->escribirMensaje($mensaje);
	}
	function agregarOferta($oferta) {
		return $this->_mantenimientoDeDatos->escribirOferta($oferta);
	}
	function agregarPago($pago) {
		return $this->_mantenimientoDeDatos->escribirPago($pago);
	}
	function agregarRol($rol) {
		return $this->_mantenimientoDeDatos->escribirRol($rol);
	}
	function agregarSubasta($subasta) {
		return $this->_mantenimientoDeDatos->escribirSubasta($subasta);
	}
	function agregarTarjetaCredito($tarjetaCredito) {
		return $this->_mantenimientoDeDatos->escribirTarjetaCredito($tarjetaCredito);
	}
	function agregarTarjetaCreditoUsuario($tarjetaCreditoUsuario) {
		return $this->_mantenimientoDeDatos->escribirTarjetaCreditoUsuario($tarjetaCreditoUsuario);
	}
	function agregarTipoEnvio($tipoEnvio) {
		return $this->_mantenimientoDeDatos->escribirTipoEnvio($tipoEnvio);
	}
	function agregarTipoPago($tipoPago) {
		return $this->_mantenimientoDeDatos->escribirTipoPago($tipoPago);
	}
	function agregarTipoSubasta($tipoSubasta) {
		return $this->_mantenimientoDeDatos->escribirTipoSubasta($tipoSubasta);
	}
	function agregarUsuario($usuario) {
		return $this->_mantenimientoDeDatos->escribirUsuario($usuario);
	}
	function agregarUsuarioDireccion($usuarioDireccion) {
		return $this->_mantenimientoDeDatos->escribirUsuarioDireccion($usuarioDireccion);
	}


	/**
	Editar datos
	**/
	function editarArticulo($articulo) {
		return $this->_mantenimientoDeDatos->modificarArticulo($articulo);
	}
	function editarCalificacion($calificacion) {
		return $this->_mantenimientoDeDatos->modificarCalificacion($calificacion);
	}
	function editarCategoria($categoria) {
		return $this->_mantenimientoDeDatos->modificarCategoria($categoria);
	}
	function editarDireccion($direccion) {
		return $this->_mantenimientoDeDatos->modificarDireccion($direccion);
	}
	function editarEnvio($envio) {
		return $this->_mantenimientoDeDatos->modificarEnvio($envio);
	}
	function editarEstadoSubasta($estadoSubasta) {
		return $this->_mantenimientoDeDatos->modificarEstadoSubasta($estadoSubasta);
	}
	function editarEstadoUsuario($estadoUsuario) {
		return $this->_mantenimientoDeDatos->modificarEstadoUsuario($estadoUsuario);
	}
	function editarImagen($imagen) {
		return $this->_mantenimientoDeDatos->modificarImagen($imagen);
	}
	function editarMensaje($mensaje) {
		return $this->_mantenimientoDeDatos->modificarMensaje($mensaje);
	}
	function editarOferta($oferta) {
		return $this->_mantenimientoDeDatos->modificarOferta($oferta);
	}
	function editarPago($pago) {
		return $this->_mantenimientoDeDatos->modificarPago($pago);
	}
	function editarRol($rol) {
		return $this->_mantenimientoDeDatos->modificarRol($rol);
	}
	function editarSubasta($subasta) {
		return $this->_mantenimientoDeDatos->modificarSubasta($subasta);
	}
	function editarTarjetaCredito($tarjetaCredito) {
		return $this->_mantenimientoDeDatos->modificarTarjetaCredito($tarjetaCredito);
	}
	function editarTarjetaCreditoUsuario($tarjetaCreditoUsuario) {
		return $this->_mantenimientoDeDatos->modificarTarjetaCreditoUsuario($tarjetaCreditoUsuario);
	}
	function editarTipoEnvio($tipoEnvio) {
		return $this->_mantenimientoDeDatos->modificarTipoEnvio($tipoEnvio);
	}
	function editarTipoPago($tipoPago) {
		return $this->_mantenimientoDeDatos->modificarTipoPago($tipoPago);
	}
	function editarTipoSubasta($tipoSubasta) {
		return $this->_mantenimientoDeDatos->modificarTipoSubasta($tipoSubasta);
	}
	function editarUsuario($usuario) {
		return $this->_mantenimientoDeDatos->modificarUsuario($usuario);
	}
	function editarUsuarioDireccion($usuarioDireccion) {
		return $this->_mantenimientoDeDatos->modificarUsuarioDireccion($usuarioDireccion);
	}


	/**
	Remover datos
	**/
	function removerArticulo($articulo) {
		return $this->_mantenimientoDeDatos->eliminarArticulo($articulo);
	}
	function removerCalificacion($calificacion) {
		return $this->_mantenimientoDeDatos->eliminarCalificacion($calificacion);
	}
	function removerCategoria($categoria) {
		return $this->_mantenimientoDeDatos->eliminarCategoria($categoria);
	}
	function removerDireccion($direccion) {
		return $this->_mantenimientoDeDatos->eliminarDireccion($direccion);
	}
	function removerEnvio($envio) {
		return $this->_mantenimientoDeDatos->eliminarEnvio($envio);
	}
	function removerEstadoSubasta($estadoSubasta) {
		return $this->_mantenimientoDeDatos->eliminarEstadoSubasta($estadoSubasta);
	}
	function removerEstadoUsuario($estadoUsuario) {
		return $this->_mantenimientoDeDatos->eliminarEstadoUsuario($estadoUsuario);
	}
	function removerImagen($imagen) {
		return $this->_mantenimientoDeDatos->eliminarImagen($imagen);
	}
	function removerMensaje($mensaje) {
		return $this->_mantenimientoDeDatos->eliminarMensaje($mensaje);
	}
	function removerOferta($oferta) {
		return $this->_mantenimientoDeDatos->eliminarOferta($oferta);
	}
	function removerPago($pago) {
		return $this->_mantenimientoDeDatos->eliminarPago($pago);
	}
	function removerRol($rol) {
		return $this->_mantenimientoDeDatos->eliminarRol($rol);
	}
	function removerSubasta($subasta) {
		return $this->_mantenimientoDeDatos->eliminarSubasta($subasta);
	}
	function removerTarjetaCredito($tarjetaCredito) {
		return $this->_mantenimientoDeDatos->eliminarTarjetaCredito($tarjetaCredito);
	}
	function removerTarjetaCreditoUsuario($tarjetaCreditoUsuario) {
		return $this->_mantenimientoDeDatos->eliminarTarjetaCreditoUsuario($tarjetaCreditoUsuario);
	}
	function removerTipoEnvio($tipoEnvio) {
		return $this->_mantenimientoDeDatos->eliminarTipoEnvio($tipoEnvio);
	}
	function removerTipoPago($tipoPago) {
		return $this->_mantenimientoDeDatos->eliminarTipoPago($tipoPago);
	}
	function removerTipoSubasta($tipoSubasta) {
		return $this->_mantenimientoDeDatos->eliminarTipoSubasta($tipoSubasta);
	}
	function removerUsuario($usuario) {
		return $this->_mantenimientoDeDatos->eliminarUsuario($usuario);
	}
	function removerUsuarioDireccion($usuarioDireccion) {
		return $this->_mantenimientoDeDatos->eliminarUsuarioDireccion($usuarioDireccion);
	}

}
?>
