jQuery(document).ready(function($){
//------------------------------------------------------------------------------
/*
* Definición de Variables globales
*/
var analysisId;						//Id del análisis a editar
var sdfmon_dates=[];				//fechas existentes del SnapshotMonitoring
var sdfmon_lastday=new Date();		//última fecha existente
var sdfmon_input_y;					//campo año para registro de archivo
var sdfmon_input_m;					//campo mes para registro de archivo
var sdfmon_input_d;					//campo día para registro de archivo
//------------------------------------------------------------------------------
/**
* Obtiene las fechas vía AJAX que ya tienen información del SnapshotMonitoring.
* Las fechas son almacenadas en la variable global 'sdfmon_dates'
*
* @author Cristian Marin
*/
var abap_get_sdfmon_dates =function(analysis,container){
	$('#upload_progress').hide();
	if(analysis === 0){
		window.console.log('no hay análisis');
		return false;
	}else{
		window.console.log('buscando fechas del análisis: '+analysis);
	}
	var container_cal=container;
	var	data={
		'action'	:'abap_ajax_get_analysis_sdfmon_dates',
		'analysis'	:analysis,
	};
	return $.post(ajaxurl,data,function(response){
		if(response.status === 'OK'){
			//window.console.log(response.message);
			$.each(response.message, function( i, val ) {
				var parts = val.split('-');
				var each_date=new Date(parts[0],parts[1]-1,parts[2]);
				sdfmon_dates[each_date]=true;
				sdfmon_lastday=each_date;
			});
			window.console.log("Fechas identificadas análisis ("+analysis+"): ");
			window.console.log(sdfmon_dates);
			window.console.log('last day: '+sdfmon_lastday);
			create_sdfmon_calendar(container_cal);
			return true;
		}else{
			window.console.log('ERROR: Obtención de fechas de sdfmon');
			window.console.log("abap_get_sdfmon_dates (response):"+JSON.stringify(response));
			return false;
		}
	},	'json');
};
//------------------------------------------------------------------------------
/**
* Instancia la variable 'analysisId' y valida que no sea nulo
*
* @author Cristian Marin
*/
var abap_check_analysis_id=function(){
	analysisId=$("#aa_container").data('abap-analysis-id');
	if( analysisId === 0){
		window.console.log("No Analysis ID found");
		return	false;
	}
	window.console.log("Analysis ID :"+analysisId);
	return analysisId;
};
//------------------------------------------------------------------------------
/**
* Realiza las tareas de actualización de mensajes de status de la barra de status
* Para su ejecución requiere:
*   status		: Tipo de mensaje a mostrar: 'start', 'loading', 'finished', 'error'
*   text		: Mensaje de texto a desplegar
*   percentage	: Porcentaje de carga
* @author Cristian Marin
*/
var abap_status_bar_update=function(status,text,percentage){
	switch(status){
		case 'start':
			$('.editor_text_status').html(text).fadeIn(500);
			$('#upload_progress').fadeIn(500);
			break;
		case 'loading':
			if((typeof percentage) === 'number'){
				$('#upload_progress').progressbar({value: percentage,max:100});
				text=text+" ("+percentage+"%)";
			}
			$('.editor_text_status').html(text);
			//progress bar at % percentage
			break;
		case 'finished':
			$('.editor_text_status').html(text).delay(5000).fadeOut(500);
			$('#upload_progress').delay(5000).fadeOut(500);
			break;
		case 'error':
			$('.editor_text_status').html(text).fadeIn(500).delay(5000).fadeOut(500);
			break;
		default:
	}
	return true;
};
//------------------------------------------------------------------------------
/**
* Realiza el proceso de carga vía AJAX de los registros del SnapshotMonitoring.
* Para su ejecición requiere
*   input_y	: año de registro
*   input_m	: mes de registro
*   input_d	: dia de registro
*   file	: manejador de archivo de datos
*
* @author Cristian Marin
*/
var abap_upload_sdfomn_file =function (input_y,input_m,input_d,file){
	//validar
	if(!abap_check_analysis_id){
		return false;
	}
	var data = new FormData();
	data.append('action', 'abap_ajax_upload_sdfmon_file');
	data.append('analysis', analysisId);
	data.append('year', input_y);
	data.append('month', input_m);
	data.append('day', input_d);
	data.append('sdfmon_file', file);
	window.console.log(analysisId);
	$.ajax({
		url: ajaxurl,
		type: 'POST',
		data: data,
		cache: false,
		dataType: 'json',
		processData: false,
		contentType: false,
		beforeSend: function () {
			abap_status_bar_update('start','Comienza la carga del archivo '+file.name,0);
		},
		xhr: function () {
			var xhr = new window.XMLHttpRequest();
			//Upload progress
			xhr.upload.addEventListener("progress", function(evt){
				if (evt.lengthComputable) {
					var percentComplete = Math.round(evt.loaded / evt.total * 100);
					window.console.log('Porcentaje: '+percentComplete);
					abap_status_bar_update('loading','Cargando '+file.name,percentComplete);
				}
			}, false);
			//success upload
			xhr.upload.addEventListener("load", function(evt){
				if (evt.lengthComputable) {
					abap_status_bar_update('loading','Ahora se está procesando el archivo'+file.name);
				}
			}, false);
			//Download progress
			xhr.addEventListener("progress", function(evt){
				if (evt.lengthComputable) {
					var percentComplete = evt.loaded / evt.total;
					//Do something with download progress
					window.console.log(percentComplete);
				}
			}, false);
			return xhr;
		},
   		success: function(response){
			window.console.log(JSON.stringify(response));
			if(typeof response.error === 'undefined'){
				abap_status_bar_update('finished','Finalizado el procesamiento de '+file.name);
				window.console.log('OK: Carga de '+file.name);
				abap_get_sdfmon_dates(abap_check_analysis_id(),$('#editor_sdfmon_calendar'));
			}else{
				abap_status_bar_update('error','Ha ocurrido un error al procesar '+file.name);
				window.console.log('ERROR: Carga de '+file.name+'('+response.error+')');
			}
		},
		error: function(jqXHR, textStatus, errorThrown){
			abap_status_bar_update('error','Ha ocurrido un error al cargar '+file.name);
			window.console.log('ERROR: Carga de '+file.name+": "+jqXHR+" - "+textStatus+" - "+errorThrown);
		}
	});
};
var abap_load_sdfmon_file=function(event){
	event.preventDefault();
	if(!abap_check_analysis_id){
		$.alert("Error Interno: No hay un an&aacute;lisis relacionado.");
		return false;
	}
	if((typeof this.files[0]) === 'undeined'){
		return false;
	}
	var	file = this.files[0];
	var	name = file.name;
	var	size = file.size;
	var	type = file.type;
//	var filename = $(this).val().replace(/.*(\/|\\)/,	'');
	if (file.name.trim() === ''){
		$.alert("No has seleccionado un archivo.");
		window.console.log("No file selected");
		return	false;
	}
	window.console.log('Archivo a evaluar: '+name+' '+type+' '+size);
	if(type !== 'text/plain'){
		$.alert("Recuerda que debes cargar archivos exportados sin formato");
		sdfmon_input_y=sdfmon_input_m=sdfmon_input_d=0;
		return false;
	}
	if(sdfmon_input_y === 0 || sdfmon_input_m === 0 || sdfmon_input_d ===0){
		$.confirm({
			animation:	'bottom',
			closeAnimation:	'bottom',
			closeIcon:	true,
			closeIconClass:	'fa	fa-close',
			confirmButtonClass:	'btn-info',
			cancelButtonClass:	'btn-danger',
			title:	'Indica	la	fecha',
			content:	'<p>Desgraciadamente, los archivos de salida del SnapshotMonitoring, no indican la fecha a la cual pertenece el detalle<p><p>Debes introducir la fecha que corresponde al archivo "'+file.name+'":</p>'+year+month+day,
			confirmButton:	'Cargar	archivo',
			cancelButton:	'Cancelar',
			confirm:	function(){
				var input_y	=	this.$content.find('#year').val();
				var input_m	=	this.$content.find('#month').val();
				var input_d	=	this.$content.find('#day').val();
				if(input_y === '0' || input_m === '0' || input_d === '0'){
					$.alert("Pon bien la fecha");
					window.console.log("Pon bien la fecha");
					return false;
				}
				if (abap_upload_sdfomn_file(input_y,input_m,input_d,file)){
					//exito
				}else{
					//error
				}
			}
		});
	}else{
		if (abap_upload_sdfomn_file(sdfmon_input_y,sdfmon_input_m,sdfmon_input_d,file)){
			//exito
		}else{
			//error
		}
	}
};
//------------------------------------------------------------------------------
/**
* Crea el calendario del 
*
* @author Cristian Marin
*/
var create_sdfmon_calendar=function(container){
	window.console.log(container);
	container.datepicker({
		inline: true,
		defaultDate:sdfmon_lastday,
		beforeShowDay:function(date) {
			if (sdfmon_dates[date]) {
				return [false,'aa_sdfmon','Análisis registrado'];
			}
				return [true, '', ''];
		},
		firstDay: 1,
		showOtherMonths: true,
		nextText: '<i class="fa fa-chevron-right" aria-hidden="true"></i>',
		prevText: '<i class="fa fa-chevron-left" aria-hidden="true"></i>',
		dateFormat: 'yy/m/d',
		yearRange: "2016:2018",
		dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
		onSelect: function(date){
			//$.alert("Elegiste la fecha "+date);
			abap_sdfmon_calendar_click(date);
		},
/*		onChangeMonthYear:function(year,month){
			$.alert("Visualizando el mes "+month+" del a&ntilde;o "+year);			
		},
*/    });
	container.datepicker('show');
	container.datepicker('refresh');
	return true;
	};
//------------------------------------------------------------------------------
/**
* Prepera los comandos para días selectables 
*
* @author Cristian Marin
*/
var abap_sdfmon_calendar_click=function(date){
	if(sdfmon_dates[date]){
		$.alert("Ese dia ya tiene datos");
		return false;
	}
	window.console.log('fecha del calendario. '+date);
	var parts = date.split('/');
	sdfmon_input_y=parts[0];
	sdfmon_input_m=parts[1];
	sdfmon_input_d=parts[2];
	window.console.log('fechas para cargar: '+sdfmon_input_y+'/'+sdfmon_input_m+'/'+sdfmon_input_d);
	$( "#abap_upload_sdfmon_file input[type=file]" ).click();
	window.console.log("click");
	return true;
};
//------------------------------------------------------------------------------
/*
* Definición de Eventos
*/
$( "#abap_upload_sdfmon_file" ).submit(function( event ) {event.preventDefault();});
$( "#abap_upload_sdfmon_file input[type=file]" ).change(abap_load_sdfmon_file);
abap_get_sdfmon_dates(abap_check_analysis_id(),$('#editor_sdfmon_calendar'));
/*
* Definición de Variables pajeras
*/
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
});