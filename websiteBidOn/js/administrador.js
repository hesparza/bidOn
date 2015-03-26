var listaSubastasActivas;
var listaSubastasPendientes;
var listaSubastasInactivas;
var fc = new FuncionesComunes();

var falloLlamada = function(obj) {
	alert("Error al llamar el servicio web: " + obj);
}

var cargarSubastasPendientes = function(obj) {
	if ( typeof obj === 'object') {
		if (obj.datos.hasOwnProperty('error')) {
			alert(obj.datos.mensaje);
			console.log("Mensaje de error: " + obj.datos.mensaje + ", error reportado: " + obj.datos.error);
		} else {			
			listaSubastasPendientes = obj.datos;
		}			
	}	
}

var cargarSubastasActivas = function(obj) {
	if ( typeof obj === 'object') {
		if (obj.datos.hasOwnProperty('error')) {
			alert(obj.datos.mensaje);
			console.log("Mensaje de error: " + obj.datos.mensaje + ", error reportado: " + obj.datos.error);
		} else {			
			listaSubastasActivas = obj.datos;
		}			
	}	
}

var cargarSubastasInactivas = function(obj) {
	if ( typeof obj === 'object') {
		if (obj.datos.hasOwnProperty('error')) {
			alert(obj.datos.mensaje);
			console.log("Mensaje de error: " + obj.datos.mensaje + ", error reportado: " + obj.datos.error);
		} else {			
			listaSubastasInactivas = obj.datos;
		}			
	}	
}

verificarSubastaDesactivada = function(obj) {
	if ( typeof obj === 'object') {
		if (obj.datos.hasOwnProperty('error')) {
			alert(obj.datos.mensaje);
			console.log("Mensaje de error: " + obj.datos.mensaje + ", error reportado: " + obj.datos.error);
		} else {			
			alert("Subasta desactivada exitosamente");
			$("#estadoSubasta"+obj.datos.id).empty();
			$("#estadoSubasta"+obj.datos.id).append("Inactiva");
			$("#btnSubasta"+obj.datos.id).empty();
			$("#btnSubasta"+obj.datos.id).append("Activar");
			$("#btnSubasta"+obj.datos.id).prop('class', 'btnActivar');
		}			
	}		
}

verificarSubastaActivada = function(obj) {
	if ( typeof obj === 'object') {
		if (obj.datos.hasOwnProperty('error')) {
			alert(obj.datos.mensaje);
			console.log("Mensaje de error: " + obj.datos.mensaje + ", error reportado: " + obj.datos.error);
		} else {			
			alert("Subasta activada exitosamente");
			$("#estadoSubasta"+obj.datos.id).empty();
			$("#estadoSubasta"+obj.datos.id).append("Activa");
			$("#btnSubasta"+obj.datos.id).empty();
			$("#btnSubasta"+obj.datos.id).append("Deshabilitar");
			$("#btnSubasta"+obj.datos.id).prop('class', 'btnDesactivar');
		}			
	}		
}

function cargarDatos() {
	if(typeof listaSubastasActivas === 'undefined' || typeof listaSubastasPendientes === 'undefined'){
		alert("Error fatal: no se pudieron cargar las subastas para el usuario. Por favor contacte al administrador.");
	};
	
	listaSubastasActivas.forEach(function(subasta) {
		$('#subastasAdmin > tbody:last').append('<tr>' +
				'<td><a href="subasta.php?id='+ subasta.id + '">' + subasta.articulo.nombre +'</a></td>' +
				'<td><output id="estadoSubasta' + subasta.id + '">' +subasta.estadoSubasta +'</output></td>' +
				'<td>' + subasta.fechaFin +'</td>' +
				'<td>'+
					'<button type="button" value="' + subasta.id + '" id="btnSubasta' + subasta.id +'" class="btnDesactivar">Deshabilitar</button>'+
				'</td>' +
				'</tr>');
	});
	
	listaSubastasPendientes.forEach(function(subasta) {
		$('#subastasAdmin > tbody:last').append('<tr>' +
				'<td><a href="subasta.php?id='+ subasta.id + '">' + subasta.articulo.nombre +'</a></td>' +
				'<td><output id="estadoSubasta' + subasta.id + '">' +subasta.estadoSubasta +'</output></td>' +
				'<td>' + subasta.fechaFin +'</td>' +
				'<td>'+
					'<button type="button" value="' + subasta.id + '" id="btnSubasta' + subasta.id + '" class="btnActivar">Activar</button>'+
				'</td>' +
				'</tr>');				
	});
	
	listaSubastasInactivas.forEach(function(subasta) {
		$('#subastasAdmin > tbody:last').append('<tr>' +
				'<td><a href="subasta.php?id='+ subasta.id + '">' + subasta.articulo.nombre +'</a></td>' +
				'<td><output id="estadoSubasta' + subasta.id + '">' +subasta.estadoSubasta +'</output></td>' +
				'<td>' + subasta.fechaFin +'</td>' +
				'<td>'+
					'<button type="button" value="' + subasta.id + '" id="btnSubasta' + subasta.id + '" class="btnActivar">Activar</button>'+
				'</td>' +
				'</tr>');				
	});
}


function validarUsuario() {
	if ($("#NOM_USUARIO").val() === "") {
		alert("La sesion no es valida, por favor inicie sesion nuevamente");
		window.location.replace('index.php');
	}
}

function registrarEventos() {
	$("body").on('click','.btnDesactivar', function() {
		fc.llamadaWS({id:$(this).attr("value")},CONFIGURACION.get('DESACTIVAR_SUBASTA'),'POST', false, verificarSubastaDesactivada, falloLlamada);
	});

	$("body").on('click','.btnActivar', function() {
		fc.llamadaWS({id:$(this).attr("value")},CONFIGURACION.get('ACTIVAR_SUBASTA'),'POST', false, verificarSubastaActivada, falloLlamada);
	});
}

/**
 * Metodo principal
 */
$(document).ready(function() {
	validarUsuario();
	//cargar datos de las subastas
	fc.llamadaWS({},CONFIGURACION.get('SUBASTAS_PENDIENTES'),'POST', false, cargarSubastasPendientes, falloLlamada);
	fc.llamadaWS({},CONFIGURACION.get('SUBASTAS_ACTIVAS'),'POST', false, cargarSubastasActivas, falloLlamada);
	fc.llamadaWS({},CONFIGURACION.get('SUBASTAS_INACTIVAS'),'POST', false, cargarSubastasInactivas, falloLlamada);	
	cargarDatos();
	registrarEventos();
});