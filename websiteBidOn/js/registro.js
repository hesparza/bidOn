var alertaExito = function(datos) {
    datos.datos.forEach(function(obj) {
        console.log("Id: " + obj.id + " Nombre: " + obj.nombre + " Descripcion: " + obj.descripcion);
    });
}
/**
 * Metodo principal
 */
$(document).ready(function() {	
	var funCom  = new FuncionesComunes();
	
//	var jsonObj = {
//			grant_type : "authorization_code",
//			code : $.urlParameters('code'),
//			redirect_uri : EPMgr.getCurrentEndPointRedirectUri(),
//			client_id : CONFIG.get('OAUTH2_CLIENT_ID'),
//			client_password : CONFIG.get('OAUTH2_CLIENT_PASSWORD')
//		};
	
	resultado = funCom.llamadaWS({},'http://localhost:80/bidOn-ws/controlador/Roles','GET', true, alertaExito, function() {});
//    (jsonObj, uri, tipo, esSincrono, funcionExito, funcionFallo)
});