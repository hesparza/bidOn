var FuncionesComunes = function(){
	/**
	*	Hace una llamada al web service con el objeto y el uri que se le proveen
	*/
	this.llamadaWS = function (jsonObj, uri, tipo, esSincrono, funcionExito, funcionFallo){				
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
	
	this.hayCamposVacios = function(arr) {
		var r = false;
		arr.forEach(function(value) {
			if (isEmpty(value)) {
				r = true;
			}
		});
		return r;
	}
}