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
		'ROL' : SERVIDOR_ACTUAL_WS + 'Rol',
		'CATEGORIAS' : SERVIDOR_ACTUAL_WS + 'Categorias',
		'SERVIDOR_URL' : SERVIDOR_ACTUAL,
		'URL_SUBIR_IMAGENES' : SERVIDOR_ACTUAL + 'subirImagenes.php',		
	};

     return {
        get: function(name) { return private[name]; }
    };
})();