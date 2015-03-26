var subasta;
var articulo;
var imagenes;
var tipoSubasta;
var estadoSubasta;
var ofertasUsuarios;
var fc = new FuncionesComunes();

var falloLlamada = function(obj) {
	alert("Error al llamar el servicio web: " + obj);
}

var cargarDatosSubasta = function(obj) {
	if ( typeof obj === 'object') {
		if (obj.datos.hasOwnProperty('error')) {
			alert(obj.datos.mensaje);
			console.log("Mensaje de error: " + obj.datos.mensaje + ", error reportado: " + obj.datos.error);
		} else {
			subasta = obj.datos.subasta;
			articulo = obj.datos.articulo;
			imagenes = obj.datos.imagenes;
			tipoSubasta = obj.datos.tipoSubasta;
			estadoSubasta = obj.datos.estadoSubasta;
			ofertasUsuarios = obj.datos.ofertasUsuarios;
		}			
	}	
}

var cargarNuevaOferta = function(obj) {
	if ( typeof obj === 'object') {
		if (obj.datos.hasOwnProperty('error')) {
			alert(obj.datos.mensaje);
			console.log("Mensaje de error: " + obj.datos.mensaje + ", error reportado: " + obj.datos.error);
		} else {
			alert('Tu oferta ha sido registrada satisfactoriamente!');
			$('#tablaOfertas > tbody:last').append('<tr><td><b>Usuario:</b> '+ $("#NOM_USUARIO").val() +' <b>Fecha:</b> ' + obj.datos.fecha + ' <b>Cantidad:</b> $' + obj.datos.cantidad + '</td></tr>').slideDown();
		}			
	}
}

function cargarImagenes() {
//	imagenes.forEach(function(imagen) {
//		$("#imagenes").append("<img src="+imagen.ruta+" alt=\"\" width=\"300\" height=\"200\">");
//	});	
	$("#imagenes").append("<img src="+imagenes[0].ruta+" alt=\"\" width=\"300\" height=\"200\">");
}

function cargarDatos() {
	$("#nombre").empty().append("<label>Nombre: " + articulo.nombre + "<label>");
	$("#estado").empty().append("<label>Estado: " + estadoSubasta.nombre + "<label>");
	$("#precioInicial").empty().append("<label>Precio inicial: $" + articulo.precio + "<label>");
	$("#tipo").empty().append("<label>Tipo de subasta: " + tipoSubasta.nombre + "<label>");
	$("#cantidad").empty().append("<label>Cantidad: " + articulo.cantidad + "<label>");
	$("#fechainicio").empty().append("<label>Fecha de inicio: " + subasta.fechaInicio + "<label>");
	$("#fechafin").empty().append("<label>Fecha de finalización: " + subasta.fechaFin + "<label>");
	$("#descripcion").empty().append("<label>Descripción:<br/>" + articulo.descripcion + "<label>");
}

function cargarOfertas() {
	if (ofertasUsuarios.length > 0) {
		ofertasUsuarios.forEach(function(ofertaUsuario) {
			$('#tablaOfertas > tbody:last').append('<tr><td><b>Usuario:</b> '+ ofertaUsuario.nomUsuario +' <b>Fecha:</b> ' + ofertaUsuario.fecha + ' <b>Cantidad:</b> $' + ofertaUsuario.cantidad + '</td></tr>');
		});		
	} else {
		$('#tablaOfertas > tbody:last').append('<tr><td>Aun no existen ofertas para esta subasta, ¡Se el primero en ofertar!</td></tr>');
	}
}

function validarPosibleOfertar() {
	if ($("#NOM_USUARIO").val() !== "" && estadoSubasta.nombre == 'Activa'){
		$("#btnOfertar").prop('disabled', false);
		$("#cantidadOferta").prop('disabled', false);
	} else {
		$("#btnOfertar").prop('disabled', true);
		$("#btnOfertar").hide();
		$("#cantidadOferta").hide();
//		if ($("#NOM_USUARIO").val() === "") {
//			alert("La sesion no es valida, por favor inicie sesion nuevamente");
//			window.location.replace('index.php');
//		}
		if (estadoSubasta.nombre != 'Activa'){
			$("#textoOfertar").empty().append("<b>No es posible ofertar ya que la subasta no esta activa</b>");	
		}
	}
	
}

(function($) {
    $(function() {
        $('[data-jcarousel]').each(function() {
            var el = $(this);
            el.jcarousel(el.data());
        });

        $('[data-jcarousel-control]').each(function() {
            var el = $(this);
            el.jcarouselControl(el.data());
        });
    });
})(jQuery);

/**
 * Metodo principal
 */
$(document).ready(function() {
	//cargar datos de la subasta, inicializando variables globales
	fc.llamadaWS({id:$("#ID_SUBASTA").val()},CONFIGURACION.get('DATOS_SUBASTA'),'POST', false, cargarDatosSubasta, falloLlamada);
	//cargar datos
	cargarImagenes();
	cargarDatos();
	cargarOfertas();
	validarPosibleOfertar();
	//TODO verificar fecha de finalizacion
	
	$("#btnOfertar").click(function() {
		if (!fc.todosLosCamposLlenos([$("#cantidadOferta").val()]))
		{
			alert('Por favor introduza una cantidad para ofertar');
			return false;
		}
		if (!fc.esNumericoDecimal($("#cantidadOferta").val())) {
			alert('La cantidad introducida no es valida.');
			return false;
		}		
		var datos = {"nomUsuario":$("#NOM_USUARIO").val(),"idSubasta":$("#ID_SUBASTA").val(),"cantidad":$("#cantidadOferta").val()};
		fc.llamadaWS(datos,CONFIGURACION.get('REGISTRO_NUEVA_OFERTA'),'POST', false, cargarNuevaOferta, falloLlamada);
	});
});