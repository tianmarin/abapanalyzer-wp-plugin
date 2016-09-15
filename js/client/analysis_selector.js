jQuery(document).ready(function($){
//------------------------------------------------------------------------------
/*
* Definición de Variables globales
*/
//var analysisId;						//Id del análisis a editar
//------------------------------------------------------------------------------
/**
* Genera el cambio de visualización sobre el análisis  
*
* @author Cristian Marin
*/
var abapSelectorRefresh=function(){
	var data = new FormData();
	data.append('action', 'abap_ajax_get_analysis_ids');
	data.append('user', 1);
	$.ajax({
		url: ajaxurl,
		type: 'POST',
		data: data,
		cache: false,
		dataType: 'json',
  		processData: false,
		contentType: false,
 		success: function(response){
//			window.console.log(JSON.stringify(response));
			if(response.status === 'OK'){
				$('.analysis_list_own').html(response.ownes);
				$('.analysis_list_noown').html(response.noownes);
				$('.analysis_list li').click(abapSelectAnalysis);
				return true;
			}else{
				$.alert("Error al obtener informaci&oacute;n.<br/>&iquest;Has iniciado sesi&oacute;n?");
				window.console.log('ERROR: Obtención de análisis ID');
				window.console.log("abap_selector_refresh (response):"+JSON.stringify(response));
				return false;
			}
		},
		error: function(jqXHR, textStatus, errorThrown){
			window.console.log('ERROR: '+jqXHR+' - '+textStatus+' - '+errorThrown);
		}
	});
};
//------------------------------------------------------------------------------
/**
* Genera el cambio de visualización sobre el análisis  
*
* @author Cristian Marin
*/
var abapSelectAnalysis=function(event){
	event.preventDefault();
	var analysis=$(this).data('analysis-id');
	if(analysis === 0 || (typeof analysis) !== 'number'){
		window.console.log("Error al obtener id de analisis");
		return false;
	}
	$("#aa_container").data('abap-analysis-id',analysis);
	$('#aa_content').abapChangeScreen('html/editor.html','js/client/editor-min.js');	
};
//------------------------------------------------------------------------------
/*
* Definición de Eventos
*/
abapSelectorRefresh	();

/*
* Definición de Variables pajeras
*/
});