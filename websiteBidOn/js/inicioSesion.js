var inicioSesion = function(obj) {
	if ( typeof obj === 'object') {
		if (obj.datos.hasOwnProperty('error')) {
			alert(obj.datos.mensaje);
			console.log(obj.datos.error);
		} else {
			console.log("Id: " + obj.datos.id + " Nombre: " + obj.datos.nombre);
			var fc = new FuncionesComunes();
			var uri = CONFIGURACION.get('ROL')  + '/id';
			var datos = {id:obj.datos.rolId};
			var rol = fc.llamadaWS(datos,uri,'GET', false, function(e){}, falloLlamada);
			
			if (rol != false && !rol.datos.hasOwnProperty('error')) {
				var uri = CONFIGURACION.get('SERVIDOR_URL') + "iniciarSesion.php?" + "id=" + obj.datos.id
				+ "&estadoUsuarioId=" + obj.datos.estadoUsuarioId 
				+ "&rol=" + rol.datos.nombre
				+ "&nombre=" + obj.datos.nombre 
				+ "&apellidoP=" + obj.datos.apellidoP 
				+ "&apellidoM=" + obj.datos.apellidoM 
				+ "&correo=" + obj.datos.correo 
				+ "&nomUsuario=" + obj.datos.nomUsuario 
				+ "&reputacion=" + obj.datos.reputacion;
				window.location.replace(uri);				
			} else {
				alert("Error al tratar de hacer inicio de sesión. Por favor contacte al administrador");
			}				

//			fc.llamadaWS(datos,uri,'GET', false, inicioSesion, falloLlamada);
		}			
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