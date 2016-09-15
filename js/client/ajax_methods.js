jQuery(document).ready(function($)	{
	$(".abap_edit_analysis").hide();
	$.fn.extend({
		animateCss:	function	(animationName,finalState)	{
			if	(finalState	===	"on"){
				$(this).show();
			}
			var	animationEnd	=	'webkitAnimationEnd	mozAnimationEnd	MSAnimationEnd	oanimationend	animationend';
			this.addClass('animated	'	+	animationName).one(animationEnd,	function()	{
				if(finalState	===	"off"){
					$(this).hide();
				}
				if	(finalState	===	"on"){
					$(this).show();
				}
				$(this).removeClass('animated	'	+	animationName);
			});
		}
	});
	$(	".abap_intro_button"	).click(function(	event	)	{
		event.preventDefault();
		var	aa=	$.confirm({
			animation:	'bottom',
			closeAnimation:	'bottom',
			closeIcon:	true,
			closeIconClass:	'fa	fa-close',
			confirmButtonClass:	'btn-info',
			cancelButtonClass:	'btn-danger',
			title:	'Crear	nuevo	an&aacute;lisis',
			content:	'<p>Debes	introducir	el	identificador	(SID)	del	sistema	que	vas	a	analizar:</p>'+
			'<input	autofocus	type="text"	id="sid"	placeholder="SID"	class="form-control">',
			confirmButton:	'Crear	análisis',
			cancelButton:	'Cancelar',
			confirm:	function(){
				var	val	=	aa.$content.find('input').val();	//	get	the	input	value.
				if(val.trim()	===	''){	//	validate	it.
						return	false;	//	dont	close	the	dialog.	(and	maybe	show	some	error.)
				}
				aa.$body.addClass('abap_jq_cfm_loading');
				var	data	=	{
					'action':	'abap_ajax_create_analysis',
					'sid':	val,
				};
				$.post(ajaxurl,	data,	function(response)	{
					if(response.message	===	"OK"){
						$(	"#abap_analysis_id"	).val(response.id);
						aa.close();
						aa.$body.removeClass('abap_jq_cfm_loading');
						$(".abap_intro_list").animateCss('fadeOutUpBig',"off");
						$(".abap_edit_analysis").animateCss('fadeInUpBig',"on");
						$.alert({
							title:"¡Felicitaciones!",
							content:"Has	creado	el	an&aacute;lisis	"+val+"	exit&oacute;samente!",
							confirmButtonClass:	'btn-success',
						});
					}
				},	'json');
				return	false;	//	dont	close	the	dialog.
					}
		});
		//$(	"#abap_create_analysis_form	input").attr('disabled',	'disabled');
//		$(this).prop('disabled',	true);
	});
	$(	"#abap_upload_sdfmon_file"	).submit(function(	event	)	{event.preventDefault();});
	$(	"#abap_upload_sdfmon_file	input[type=file]"	).change(function(	event	)	{
		event.preventDefault();
		var	file	=	this.files[0];
		var	name	=	file.name;
		var	size	=	file.size;
		var	type	=	file.type;
		
		var	filename	=	$(this).val().replace(/.*(\/|\\)/,	'');
		if	(filename.trim()	===	''){
			$.alert("No has seleccionado un archivo.")
			window.console.log("No	file	selected");
			return	false;
		}

		if($(	"#abap_analysis_id"	).val()	===	'0'){
			window.console.log("No	abap	analysis	ID	found");
			$.alert("No hay un an&aacute;lisis relacionado.");
			return	false;
		}
		window.console.log(name+"	"+type+"	"+size);
		window.console.log("Please	Confirm	date");
		$.confirm({
			animation:	'bottom',
			closeAnimation:	'bottom',
			closeIcon:	true,
			closeIconClass:	'fa	fa-close',
			confirmButtonClass:	'btn-info',
			cancelButtonClass:	'btn-danger',
			title:	'Indica	la	fecha',
			content:	'<p>Desgraciadamente,	los	archivos	de	salida	del	SnapshotMonitoring,	no	indican	la	fecha	a	la	cual	pertenece	el	detalle<p><p>Debes	introducir	la	fecha	que	corresponde	al	archivo	"'+filename+'":</p>'+year+month+day,
			confirmButton:	'Cargar	archivo',
			cancelButton:	'Cancelar',
			confirm:	function(){
				var	input_y	=	this.$content.find('#year').val();
				var	input_m	=	this.$content.find('#month').val();
				var	input_d	=	this.$content.find('#day').val();
				if(input_y	===	'0'	||	input_m	===	'0'	||	input_d	===	'0'){
						window.console.log("Pon	bien la fecha");
						return	false;
				}
				var data = new FormData();
				data.append('action', 'abap_ajax_upload_sdfmon_file');
				data.append('analysis', $("#abap_analysis_id").val());
				data.append('year', input_y);
				data.append('month', input_m);
				data.append('day', input_d);
				data.append('sdfmon_file', file);

				window.console.log("Comienza la	llamada Post");
				$(".upload").addClass('abap_jq_cfm_loading');
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: data,
					cache: false,
					dataType: 'json',
					processData: false,
					contentType: false,
					success: function(response, textStatus, jqXHR){
						window.console.log(JSON.stringify(response));
						get_analysis_sdfmon_files($("#abap_analysis_id").val());
						$(".upload").removeClass('abap_jq_cfm_loading');
						if(typeof response.error === 'undefined'){
							// Success so call function to process the form
							window.console.log('OK');
//							submitForm(event, data);
						}else{
							// Handle errors here
							window.console.log('ERRORS: ' + response.error);
						}
					},
					error: function(jqXHR, textStatus, errorThrown){
						// Handle errors here
						window.console.log('ERRORS: ' + jqXHR + textStatus + errorThrown);
						// STOP LOADING SPINNER
					}
				});
			}
		});
	});
	var	year='<select	id="year">'+
				'<option	value="0">Año</option>'+
				'<option	value="2010">2010</option>'+
				'<option	value="2011">2011</option>'+
				'<option	value="2012">2012</option>'+
				'<option	value="2013">2013</option>'+
				'<option	value="2014">2014</option>'+
				'<option	value="2015">2015</option>'+
				'<option	value="2016">2016</option>'+
				'<option	value="2017">2017</option>'+
				'<option	value="2018">2018</option>'+
				'<option	value="2019">2019</option>'+
				'<option	value="2020">2020</option>'+
			'</select>';
	var	month='<select	id="month">'+
				'<option	value="0">Mes</option>'+
				'<option	value="1">Enero</option>'+
				'<option	value="2">Febrero</option>'+
				'<option	value="3">Marzo</option>'+
				'<option	value="4">Abril</option>'+
				'<option	value="5">Mayo</option>'+
				'<option	value="6">Junio</option>'+
				'<option	value="7">Julio</option>'+
				'<option	value="8">Agosto</option>'+
				'<option	value="9">Septiembre</option>'+
				'<option	value="10">Octubre</option>'+
				'<option	value="11>Noviembre</option>'+
				'<option	value="12>Diciembre</option>'+
			'</select>';
	var	day='<select	id="day">'+
				'<option	value="0">D&iacute;a</option>'+
				'<option	value="1">01</option>'+
				'<option	value="2">02</option>'+
				'<option	value="3">03</option>'+
				'<option	value="4">04</option>'+
				'<option	value="5">05</option>'+
				'<option	value="6">06</option>'+
				'<option	value="7">07</option>'+
				'<option	value="8">08</option>'+
				'<option	value="9">09</option>'+
				'<option	value="10">10</option>'+
				'<option	value="11">11</option>'+
				'<option	value="12">12</option>'+
				'<option	value="13">13</option>'+
				'<option	value="14">14</option>'+
				'<option	value="15">15</option>'+
				'<option	value="16">16</option>'+
				'<option	value="17">17</option>'+
				'<option	value="18">18</option>'+
				'<option	value="19">19</option>'+
				'<option	value="20">20</option>'+
				'<option	value="21">21</option>'+
				'<option	value="22">22</option>'+
				'<option	value="23">23</option>'+
				'<option	value="24">24</option>'+
				'<option	value="25">25</option>'+
				'<option	value="26">26</option>'+
				'<option	value="27">27</option>'+
				'<option	value="28">28</option>'+
				'<option	value="29">29</option>'+
				'<option	value="30">30</option>'+
				'<option	value="31">31</option>'+
			'</select>';
	function get_analysis_sdfmon_files(analysis){
		window.console.log('Comenzando el refresh');
		var	data	=	{
			'action'	:'abap_ajax_get_analysis_sdfmon_files',
			'analysis'	:analysis,
		};
		$.post(ajaxurl,	data,	function(response)	{
			$("#abap_sdfmon_files").addClass('abap_jq_cfm_loading');
			window.console.log('llegó respuesta del refresh');
//			window.console.log(JSON.stringify(response));
			$("#abap_sdfmon_files").removeClass('abap_jq_cfm_loading');
			$("#abap_sdfmon_files").html(response.message);
		},	'json');
	}
	get_analysis_sdfmon_files(0);
});

