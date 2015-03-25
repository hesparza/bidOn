//variables globales
var imagenes = [];
var categoria = "";
var tipoSubasta = "";
var nomUsuario = "";
var fc = new FuncionesComunes();

var registroUsuario = function(obj) {
	if ( typeof obj === 'object') {
		if (obj.datos.hasOwnProperty('error')) {
			alert(obj.datos.mensaje);
			console.log("Mensaje de error: " + obj.datos.mensaje + ", error reportado: " + obj.datos.error);
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

function compatibleParaImagenes() {
	if (window.File && window.FileReader && window.FileList && window.Blob) {
	  return true;
	} else {
	  return false;
	}
}

// getElementById
function $id(id) {
	return document.getElementById(id);
}


// output information
function Output(msg) {
	var m = $id("messages");
	m.innerHTML = msg + m.innerHTML;
}


// file drag hover
function FileDragHover(e) {
	e.stopPropagation();
	e.preventDefault();
	e.target.className = (e.type == "dragover" ? "hover" : "");
}


// file selection
function FileSelectHandler(e) {

	// cancel event and hover styling
	FileDragHover(e);

	// fetch FileList object
	var files = e.target.files || e.dataTransfer.files;

	// process all File objects
	for (var i = 0, f; f = files[i]; i++) {
		ParseFile(f);
		UploadFile(f);
	}

}


// output file information
function ParseFile(file) {

	if (file.type == "image/png" || file.type == "image/jpeg" || file.type == "image/jpg") {
		Output(
			"<p>Informacion de la imagen: <strong>" + file.name +
			"</strong> tipo: <strong>" + file.type +
			"</strong> tamaño: <strong>" + file.size +
			"</strong> bytes</p>"
		);

		// display an image
		if (file.type.indexOf("image") == 0) {
			var reader = new FileReader();
			reader.onload = function(e) {
				Output(
					"<p><strong>" + file.name + ":</strong><br />" +
					'<img src="' + e.target.result + '" /></p>'
				);
			}
			reader.readAsDataURL(file);
		}

		// display text
		if (file.type.indexOf("text") == 0) {
			var reader = new FileReader();
			reader.onload = function(e) {
				Output(
					"<p><strong>" + file.name + ":</strong></p><pre>" +
					e.target.result.replace(/</g, "&lt;").replace(/>/g, "&gt;") +
					"</pre>"
				);
			}
			reader.readAsText(file);
		}
	} else {
		alert("La imagen " +  file.name + " no es valida"
				+ "\nPor favor agregue una imagen de uno de los siguientes formatos:\n- png\n- jpeg\n- jpg");
	}
}


// upload JPEG files
function UploadFile(file) {

	// following line is not necessary: prevents running on SitePoint servers
	if (location.host.indexOf("sitepointstatic") >= 0) return

	var xhr = new XMLHttpRequest();
	if (xhr.upload && (file.type == "image/png" || file.type == "image/jpeg" || file.type == "image/jpg")  && file.size <= $id("MAX_FILE_SIZE").value) {

		// create progress bar
		var o = $id("progress");
		var progress = o.appendChild(document.createElement("p"));
		progress.appendChild(document.createTextNode("Subiendo imagen " + file.name));


		// progress bar
		xhr.upload.addEventListener("progress", function(e) {
			var pc = parseInt(100 - (e.loaded / e.total * 100));
			progress.style.backgroundPosition = pc + "% 0";
		}, false);

		// file received/failed
		xhr.onreadystatechange = function(e) {
			if (xhr.readyState == 4) {
				if (xhr.status == 200) {
					progress.className = "success";
					imagenes.push(file.name);
				} else {
					progress.className = "failure";
				}					
			}
		};

		// start upload
		xhr.open("POST", CONFIGURACION.get('URL_SUBIR_IMAGENES'), true);
		xhr.setRequestHeader("X-FILENAME", file.name);
		xhr.send(file);

	}

}


// initialize
function Init() {

	var fileselect = $id("fileselect"),
		filedrag = $id("filedrag");
//		submitbutton = $id("submitbutton");

	// file select
	fileselect.addEventListener("change", FileSelectHandler, false);

	// is XHR2 available?
	var xhr = new XMLHttpRequest();
	if (xhr.upload) {

		// file drop
		filedrag.addEventListener("dragover", FileDragHover, false);
		filedrag.addEventListener("dragleave", FileDragHover, false);
		filedrag.addEventListener("drop", FileSelectHandler, false);
		filedrag.style.display = "block";

//		// remove submit button
//		submitbutton.style.display = "none";
	}

}

var falloLlamada = function(obj) {
	alert("Error al llamar el servicio web: " + obj);
}

function cargarCategorias() {
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

function cargarTipoSubastas() {
	obj = fc.llamadaWS({},CONFIGURACION.get('TIPO_SUBASTAS'),'GET', false, function(e) {}, falloLlamada);
	if ( typeof obj === 'object') {
		if (obj.datos.hasOwnProperty('error')) {
			alert(obj.datos.mensaje);
			console.log("Mensaje de error: " + obj.datos.mensaje + ", error reportado: " + obj.datos.error);
		} else {
			obj.datos.forEach(function(tipoSubasta) {
			    $("#tipoSubasta").append( $('<option></option>').val(tipoSubasta.id).html(tipoSubasta.nombre));
			});
		}		
	} else {
		alert("Error al cargar los tipos de subasta. Por favor contacte al administrador");
		return false;
	}
}

$(document).on('change', '#categoria', function(e) {
	categoria = this.options[e.target.selectedIndex].value;
});

$(document).on('change', '#tipoSubasta', function(e) {
	tipoSubasta = this.options[e.target.selectedIndex].value;
});

var registroSubasta = function(obj) {
	if ( typeof obj === 'object') {
		if (obj.datos.hasOwnProperty('error')) {
			alert(obj.datos.mensaje);
			console.log("Mensaje de error: " + obj.datos.mensaje + ", error reportado: " + obj.datos.error);
		} else {
			fc.borrarHtml("comentarios");
			fc.insertarHeaderHtml("comentarios", 2, "Tu subasta ha quedado registrada en nuestro sistema, un administrador la va a revisar y aprobar a la brevedad posible.");
			fc.insertarHeaderHtml("comentarios", 2, "Haz click <a href=\"index.php\">aqui</a> para regresar al inicio.");			
		} 		
	} else {
		alert("Error al cargar datos de la nueva subasta. Por favor contacte al administrador");
		return false;
	}	
}

/**
 * Metodo principal
 */
$(document).ready(function() {
	if(!compatibleParaImagenes) {
		alert('Lo sentimos, tu explorador web no es compatible para subir imagenes. \n Por favor intenta con uno diferente.');
	}
	//Cargar variable que contiene el usuario
	nomUsuario = $("#NOM_USUARIO").val();
	if (nomUsuario == "" || nomUsuario === 'undefined') {
		alert("Error fatal: no se encontro un usuario valido en la sesion. Por favor contacte al administrador.");
		return false;
	}
	//Inicializar todo lo necesario para el manejo de imagenes
	Init();
	//Cargar categorias
	cargarCategorias();
	//Cargar tipos de subasta
	cargarTipoSubastas();
	$("#FormaNuevaSubasta").submit(function() {        
		if (!fc.todosLosCamposLlenos([$("#articulo").val(),
		                              $("#precio").val(),
		                              $("#cantidad").val(),
		                              $("#fechainicio").val(),
		                              $("#fechafin").val(),
		                              $("#descripcion").val()]))
		{
			alert('Por favor llene todos los campos');
			return false;
		}
		
		if (!fc.esNombreArticuloValido($("#articulo").val())) {
			alert('El nombre no es correcto, solo puede contener letras.');
			return false;
		}
		if (!fc.esNumericoDecimal($("#precio").val())) {
			alert('El precio es incorrecto, unicamente debe de contener numeros y un punto decimal.');
			return false;
		}		
		if (!fc.esNumerico($("#cantidad").val())) {
			alert('La cantidad es incorrecta, unicamente debe de contener numeros.');
			return false;
		}
		if ((typeof imagenes === 'undefined') || (imagenes.length < 1)) {
			alert('Por favor agrega una o hasta 5 imagenes.');
			return false;			
		}
		if (categoria == "" || categoria == 0) {
			alert('Por favor seleccione una categoria valida.');
			return false;
		}
		if (tipoSubasta == "" || tipoSubasta == 0) {
			alert('Por favor seleccione un tipo de subasta valido.');
			return false;
		}
		console.log('FormaNuevaSubasta esta correcta');
		var datos = {articulo:$("#articulo").val(),
					precio:$("#precio").val(),
					cantidad:$("#cantidad").val(),
					fechainicio:$("#fechainicio").val(),
					fechafin:$("#fechafin").val(),
					descripcion:$("#descripcion").val(),
					imagenes:imagenes,
					categoria:categoria,
					tipoSubasta:tipoSubasta,
					nomUsuario:nomUsuario};
		fc.llamadaWS(datos,CONFIGURACION.get('REGISTRO_NUEVA_SUBASTA'),'POST', false, registroSubasta, falloLlamada);
		return false;
	});	
	$("#cancel").click(function(){
		window.location.replace('index.php');
	});
});