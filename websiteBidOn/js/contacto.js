var verificarEnvioCorreo = function(obj) {
	if ( typeof obj === 'object') {
		if (obj.datos.hasOwnProperty('error')) {
			alert(obj.datos.mensaje);
			console.log("Mensaje de error: " + obj.datos.mensaje + ", error reportado: " + obj.datos.error);
		} else {
			alert("Tu correo ha sido enviado satisfactoriamente al equipo de BidOn. \nMuchas gracias!");
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
	$("#FormaContacto").submit(function() {        
		if (!fc.todosLosCamposLlenos([$("#correo").val(),
		                              $("#asunto").val(),
		                              $("#mensaje").val()]))
		{
			alert('Por favor llene todos los campos');
			return false;
		}
		
		if (!fc.esNombreValido($("#asunto").val())) {
			alert('El asunto sólo puede contener letras.');
			return false;
		}
		if (!fc.esEmailValido($("#correo").val())) {
			alert('El correo esta incorrecto.');
			return false;
		}
		console.log('FormaContacto esta correcta');
		datos = {correo:$("#correo").val(),asunto:$("#asunto").val(),mensaje:$("#mensaje").val()};
		fc.llamadaWS(datos,CONFIGURACION.get('ENVIAR_CORREO'),'POST', false, verificarEnvioCorreo, falloLlamada);                
	});	
});
