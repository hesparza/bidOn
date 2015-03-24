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
		'SERVIDOR_URL' : SERVIDOR_ACTUAL,
	};

     return {
        get: function(name) { return private[name]; }
    };
})();