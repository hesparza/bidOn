var FuncionesComunes = function(){
	/**
	*	Hace una llamada al web service con el objeto y el uri que se le proveen
	*/
	this.llamadaWS = function(jsonObj, uri, tipo, esAsincrono, funcionExito, funcionFallo){				
		var result=false;		
		$.ajax({
		  type: tipo,
 		  url:  uri, 		  
		  async: esAsincrono,	
		  contentType: "application/json;",
		  data: JSON.stringify(jsonObj),
		  success: function(data){funcionExito(data); result=true;},
		  failure: function(data) { alert(data); result=false;}
		});	
		return result;
	};

    this.estaVacio = function(str) {
        if (str === '') {
            return true;
        } else {
            return false;
        }
    }

    this.noEstaVacio = function(str) {
    	if (str !== '') {
    		return true;
    	} else {
    		return false;
    	}
    }

    this.esNombreValido = function(str) {
    	if (str.match('^[a-zA-Z]+[a-zA-Z]*$') != null) {
    		return true;
    	} else {
    		return false;
    	}
    }
    
    this.esNombreUsuarioValido = function(str) {
    	if (str.match('^[a-z]+[a-z]*$') != null) {
    		if(str.length < 6){
    			return false;
    		}
    		return true;
    	} else {
    		return false;
    	}
    }   

    this.esEmailValido = function(str) {
    	if (str.match('^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$') != null) {
    		return true;
    	} else {
    		return false;
    	}
    }    
    
    this.esNumerico = function (str) {
    	if (str.match('^[0-9]+$') != null) {
    		return true;
    	}
    	return false;
    }
    
    this.todosLosCamposLlenos = function(arr) {
    	var r = 0;
    	var ref = this;
    	$.each(arr, function(index, value) {
    		if (ref.estaVacio(value)) {
    			r++;
    		}
    	});
    	if (r >= 1) {
    		return false;
    	} else {
    		return true;
    	}
    }
    
    this.borrarHtml = function(id) {
    	$("#" + id).empty();
    }
    
    this.insertarHeaderHtml = function(id,tamano,texto) {
    	$("#" + id).append("<h" + tamano + ">" + texto + "</h" +tamano+ ">");
    }
};