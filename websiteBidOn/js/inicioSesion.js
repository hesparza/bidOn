var inicioSesion = function(obj) {
	if ( typeof obj === 'object') {
		if (obj.datos.hasOwnProperty('error')) {
			alert(obj.datos.mensaje);
			console.log("Mensaje de error: " + obj.datos.mensaje + ", error reportado: " + obj.datos.error);
		} else {
			var uri = CONFIGURACION.get('SERVIDOR_URL') + "iniciarSesion.php?" + "id=" + obj.datos.id
			+ "&estadoUsuario=" + obj.datos.estadoUsuario
			+ "&rol=" + obj.datos.rol
			+ "&estadoUsuarioId=" + obj.datos.estadoUsuarioId
			+ "&rolId=" + obj.datos.rolId		
			+ "&nombre=" + obj.datos.nombre 
			+ "&apellidoP=" + obj.datos.apellidoP 
			+ "&apellidoM=" + obj.datos.apellidoM 
			+ "&correo=" + obj.datos.correo 
			+ "&nomUsuario=" + obj.datos.nomUsuario 
			+ "&reputacion=" + obj.datos.reputacion;
			window.location.replace(uri);								
		} 		
	} else {
		alert("Error al cargar datos de inicio de sesión. Por favor contacte al administrador");
		return false;
	}	
}

var falloLlamada = function(obj) {
	alert("Error al llamar el servicio web: " + obj);
}
/**
 * Metodo principal
 */
$(document).ready(function() {
	var fc = new FuncionesComunes();
	$("#FormaInicioSesion").submit(function() {        
		if (!fc.todosLosCamposLlenos([$("#nomUsuario").val(),$("#contrasena").val()]))
		{
			alert('Por favor llene todos los campos');
			return false;
		}
		console.log('FormaInicioSesion esta correcta');
		var datos = {nomUsuario:$("#nomUsuario").val(), contrasena:$("#contrasena").val()};
		obj = fc.llamadaWS(datos,CONFIGURACION.get('INICIO_SESION'),'POST', false, inicioSesion, falloLlamada);
		return false;
	});	
	//return false;
});