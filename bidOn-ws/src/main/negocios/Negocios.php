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
			$idUsuario = $this->obtenerId($usuarios);

			$datos = (array)$datos;
			$datos['id'] = $idUsuario;
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
						$subasta['estadoSubasta'] = 'Activa';
						$subasta = (object)$subasta;
						$_subastasActivas[] = $subasta;
					} 
				}			
			}
		}
		
		return $_subastasActivas;
	}
	
	function subastasPendientes() {
		//Obtener subastas
		$_subastas = $this->obtenerSubastas();
		if (!is_array($_subastas) && property_exists($_subasta,'error')) {
			return new Error('Error fatal: No se pudieron obtener las subastas. Por favor contacte al administrador.', 'Fallo al tratar de obtener todas las subastas');
		}
	
		//Obtener el id del estado de las subastas activas
		$estadoSubastas = $this->obtenerEstadoSubastas();
		$estadoSubastaId = "";
		foreach ($estadoSubastas as $llave => $valor) {
			if (strcmp($valor->nombre, 'Pendiente') == 0) {
				$estadoSubastaId= $valor->id;
				break;
			}
		}
		$_subastasPendientes = array();
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
						$subasta['estadoSubasta'] = 'Pendiente';
						$subasta = (object)$subasta;
						$_subastasPendientes[] = $subasta;
					}
				}
			}
		}
	
		return $_subastasPendientes;
	}
	
	function subastasInactivas() {
		//Obtener subastas
		$_subastas = $this->obtenerSubastas();
		if (!is_array($_subastas) && property_exists($_subasta,'error')) {
			return new Error('Error fatal: No se pudieron obtener las subastas. Por favor contacte al administrador.', 'Fallo al tratar de obtener todas las subastas');
		}
	
		//Obtener el id del estado de las subastas activas
		$estadoSubastas = $this->obtenerEstadoSubastas();
		$estadoSubastaId = "";
		foreach ($estadoSubastas as $llave => $valor) {
			if (strcmp($valor->nombre, 'Inactiva') == 0) {
				$estadoSubastaId= $valor->id;
				break;
			}
		}
		$_subastasInactivas = array();
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
						$subasta['estadoSubasta'] = 'Inactiva';
						$subasta = (object)$subasta;
						$_subastasInactivas[] = $subasta;
					}
				}
			}
		}
	
		return $_subastasInactivas;
	}

	/**
	 * Obtener usuarios con su estado y rol
	 */
	function obenerUsuariosCompletos() {
		//Obtener todos los usuarios
		$_listaUsuarios = array();
		$_usuarios = $this->obtenerUsuarios();
		if (!is_array($_usuarios) && !property_exists($_usuarios, 'error')) {
			$_listaUsuarios[] = $_usuarios;
		} elseif (is_array($_usuarios)) {
			$_listaUsuarios = $_usuarios;
		} else {
			return new Error('Error fatal: No se pudieron obtener los usuarios. Por favor contacte al administrador.', $_usuarios->obtenerError());
		}
		
		//Obtener Estados de usuario
		$_listaEstadosUsuario = array();			
		$_estadoUsuarios = $this->obtenerEstadoUsuarios();
		foreach ($_estadoUsuarios as $llave => $valor) {
			$_listaEstadosUsuario[$valor->id] = $valor->nombre;
		}
		
		//Obtener Roles de usuario
		$_listaRolesUsuario = array();
		$roles = $this->obtenerRoles();
		foreach ($roles as $llave => $valor) {
			$_listaRolesUsuario[$valor->id] = $valor->nombre;
		}
		
		//Agregar a cada usuario su nombre de Estado y su nombre de Rol 
		foreach ($_listaUsuarios as $llave => $valor) {
			$valor = (array)$valor;
			$valor['estadoUsuario'] = $_listaEstadosUsuario[$valor['estadoUsuarioId']];
			$valor['rol'] = $_listaRolesUsuario[$valor['rolId']];
			$valor = (object)$valor;
			$_listaUsuarios[$llave] = $valor;
		}
		return $_listaUsuarios;
	}
	
	function desactivarSubasta($datos) {
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
		
		//Actualizar estado subasta
		$estadoSubastas = $this->obtenerEstadoSubastas();
		$estadoSubastaId = "";
		foreach ($estadoSubastas as $llave => $valor) {
			if (strcmp($valor->nombre, 'Inactiva') == 0) {
				$estadoSubastaId= $valor->id;
				break;
			}
		}
		if (strcmp($estadoSubastaId, "") == 0) {
			return new Error('Error fatal: no se pudo obtener el estado de la subasta inactiva.Por favor contacte al administrador.', 'No se pudo encontrar un id para el estado de subasta Inactiva');
		}
		
		$_subasta = (array)$_subasta;
		$_subasta['estadoId'] = $estadoSubastaId;
		$_subasta = (object)$_subasta;

		$resultado = $this->editarSubasta($_subasta);
		if (property_exists($resultado, 'error')) {
			return new Error('Error al intentar desactivar la subasta. Por favor contacte al administrador.', $resultado->obtenerError());
		}
		return $resultado;
	}
	
	function activarSubasta($datos) {
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
	
		//Actualizar estado subasta
		$estadoSubastas = $this->obtenerEstadoSubastas();
		$estadoSubastaId = "";
		foreach ($estadoSubastas as $llave => $valor) {
			if (strcmp($valor->nombre, 'Activa') == 0) {
				$estadoSubastaId= $valor->id;
				break;
			}
		}
		if (strcmp($estadoSubastaId, "") == 0) {
			return new Error('Error fatal: no se pudo obtener el estado de la subasta inactiva.Por favor contacte al administrador.', 'No se pudo encontrar un id para el estado de subasta Inactiva');
		}
	
		$_subasta = (array)$_subasta;
		$_subasta['estadoId'] = $estadoSubastaId;
		$_subasta = (object)$_subasta;
	
		$resultado = $this->editarSubasta($_subasta);
		if (property_exists($resultado, 'error')) {
			return new Error('Error al intentar activar la subasta. Por favor contacte al administrador.', $resultado->obtenerError());
		}
		return $resultado;
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
		foreach ($_listaSubastasOferta as $i => $sub) {
				$tmp = (array)$sub;
				$tmp['gano'] = $sub->usuarioGanador == $_usuario->id ? true : false;
				$tmp = (object)$tmp;
				$_listaSubastasOferta[$i] = $tmp;
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
	
	/**
	 * Busca ganadores y actualiza las subastas con su id
	 * @return Error|multitype:StdClass
	 */
	function actualizarGanadores() {
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
		//Obtener subastas activas	
		$_subastasActivas = array();
		foreach ($_subastas as $llave => $subasta) {
			if ($subasta->estadoId == $estadoSubastaId) {
				$_subastasActivas[] = $subasta;									
			}
		}
		
		//Obtener las subastas finalizadas
		$_listaSubastasFinalizadas = array();
		foreach ($_subastasActivas as $llave => $valor) {
			$fechaActual = strtotime(date('Y-m-d h:i:s'));
			$fechaFin = strtotime(date($valor->fechaFin));
			$segundos = $fechaFin - $fechaActual;
			$diasRestantes = $segundos/86400;
			if ($diasRestantes < 0) {
				$_listaSubastasFinalizadas[] = $valor;
			}
		}
		//Obtener ganador para cada subasta
		$_listaGanadores = array();
		foreach ($_listaSubastasFinalizadas as $llave => $valor) {
			$_tmpofertas = array();
			$_tmpofertas['subastaId'] = $valor->id;
			$_tmpofertas = (object)$_tmpofertas;
			$_tmpofertas = $this->obtenerOfertaPorSubastaId($_tmpofertas);
			if (!is_array($_tmpofertas) && !property_exists($_articulo,'error')) {
				$_listaGanadores[$valor->id] = $_tmpofertas->usuarioId; 
			} elseif (is_array($_tmpofertas)) {
				$_tmpMaxCant = 0;
				$_tmpGanadorId = "";
				foreach ($_tmpofertas as $l => $v) {
					if ($v->cantidad > $_tmpMaxCant) {
						$_tmpMaxCant = $v->cantidad;
						$_tmpGanadorId = $v->usuarioId;
					}
				}
				if (strcmp($_tmpGanadorId, "") != 0) {
					$_listaGanadores[$valor->id] = $_tmpGanadorId;
					$_tmpGanadorId = "";
				}				
			} else {
				return new Error("Error Fatal: No se pudo obtener un ganador de subasta",$_tmpGanadorId->error);
			}
		}
		
		//Obtener el id del estado de las subastas inactivas
		$estadoSubastas = $this->obtenerEstadoSubastas();
		$estadoSubastaId = "";
		foreach ($estadoSubastas as $llave => $valor) {
			if (strcmp($valor->nombre, 'Inactiva') == 0) {
				$estadoSubastaId= $valor->id;
				break;
			}
		}		
		if (strcmp($estadoSubastaId, "") == 0) {
			return new Error("Error Fatal: No se pudo obtener el id para las subastas inactivas","No se pudo obtener el id para el estado de subasta: Inactiva");
		}
				
		//Actualizar las subastas con su ganador y ponerlas como inactivas
		$_listaSubastasActualizadas = array();
		foreach ($_listaGanadores as $llave => $valor) {
			$_tmpsubasta = array();
			$_tmpsubasta['id'] = $llave;
			$_tmpsubasta = (object)$_tmpsubasta;
			$_tmpsubasta = $this->obtenerSubastaPorId($_tmpsubasta);
			if (!property_exists($_tmpsubasta,'error')) {
				$_tmpsubasta = (array)$_tmpsubasta;
				$_tmpsubasta['usuarioGanador'] = $valor;
				$_tmpsubasta['estadoId'] = $estadoSubastaId;
				$_tmpsubasta['fechaModificacion'] = date('Y-m-d h:i:s');
				$_tmpsubasta = (object)$_tmpsubasta;
				$_tmpsubasta = $this->editarSubasta($_tmpsubasta);
				if (!property_exists($_tmpsubasta,'error')) {
					$_listaSubastasActualizadas = $_tmpsubasta;
				}				
			}
			
		}	
		return $_listaSubastasActualizadas;
	}
	
	//TODO mejorar este metodo
	function realizarPago($datos) {
		if (!property_exists($datos,'nombre') ||
			!property_exists($datos,'numeroTarjeta') ||
			!property_exists($datos,'cvv') ||
			!property_exists($datos,'calle') ||
			!property_exists($datos,'numeroExterior') ||
			!property_exists($datos,'numeroInterior') ||
			!property_exists($datos,'colonia') ||
			!property_exists($datos,'codigoPostal') ||
			!property_exists($datos,'ciudad') ||
			!property_exists($datos,'estado') ||
			!property_exists($datos,'pais') ||
			!property_exists($datos, 'fechaexpiracion') ||
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
		//Registrar direccion
		//Crear objeto oferta y guardarlo
		$_idDireccion = $this->obtenerDirecciones();
		$_idDireccion = $this->obtenerId($_idDireccion);
		$_direccion = array();
		$_direccion['id'] = $_idDireccion;
		$_direccion['usuarioId'] = $_usuario->id;
		$_direccion['calle'] = $datos->calle;
		$_direccion['numeroExt'] = $datos->numeroExterior;
		$_direccion['numeroInt'] = $datos->numeroInterior;
		$_direccion['colonia'] = $datos->colonia;
		$_direccion['codigoPostal'] = $datos->codigoPostal;
		$_direccion['ciudad'] = $datos->ciudad;
		$_direccion['estado'] = $datos->estado;
		$_direccion['pais'] = $datos->pais;
		$_direccion = (object)$_direccion;
		$_direccion = $this->agregarDireccion($_direccion);
		if (property_exists($_direccion,'error')) {
			return new Error('Error al guardar los datos de la dirección, por favor asegurese de haberlos introducido de manera correcta.', $_direccion->error);
		}
		
		$_direccionId= $this->obtenerDirecciones();
		$_direccionId = $this->obtenerId($_direccionId);
		$_direccionId = $_direccionId > 1 ? --$_direccionId : $_direccionId;
		
		//Registrar usuario-direccion
		//TODO corregir la incertidumbre de traer el id de la direccion incorrecto
		$_usuarioDireccion =  array();
		$_usuarioDireccion['usuarioId'] = $_usuario->id;
		$_usuarioDireccion['direccionId'] = $_direccionId;
		$_usuarioDireccion = (object)$_usuarioDireccion;
		$_usuarioDireccion = $this->agregarUsuarioDireccion($_usuarioDireccion);
		if (property_exists($_usuarioDireccion,'error')) {
			return new Error('Error fatal: error al intentar guardar el objeto usuario-direccion. Por favor contacte al administrador.', $_usuarioDireccion->error);
		}
		
		//Registrar tarjeta de credito
		$_idTarjetaCredito = $this->obtenerTarjetaCreditos();
		$_idTarjetaCredito = $this->obtenerId($_idTarjetaCredito);
		$_idTarjetaCredito = $_idTarjetaCredito > 1 ? --$_idTarjetaCredito : $_idTarjetaCredito;

		$_tarjetaCredito = array();
		$_tarjetaCredito['id'] = $_idTarjetaCredito;
		$_tarjetaCredito['nombre'] = $datos->nombre;
		$_tarjetaCredito['descripcion'] = "";
		$_tarjetaCredito['numeracion'] = $datos->numeroTarjeta;
		$_tarjetaCredito['fechaExpiracion'] = $datos->fechaexpiracion;
		$_tarjetaCredito['cvv'] = $datos->cvv;
		$_tarjetaCredito['calle'] = $datos->calle;
		$_tarjetaCredito['numero'] = $datos->numeroExterior;
		$_tarjetaCredito['colonia'] = $datos->colonia;
		$_tarjetaCredito['codigoPostal'] = $datos->codigoPostal;
		$_tarjetaCredito['ciudad'] = $datos->ciudad;
		$_tarjetaCredito['estado'] = $datos->estado;
		$_tarjetaCredito['pais'] = $datos->pais;
		$_tarjetaCredito = (object)$_tarjetaCredito;		
		$_tarjetaCredito = $this->agregarTarjetaCredito($_tarjetaCredito);
		if (property_exists($_tarjetaCredito,'error')) {
			return new Error('Error fatal: error al intentar guardar los datos de la tarjeta de crédito, por favor asegurese de haberlos introducido de manera correcta.', $_tarjetaCredito->error);
		}

		$_tarjetaCreditoUsuarioId= $this->obtenerTarjetaCreditoUsuarios();
		$_tarjetaCreditoUsuarioId = $this->obtenerId($_tarjetaCreditoUsuarioId);
		
		//Registrar tarjetaCredito-usuario
		$_tarjetaCreditoUsuario =  array();
		$_tarjetaCreditoUsuario['usuarioId'] = $_usuario->id;
		$_tarjetaCreditoUsuario['tarjetaCreditoId'] = $_tarjetaCreditoUsuarioId;
		$_tarjetaCreditoUsuario = (object)$_tarjetaCreditoUsuario;
		$_tarjetaCreditoUsuario = $this->agregarTarjetaCreditoUsuario($_tarjetaCreditoUsuario);
		if (property_exists($_tarjetaCreditoUsuario,'error')) {
			return new Error('Error fatal: error al intentar guardar la relación TarjetaCredito-Usuario. Por favor contacte al administrador.', $_tarjetaCreditoUsuario->error);
		}
		
		//Obtener el id del tipo de pago de tarjeta de credito
		$_tipoPagoId = "";
		$_tipoPago = $this->obtenerTipoPagos();
		if (!is_array($_tipoPago) && !property_exists($_tipoPago,'error')) {
			if (strcmp($_tipoPago->nombre, 'Tarjeta de credito') == 0) {
				$_tipoPagoId = $_tipoPago->id;
			}
		} elseif (is_array($_tipoPago)) {
			foreach ($_tipoPago as $llave => $valor) {
				if (strcmp($valor->nombre, 'Tarjeta-de-credito') == 0) {
					$_tipoPagoId = $valor->id;
					break;
				}
			}			
		} else {
			return new Error("Error Fatal: no se pudieron encontrar los tipos de pago disponibles.","Error al intentar encontrar el id para el tipo de pago: Tarjeta de credito");
		}
		
		//Regisrar pago
		$_idPago = $this->obtenerPagos();
		$_idPago = $this->obtenerId($_idPago);
		
			//Necesito la cantidad a pagar!! -_-
			$_cantidad = "";
			//Obtener subasta
			$_subasta['id'] = $datos->idSubasta;
			$_subasta = (object)$_subasta;			
			$_tmpofertas = $this->obtenerOfertaPorSubastaId($_subasta);
			if (!is_array($_tmpofertas) && property_exists($_tmpofertas,'error')) {
				return new Error('Error fatal: No se encontró la cantidad de la subasta para la que se pretende realizar el pago. Por favor contacte al administrador.', 'No se encontraron ofertas para la subasta con id: ' . $_subasta->id);
			}
			if (!is_array($_tmpofertas) && !property_exists($_tmpofertas,'error')) {
				$_cantidad = $_tmpofertas->cantidad;
			} elseif (is_array($_tmpofertas)) {
				$_tmpMaxCant = 0;
				foreach ($_tmpofertas as $l => $v) {
					if ($v->cantidad > $_tmpMaxCant) {
						$_tmpMaxCant = $v->cantidad;
					}
				}
				$_cantidad = $_tmpMaxCant;
			}
			if(strcmp($_cantidad, "") == 0) {
				return new Error("Error Fatal: no se pudo realizar el pago. Por favor contacte al administrador","Error al intentar encontrar la cantidad a pagar");
			}
		
		$_pago['id'] = $_idPago;
		$_pago['tipoPagoId'] = $_tipoPagoId;
		$_pago['subastaId'] = $datos->idSubasta;
		$_pago['fecha'] = date('Y-m-d h:i:s');
		$_pago['pagoAceptado'] = true; //Tenemos mucho dineros!
		$_pago['cantidad'] = $_cantidad;
		$_pago = (object)$_pago;
		$_pago = $this->agregarPago($_pago);
		if (property_exists($_pago,'error')) {
			return new Error('Error fatal: error al intentar guardar el pago. Por favor contacte al administrador.', $_pago->error);
		}
		return $_pago;
	}
	
	/**
	 * Obtener la cantidad a pagar por una subasta dependiendo de su tipo
	 */	
	function cantidadPago ($datos) {
		if (!property_exists($datos,'id')) {
				return new Error('Error fatal: no se encontró el id de la subsata a pagar. Por favor contacte al administrador.','El objeto proporcionado no contiene el id de la subasta.');				
		}

		//Obtener subasta
		$_subasta['id'] = $datos->id;
		$_subasta = (object)$_subasta;
		$_subasta = $this->obtenerSubastaPorId($_subasta);
		if (property_exists($_subasta,'error')) {
			return new Error('Error fatal: No se pudo identificar la subasta seleccionada. Por favor contacte al administrador.', 'No se encontro una subasta con el id ' . $_subasta->id);
		}

		//Obtener ofertas para la subasta
		$_listaOfertas = array();
		$_ofertas['subastaId'] = $_subasta->id;
		$_ofertas = (object)$_ofertas;
		$_ofertas = $this->obtenerOfertaPorSubastaId($_ofertas);
		
		//Determinar la cantidad mas alta y la segunda cantidad mas alta
		$_cantidadMasAlta = 0;
		$_segundaCantidadMasAlta = 0;		
		if (!is_array($_ofertas) && !property_exists($_ofertas,'error')) {
			$_cantidadMasAlta = $_segundaCantidadMasAlta =$_ofertas->cantidad;
		} else if (is_array($_ofertas)) {
			foreach ($_ofertas as $llave => $valor) {
				if ($valor->cantidad > $_cantidadMasAlta) {
					$_segundaCantidadMasAlta = $_cantidadMasAlta;
					$_cantidadMasAlta = $valor->cantidad;
				} elseif($valor->cantidad > $_segundaCantidadMasAlta) {
					$_segundaCantidadMasAlta = $valor->cantidad;
				}
			}
		}

		//Obtener el tipo de subasta estado subasta
		$_tipoSubastas = $this->obtenerTipoSubastas();
		$_inglesaId = "";
		$_inglesaId = "";
		foreach ($_tipoSubastas as $llave => $valor) {
			if (strcmp($valor->nombre, 'Inglesa') == 0) {
				$_inglesaId= $valor->id;
			}
			if (strcmp($valor->nombre, 'Vickrey') == 0) {
				$_vickReyId= $valor->id;
			}						
		}
		if (strcmp($_inglesaId, "") == 0) {
			return new Error('Error fatal: no se encontró el tipo de subasta inglesa. Por favor contacte al administrador.', 'No se pudo encontrar un id para tipo de subasta Inglesa');
		}
		if (strcmp($_vickReyId, "") == 0) {
			return new Error('Error fatal: no se encontró el tipo de subasta vickrey. Por favor contacte al administrador.', 'No se pudo encontrar un id para tipo de subasta Vickrey');
		}

		//Si es subasta inglesa regresar la cantidad de la oferta mas alta		
		if ($_subasta->tipoSubastaId == $_inglesaId) {
			$_subasta = (array)$_subasta;
			$_subasta['tipoSubasta'] = 'Inglesa';
			$_subasta['cantidad'] = $_cantidadMasAlta;
			$_subasta = (object)$_subasta;
			return $_subasta;
		}
		//Si es subasta vickrey regresar la cantidad de la segunda oferta mas alta
		if ($_subasta->tipoSubastaId == $_vickReyId) {
			$_subasta = (array)$_subasta;
			$_subasta['tipoSubasta'] = 'Vickrey';
			if ($_segundaCantidadMasAlta == 0) {
				$_subasta['cantidad'] = $_cantidadMasAlta;
			} else {
				$_subasta['cantidad'] = $_segundaCantidadMasAlta;
			}
			$_subasta = (object)$_subasta;
			return $_subasta;
		}
		
		//No deberia de llegar aqui
		return new Error('Error fatal: no se pudo determinar el tipo de subasta para asignar la cantidad a pagar. Por favor contacte al administrador.', 'El tipo de subasta no coincidio con ninguno de los tipos de subasta disponibles.');
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
