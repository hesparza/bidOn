var subastas;
var fc = new FuncionesComunes();


var falloLlamada = function(obj) {
	alert("Error al llamar el servicio web: " + obj);
}

var cargarSubastasActivas = function(obj) {
	if ( typeof obj === 'object') {
		if (obj.datos.hasOwnProperty('error')) {
			alert(obj.datos.mensaje);
			console.log("Mensaje de error: " + obj.datos.mensaje + ", error reportado: " + obj.datos.error);
		} else {
			subastas = obj.datos;
			var contador = 0;
			var ids = 0;
			var fechaActual = new Date();
			$("#listaSubastas").append("<ul class=\"destacadas\" id=\"subastas" + ids + "\"></ul>");
			for (i = 0; i < subastas.length; i++) {
				subasta = subastas[i];
				if (contador >= 3) {
					contador = 0;
					ids++;
					if (ids == 2) {
						break;
					}
					$("#listaSubastas").append("<ul class=\"destacadas\" id=\"subastas" + ids + "\"></ul>");
				}
				var fechaFin = new Date(subasta.fechaFin.replace(/-/g,'/'));
				var fechaResta = Math.abs(fechaFin.getTime() - fechaActual.getTime());
				var fechaRestaDias = Math.ceil(fechaResta / (1000 * 3600 * 24));
				if (fechaRestaDias < 0) {
					fechaResta = 0;
				}
				$("#subastas" + ids).append("" +
						"<li> <img src=\""+subasta.imagenes[0].ruta+"\" width=\"200\" height=\"200\">" +
						"<h3><a href=\"subasta.php?id="+ subasta.id +"\">"+subasta.articulo.nombre+"</a></h3>" +
						"<p>" + subasta.articulo.descripcion +
						"<span class=\"contador\">" + fechaRestaDias + " días para finalizar</span> </p>" +
						"<p class=\"oferta\"> <span class=\"ahora\">$"+subasta.articulo.precio+"</span></p>" +
						"</li>"
				);
				contador++;				
			}
		}			
	}	
}


function cargarDatos() {
	$("#estado").empty().append("<label>Estado: " + estadoSubasta.nombre + "<label>");
	$("#precioInicial").empty().append("<label>Precio inicial: " + articulo.precio + "<label>");
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
		if ($("#NOM_USUARIO").val() === "") {
			alert("La sesion no es valida, por favor inicie sesion nuevamente");
			window.location.replace('index.php');
		}
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
	fc.llamadaWS({},CONFIGURACION.get('SUBASTAS_ACTIVAS'),'POST', false, cargarSubastasActivas, falloLlamada);
});