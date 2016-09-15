jQuery(document).ready(function($){
	$('#abap_intro_analysis_selector').click(function(event){
		event.preventDefault();
		$('#aa_content').abapChangeScreen('html/analysis_selector.html','js/client/analysis_selector-min.js');
	});
	$("#abap_intro_button").click(function(event){
		event.preventDefault();
		var	aa=	$.confirm({
			animation			:'bottom',
			closeAnimation		:'bottom',
			closeIcon			:true,
			closeIconClass		:'fa fa-close',
			confirmButtonClass	:'btn-info',
			cancelButtonClass	:'btn-danger',
			title				:'Crear nuevo an&aacute;lisis',
			content				:'<p>Debes introducir el identificador (SID) del sistema que vas a analizar:</p>'+
								'<input autofocus type="text" id="sid" placeholder="SID" class="form-control">',
			confirmButton		:'Crear análisis',
			cancelButton		:'Cancelar',
			confirm: function(){
				var sid	= aa.$content.find('input').val();
				window.console.log("Validando SID");
				if(sid.trim() === ''){
					return false;
				}
				var	data={
					'action':'abap_ajax_create_analysis',
					'sid':	sid,
				};
				window.console.log("Creando analisis");
				$.post(ajaxurl, data, function(response) {
					window.console.log("Respuesta AJAX");
					window.console.log(JSON.stringify(response));
					if(response.status	===	"OK"){
						aa.close();
						window.console.log("respuesta OK - ID: "+response.analysisId);
						$("#aa_container").data('abap-analysis-id',response.analysisId);
//						$("#aa_container")['abap-analysis-id']=response.id;
						$('#aa_content').abapChangeScreen('html/editor.html','js/client/editor-min.js');
						$.alert({
							title:"¡Felicitaciones!",
							content:"<p>Has creado el an&aacute;lisis <strong>"+sid+"</strong> exit&oacute;samente!</p><p>Ahora vamos a agregar informaci&oacute;n</p>",
							confirmButtonClass:	'btn-success',
						});
					}
				},'json');
				return	false;
			}
		});
	});
});