/**
 * CONSTANTS
 */
var CONFIGURACION = (function() {
	var port = window.location.port != "" ? ':' + window.location.port : "";
	var SERVIDOR_ACTUAL_WS = window.location.protocol + '//' + window.location.hostname + port +'/bidOn-ws/controlador/';
	var SERVIDOR_ACTUAL = window.location.protocol + '//' + window.location.hostname + port +'/websiteBidOn/';
	var private = {
		'INICIO_SESION' : SERVIDOR_ACTUAL_WS + 'InicioSesion',
		'REGISTRO_NUEVO_USUARIO' : SERVIDOR_ACTUAL_WS + 'RegistroNuevoUsuario',
		'REGISTRO_NUEVA_SUBASTA' : SERVIDOR_ACTUAL_WS + 'RegistroNuevaSubasta',
		'DATOS_SUBASTA' : SERVIDOR_ACTUAL_WS + 'DatosSubasta',
		'REGISTRO_NUEVA_OFERTA' : SERVIDOR_ACTUAL_WS + 'RegistroNuevaOferta',
		'SUBASTAS_ACTIVAS' : SERVIDOR_ACTUAL_WS + 'SubastasActivas',
		'SUBASTAS_INACTIVAS' : SERVIDOR_ACTUAL_WS + 'SubastasInactivas',
		'ACTIVAR_SUBASTA' : SERVIDOR_ACTUAL_WS + 'ActivarSubasta',
		'OBTENER_USUARIOS_COMPLETOS' : SERVIDOR_ACTUAL_WS + 'ObenerUsuariosCompletos',
		'OBTENER_ESTADO_USUARIOS' : SERVIDOR_ACTUAL_WS + 'EstadoUsuarios',
		'EDITAR_USUARIO' : SERVIDOR_ACTUAL_WS + 'Usuario',
		'OBTENER_ROLES' : SERVIDOR_ACTUAL_WS + 'Roles',
		'DESACTIVAR_SUBASTA' : SERVIDOR_ACTUAL_WS + 'DesactivarSubasta',
		'SUBASTAS_PENDIENTES' : SERVIDOR_ACTUAL_WS + 'SubastasPendientes',
		'CANTIDAD_PAGO': SERVIDOR_ACTUAL_WS + 'CantidadPago',
		'MIS_SUBASTAS' : SERVIDOR_ACTUAL_WS + 'MisSubastas',
		'REALIZAR_PAGO' : SERVIDOR_ACTUAL_WS + 'RealizarPago',
		'ROL' : SERVIDOR_ACTUAL_WS + 'Rol',
		'CATEGORIAS' : SERVIDOR_ACTUAL_WS + 'Categorias',
		'TIPO_SUBASTAS' : SERVIDOR_ACTUAL_WS + 'TipoSubastas',
		'SERVIDOR_URL' : SERVIDOR_ACTUAL,
		'URL_SUBIR_IMAGENES' : SERVIDOR_ACTUAL + 'subirImagenes.php',		
		'ENVIAR_CORREO' : SERVIDOR_ACTUAL + 'enviarCorreo.php'
	};

     return {
        get: function(name) { return private[name]; }
    };
})();
