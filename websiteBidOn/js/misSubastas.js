var listaSubastasUsuario;
var listaSubastasOferta;
var fc = new FuncionesComunes();

var falloLlamada = function(obj) {
	alert("Error al llamar el servicio web: " + obj);
}

var cargarSubastas = function(obj) {
	if ( typeof obj === 'object') {
		if (obj.datos.hasOwnProperty('error')) {
			alert(obj.datos.mensaje);
			console.log("Mensaje de error: " + obj.datos.mensaje + ", error reportado: " + obj.datos.error);
		} else {
			listaSubastasUsuario = obj.datos.listaSubastasUsuario;
			listaSubastasOferta = obj.datos.listaSubastasOferta;
		}			
	}	
}


function cargarDatos() {
	if(typeof listaSubastasUsuario === 'undefined' || typeof listaSubastasOferta === 'undefined'){
		alert("Error fatal: no se pudieron cargar las subastas para el usuario. Por favor contacte al administrador.");
	};
	
	listaSubastasUsuario.forEach(function(subasta) {

		$('#misSubastas > tbody:last').append('<tr>' +
				'<td><a href="subasta.php?id='+ subasta.id + '">' + subasta.articulo.nombre +'</a></td>' +
				'<td>' + subasta.estadoSubasta +'</td>' +
				'<td>' + subasta.tipoSubasta +'</td>' +
				'<td> Creador </td>' +
				'<td>' + subasta.fechaFin +'</td>' +
				'</tr>');
	});
	
	listaSubastasOferta.forEach(function(subasta) {
		var relacion = 'Ofertó';
		var estado = subasta.estadoSubasta;
		if (subasta.gano) {
			realcion ='<a href="pago.php?id="'+ subasta.id +'>GANADOR! -- Pagar</a>';
			estado = 'Inactiva';
		}
		$('#misSubastas > tbody:last').append('<tr>' +
				'<td><a href="subasta.php?'+ subasta.id + '">' + subasta.articulo.nombre +'</a></td>' +
				'<td>' + estado +'</td>' +
				'<td>' + subasta.tipoSubasta +'</td>' +
				'<td> ' + relacion + ' </td>' +
				'<td>' + subasta.fechaFin +'</td>' +
				'</tr>');				
	});
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
	//cargar datos de las subastas
	fc.llamadaWS({nomUsuario:$("#NOM_USUARIO").val()},CONFIGURACION.get('MIS_SUBASTAS'),'POST', false, cargarSubastas, falloLlamada);
	cargarDatos();
//	$("#btnOfertar").click(function() {
//		if (!fc.todosLosCamposLlenos([$("#cantidadOferta").val()]))
//		{
//			alert('Por favor introduza una cantidad para ofertar');
//			return false;
//		}
//		if (!fc.esNumericoDecimal($("#cantidadOferta").val())) {
//			alert('La cantidad introducida no es valida.');
//			return false;
//		}		
//		var datos = {"nomUsuario":$("#NOM_USUARIO").val(),"idSubasta":$("#ID_SUBASTA").val(),"cantidad":$("#cantidadOferta").val()};
//		fc.llamadaWS(datos,CONFIGURACION.get('REGISTRO_NUEVA_OFERTA'),'POST', false, cargarNuevaOferta, falloLlamada);
//	});
});