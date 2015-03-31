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
	//$("#nomUsuario").empty().append("<label>Creador: " + $("#NOM_USUARIO").val() + "</label>");
	$("#nombre").empty().append("<label>Nombre:</label><span> " + articulo.nombre + "</span>");
	$("#estado").empty().append("<label>Estado:</label><span> " + estadoSubasta.nombre + "</span>");
	$("#precioInicial").empty().append("<label>Precio inicial:</label><span> $" + articulo.precio + "</span>");
	$("#tipo").empty().append("<label>Tipo de subasta:</label><span> " + tipoSubasta.nombre + "</span>");
	$("#cantidad").empty().append("<label>Cantidad:</label><span> " + articulo.cantidad + "</span>");
	$("#fechainicio").empty().append("<label>Fecha de inicio:</label><span> " + subasta.fechaInicio + "</span>");
	$("#fechafin").empty().append("<label>Fecha de finalización:</label><span> " + subasta.fechaFin + "</span>");
	$("#descripcion").empty().append("<span>" + articulo.descripcion + "</span>");
}

function cargarOfertas() {
	if (ofertasUsuarios.length > 0) {
		$('#tablaOfertas > tbody:last').append('<tr><td><b>Usuario:</b></td><td><b>Fecha:</b></td><td><b>Cantidad:</b></td></tr>');
		ofertasUsuarios.forEach(function(ofertaUsuario) {
			$('#tablaOfertas > tbody:last').append('<tr><td>'+ ofertaUsuario.nomUsuario +'</td><td>' + ofertaUsuario.fecha + '</td><td>$' + ofertaUsuario.cantidad + '</td></tr>');
		});		
	} else {
		$('#tablaOfertas > tbody:last').append('<tr><td colspan="3">Aun no existen ofertas para esta subasta, ¡Se el primero en ofertar!</td></tr>');
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