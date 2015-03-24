var FuncionesComunes = function(){
	/**
	*	Hace una llamada al web service con el objeto y el uri que se le proveen
	*/
	this.llamadaWS = function(jsonObj, uri, tipo, esSincrono, funcionExito, funcionFallo){				
		var result="";		
		$.ajax({
		  type: tipo,
 		  url:  uri, 		  
		  async: esSincrono,	
		  contentType: "application/json;",
		  data: JSON.stringify(jsonObj),
		  success: function(data){funcionExito(data); result=true;},
		  failure: function(data) { alert(data);}
		});	
		return true;
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
    	if (str.match('^[a-zA-Z0-9\-\_]+[a-zA-Z0-9]*$') != null) {
    		return true;
    	} else {
    		return false;
    	}
    }

    this.esEmailValido = function(str) {
    	if (str.match('^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$') != null) {
    		console.log('here---'+str);
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
    		if (ref.noEstaVacio(value)) {
    			r++;
    		}
    	});
    	if (r > 1) {
    		return true;
    	} else {
    		return false;
    	}
    }
};