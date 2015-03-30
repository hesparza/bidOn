var listaUsuarios;
var arrEstadosUsuario = new Array();;
var arrRoles = new Array();;
var fc = new FuncionesComunes();

var falloLlamada = function(obj) {
	alert("Error al llamar el servicio web: " + obj);
}

var cargarUsuarios = function(obj) {
	if ( typeof obj === 'object') {
		if (obj.datos.hasOwnProperty('error')) {
			alert(obj.datos.mensaje);
			console.log("Mensaje de error: " + obj.datos.mensaje + ", error reportado: " + obj.datos.error);
		} else {			
			listaUsuarios = obj.datos;
		}			
	}	
}

var cargarEstadoUsuarios = function(obj) {
	if ( typeof obj === 'object') {
		if (obj.datos.hasOwnProperty('error')) {
			alert(obj.datos.mensaje);
			console.log("Mensaje de error: " + obj.datos.mensaje + ", error reportado: " + obj.datos.error);
		} else {			
			obj.datos.forEach(function(estadoUsuario) { 
				arrEstadosUsuario[estadoUsuario.id] = estadoUsuario.nombre;				
			});
		}			
	}	
}

var cargarRoles = function(obj) {
	if ( typeof obj === 'object') {
		if (obj.datos.hasOwnProperty('error')) {
			alert(obj.datos.mensaje);
			console.log("Mensaje de error: " + obj.datos.mensaje + ", error reportado: " + obj.datos.error);
		} else {
			obj.datos.forEach(function(rol) { 
				arrRoles[rol.id] = rol.nombre;				
			});
		}			
	}	
}

verificarUsuarioEditado = function(obj) {
	if ( typeof obj === 'object') {
		if (obj.datos.hasOwnProperty('error')) {
			alert(obj.datos.mensaje);
			console.log("Mensaje de error: " + obj.datos.mensaje + ", error reportado: " + obj.datos.error);
		} else {			
			alert('El usuariio ' + obj.datos.nomUsuario + ' ha sido actualizado exitosamente');
		}			
	}		
}

function cargarDatos() {
	if(typeof listaUsuarios === 'undefined') {
		alert("Error fatal: no se pudieron cargar los usuarios. Por favor contacte al administrador.");
	};
	
	listaUsuarios.forEach(function(usuario) {
		var selectEstadosUsuario = '<select class="editarUsuario" name="estadoUsuario" id="selectEstadoUsuario' + usuario.id + '">';
		arrEstadosUsuario.forEach(function(estadoUsuario) {							
			if(estadoUsuario === usuario.estadoUsuario) {
				selectEstadosUsuario+='<option value="' + usuario.estadoUsuarioId + '" selected>' + estadoUsuario + '</option>';
			} else {
				selectEstadosUsuario+='<option value="' + usuario.estadoUsuarioId + '">' + estadoUsuario + '</option>';
			}						  
		});
		selectEstadosUsuario+='</select>';
			
		var selectRol = '<select class="editarUsuario" name="rol" id="selectRol' + usuario.id + '">';
		arrRoles.forEach(function(rol) {
			if(rol === usuario.rol) {
				selectRol+='<option value="' + usuario.rolId + '" selected>' + rol + '</option>';
			} else {
				selectRol+='<option value="' + usuario.rolId + '">' + rol + '</option>';
			}						  
		});
		selectRol+='</select>';
			
		$('#usuariosAdmin > tbody:last').append('<tr>' +
				'<output id="reglonUsuario' + usuario.id + '">'+
				'<td><input type="text" class="editarUsuario" value="' + usuario.nomUsuario + '" id="nomUsuario'+ usuario.id +'" /></td>' +
				'<td><input type="text" class="editarUsuario" value="' + usuario.nombre + '" id="nombre'+ usuario.id +'" /></td>' +
				'<td><input type="text" class="editarUsuario" value="' + usuario.apellidoP + '" id="apellidoP'+ usuario.id +'" /></td>' +
				'<td><input type="text" class="editarUsuario" value="' + usuario.apellidoM + '" id="apellidoM'+ usuario.id +'" /></td>' +
				'<td><input type="text" class="editarUsuario" value="' + usuario.correo + '" id="correo'+ usuario.id +'" /></td>' +
				'<input type="hidden" class="editarUsuario" value="' + usuario.contrasena + '" id="contrasena'+ usuario.id +'" />' +
				'<input type="hidden" class="editarUsuario" value="' + usuario.reputacion + '" id="reputacion'+ usuario.id +'" />' +
				'<td>' + selectEstadosUsuario + '</td>' +
				'<td>' + selectRol + '</td>' +
				
				'<td>' +
					'<button type="button" value="' + usuario.id + '" id="btnActualizar' + usuario.id +'" class="editarUsuario">Actualizar Usuario</button>'+
				'</td>' +
				'</output></tr>');
		$("body").on('click','#btnActualizar' + usuario.id, function() {
			fc.llamadaWS(recolectarDatos($(this).attr("value")),CONFIGURACION.get('EDITAR_USUARIO'),'PUT', false, verificarUsuarioEditado, falloLlamada);
		});
	});
	
}

function recolectarDatos(id) {
	var obj = {
			id:id,
			estadoUsuarioId:arrEstadosUsuario.indexOf($('#selectEstadoUsuario' + id).find(":selected").text()),
			rolId:arrRoles.indexOf($('#selectRol' + id).find(":selected").text()),
			nombre:$("#nombre" + id).val(),
			apellidoP:$("#apellidoP" + id).val(),
			apellidoM:$("#apellidoM" + id).val(),
			correo:$("#correo" + id).val(),
			nomUsuario:$("#nomUsuario" + id).val(),
			contrasena:$("#contrasena" + id).val(),
			reputacion:$("#reputacion" + id).val(),
	}
	return obj;
}


function validarUsuario() {
	if ($("#NOM_USUARIO").val() === "") {
		alert("La sesion no es valida, por favor inicie sesion nuevamente");
		window.location.replace('index.php');
	}
}


/**
 * Metodo principal
 */
$(document).ready(function() {
	validarUsuario();
	//cargar datos de los usuarios
	fc.llamadaWS({},CONFIGURACION.get('OBTENER_USUARIOS_COMPLETOS'),'POST', false, cargarUsuarios, falloLlamada);
	fc.llamadaWS({},CONFIGURACION.get('OBTENER_ESTADO_USUARIOS'),'GET', false, cargarEstadoUsuarios, falloLlamada);
	fc.llamadaWS({},CONFIGURACION.get('OBTENER_ROLES'),'GET', false, cargarRoles, falloLlamada);
	cargarDatos();
});