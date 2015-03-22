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
}