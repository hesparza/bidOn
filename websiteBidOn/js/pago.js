//variables globales
var formaPago = "";
var nomUsuario = "";
var idSubasta = "";
var fc = new FuncionesComunes();

var verificarPago = function(obj) {
	if ( typeof obj === 'object') {
		if (obj.datos.hasOwnProperty('error')) {
			alert(obj.datos.mensaje);
			console.log("Mensaje de error: " + obj.datos.mensaje + ", error reportado: " + obj.datos.error);
		} else {
			fc.borrarHtml("comentarios");
			fc.insertarHeaderHtml("comentarios", 2, "Gracias " + nomUsuario+ "!");
			fc.insertarHeaderHtml("comentarios", 2, "Tu pago ha quedado registrado satisfactoriamente y tu envio estará en camino pronto.");
			fc.insertarHeaderHtml("comentarios", 2, "Haz click <a href=\"inicioSesion.php\">aqui</a> para seguir subastando.");
		}			
	}	
}


var falloLlamada = function(obj) {
	alert("Error al llamar el servicio web: " + obj);
}

//TODO implementar esta funcion para cargar formas de pago
function cargarFormasDePago() {
	obj = fc.llamadaWS({},CONFIGURACION.get('CATEGORIAS'),'GET', false, function(e) {}, falloLlamada);
	if ( typeof obj === 'object') {
		if (obj.datos.hasOwnProperty('error')) {
			alert(obj.datos.mensaje);
			console.log("Mensaje de error: " + obj.datos.mensaje + ", error reportado: " + obj.datos.error);
		} else {
			obj.datos.forEach(function(categoria) {
			    $("#categoria").append( $('<option></option>').val(categoria.id).html(categoria.nombre));
			});
		}		
	} else {
		alert("Error al cargar las categorias. Por favor contacte al administrador");
		return false;
	}
}

$(document).on('change', '#formaPago', function(e) {
	categoria = this.options[e.target.selectedIndex].value;
});

/**
 * Metodo principal
 */
$(document).ready(function() {
	//Cargar variable que contiene el usuario
	nomUsuario = $("#NOM_USUARIO").val();
	idSubasta = $("#ID_SUBASTA").val();
	if (nomUsuario == "" || nomUsuario === 'undefined') {
		alert("Error fatal: no se encontro un usuario valido en la sesion. Por favor contacte al administrador.");
		return false;
	}
	if (idSubasta == "" || idSubasta === 'undefined') {
		alert("Error fatal: no se encontro un id de subasta válido para pagar. Por favor intente de nuevo o contacte a su administrador.");
		return false;
	}	
	//Cargar formas de pago
	//cargarFormasDePago(); //TODO <-- Implementar esta funcion
	$("#FormaPago").submit(function() {        
		if (!fc.todosLosCamposLlenos([$("#nombre").val(),
		                              $("#numeroTarjeta").val(),
		                              $("#cvv").val(),
		                              $("#calle").val(),
		                              $("#numeroExterior").val(),
		                              $("#numeroInterior").val(),
		                              $("#colonia").val(),
		                              $("#codigoPostal").val(),
		                              $("#ciudad").val(),
		                              $("#estado").val(),
		                              $("#pais").val()]))
		{
			alert('Por favor llene todos los campos');
			return false;
		}
		console.log('FormaPago esta correcta');
		var datos = {nombre:$("#nombre").val(),
					numeroTarjeta:$("#numeroTarjeta").val(),
					cvv:$("#cvv").val(),
					calle:$("#calle").val(),
					numeroExterior:$("#numeroExterior").val(),
					numeroInterior:$("#numeroInterior").val(),
					colonia:$("#colonia").val(),
					codigoPostal:$("#codigoPostal").val(),
					ciudad:$("#ciudad").val(),
					estado:$("#estado").val(),
					pais:$("#pais").val(),
					fechaexpiracion:$("#fechaexpiracion").val(),
					nomUsuario:nomUsuario,
					idSubasta:idSubasta};
		fc.llamadaWS(datos,CONFIGURACION.get('REALIZAR_PAGO'),'POST', false, verificarPago, falloLlamada);
		return false;
	});	
	$("#cancel").click(function(){
		window.location.replace('index.php');
	});
});