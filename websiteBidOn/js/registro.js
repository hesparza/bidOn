var registroUsuario = function(obj) {
	if ( typeof obj === 'object') {
		if (obj.datos.hasOwnProperty('error')) {
			alert(obj.datos.mensaje);
			console.log(obj.datos.error);
		} else {
			var fc = new FuncionesComunes();
			fc.borrarHtml("userZone");
			fc.insertarHeaderHtml("userZone", 2, "Gracias " + obj.datos.nombre + "!");
			fc.insertarHeaderHtml("userZone", 2, "Tu usuario <u>" + obj.datos.nomUsuario + "</u> ha quedado registrado satisfactoriamente.");
			fc.insertarHeaderHtml("userZone", 2, "Haz click <a href=\"inicioSesion.php\">aqui</a> para comenzar a subastar.");
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
	$("#FormaRegistro").submit(function() {        
		if (!fc.todosLosCamposLlenos([$("#nombre").val(),
		                              $("#apellidoPaterno").val(),
		                              $("#apellidoMaterno").val(),
		                              $("#correo").val(),
		                              $("#nomUsuario").val(),
		                              $("#contrasena").val()]))
		{
			alert('Por favor llene todos los campos');
			return false;
		}
		
		if (!fc.esNombreValido($("#nombre").val())) {
			alert('El nombre no es correcto, solo puede contener letras.');
			return false;
		}
		if (!fc.esNombreValido($("#apellidoPaterno").val())) {
			alert('El apellido paterno esta incorrecto.');
			return false;
		}		
		if (!fc.esNombreValido($("#apellidoMaterno").val())) {
			alert('El apellido materno esta incorrecto.');
			return false;
		}		
		if (!fc.esEmailValido($("#correo").val())) {
			alert('El correo esta incorrecto.');
			return false;
		}
		if (!fc.esNombreUsuarioValido($("#nomUsuario").val())) {
			alert('El nombre de usuario esta incorrecto, unicamente puede ' +
				  'contener letras minusculas y debe de ser de al menos 6 caracteres.');
			return false;
		}
		if($("#contrasena").val().length < 6) {
			alert('La contraseña debe de contener al menos 6 caracteres.');
			return false;			
		}
		//TODO validar contrasena
		
		console.log('FormaRegistro esta correcta');
		var datos = {nombre:$("#nombre").val(),
					apellidoP:$("#apellidoPaterno").val(),
					apellidoM:$("#apellidoMaterno").val(),
					correo:$("#correo").val(),
					nomUsuario:$("#nomUsuario").val(),
					contrasena:$("#contrasena").val()};
		fc.llamadaWS(datos,CONFIGURACION.get('REGISTRO_NUEVO_USUARIO'),'POST', false, registroUsuario, falloLlamada);
		return false;
	});	
	//return false;
});