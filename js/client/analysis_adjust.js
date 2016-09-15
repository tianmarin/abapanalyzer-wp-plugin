jQuery(document).ready(function($){
//------------------------------------------------------------------------------
/*
* Definición de Variables globales
*/
var chart;							//Instancia de gráfico
var maxGraph;						//Maximum Graph [Linear]
var minGraph;						//Minimum Graph [Linear]
var avgGraph;						//Average Graph [Linear]
//------------------------------------------------------------------------------
/**
* Obtiene via AJAX la información del elemento a graficar.
* Dependiendo del activo, se solicita grafica el gráfico correspondiente
*
* @author Cristian Marin
*/
function abapAssetGetData(element){
	var data = new FormData();
	data.append('action', 'abap_get_chart_data_provider');
	data.append('analysis', $("#aa_container").data('abap-analysis-id'));
	data.append('data_source', element.data('data-source'));
	data.append('asset',  element.data('asset'));
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
				chart.addTitle(response.analysisSid+" | "+response.assetName, 15, '', 1, false);
				switch(response.chartType){
					case 'linearMax':
						chart.dataProvider = response.message;
						maxGraph.title="Max "+response.assetName;
						chart.addGraph(maxGraph);
						avgGraph.title="Avg "+response.assetName;
						chart.addGraph(avgGraph);
						chart.validateData();
						chart.write("adjust_chart");
//						window.console.log(chart.titles[0].text);
						break;
					case 'linearMin':
						chart.dataProvider = response.message;
						minGraph.title="Min "+response.assetName;
						chart.addGraph(minGraph);
						avgGraph.title="Avg "+response.assetName;
						chart.addGraph(avgGraph);
						chart.validateData();
						chart.write("adjust_chart");
//						window.console.log(chart.titles[0].text);
						break;
					default:
				}
				return true;
			}else{
				$.alert("Error al obtener informaci&oacute;n.<br/>&iquest;Has iniciado sesi&oacute;n?");
				window.console.log("ERROR:"+JSON.stringify(response));
				return false;
			}
		},
		error: function(jqXHR, textStatus, errorThrown){
			window.console.log('ERROR: '+jqXHR+' - '+textStatus+' - '+errorThrown);
		}	
	});
}
	function abapAlert(event){
//		$.alert("yapo");
//		window.console.log(event);
		if(event.type === 'hideItem'){
			window.console.log("Ocultar "+event.dataItem.id);
		}
		if(event.type === 'showItem'){
			window.console.log("Mostrar "+event.dataItem.id);
		}
	}
var abapChartSetup=function(){
	$("span[data-trigger='analysis_adjust_asset']").click(function(event){
		event.preventDefault();
		window.console.log("Obtener información de :"+$(this).data('asset'));
		if( (typeof chart) !== 'undefined'){
			chart.clear();
		}
		chart=null;
		buildAbapAnalyzerChart();
		abapAssetGetData($(this));
	}); 
};
	// create chart
	var buildAbapAnalyzerChart = function() {
	
		// SERIAL CHART
		chart							=new AmCharts.AmSerialChart();
		chart.categoryField				="date";
		chart.marginTop					=0;
		chart.marginBottom				=0;
		chart.marginRight				=0;
		chart.marginLeft				=0;
		chart.thousandsSeparator		=',';
		chart.decimalSeparator			='.';
		chart.dataDateFormat			="YYYY-MM-DD";
//		chart.addClassNames				=true;


		
		// TITLE
//		chart.addTitle("SID | Asset", 15, '', 1, false);

		// GRAPHS
		maxGraph						=new AmCharts.AmGraph();
		maxGraph.id						="max";
		maxGraph.bullet					="round";
		maxGraph.bulletSize				=3;
		maxGraph.lineColor				="#63A0D7";
		maxGraph.lineThickness			=1;
		maxGraph.type					="smoothedLine";
		maxGraph.bezierX				=6;
		maxGraph.bezierY				=12;
		maxGraph.valueField				="max";
		maxGraph.fillColors				=["#63A0D7"];
//		maxGraph.fillColors				=["#63A0D7","#EE8335"];
		maxGraph.fillAlphas				=0.1;
		maxGraph.balloonText			="[[category]]<br><b><span style='font-size:14px;'>[[value]]</span></b>";
//		maxGraph.markerType				="circle";

		minGraph						=new AmCharts.AmGraph();
		minGraph.id						="min";
		minGraph.bullet					="round";
		minGraph.bulletSize				=3;
		minGraph.lineColor				="#63A0D7";
		minGraph.lineThickness			=1;
		minGraph.type					="smoothedLine";
		minGraph.bezierX				=6;
		minGraph.bezierY				=12;
		minGraph.valueField				="min";
		minGraph.fillColors				=["#63A0D7"];
//		maxGraph.fillColors				=["#63A0D7","#EE8335"];
		minGraph.fillAlphas				=0.1;
		minGraph.balloonText			="[[category]]<br><b><span style='font-size:14px;'>[[value]]</span></b>";
//		maxGraph.markerType				="circle";
	
		avgGraph						=new AmCharts.AmGraph();
		avgGraph.id						="avg";
		avgGraph.bullet					="round";
		avgGraph.bulletSize				=3;
		avgGraph.lineColor				="#EE8335";
		avgGraph.lineThickness			=1;
		avgGraph.type					="smoothedLine";
		avgGraph.bezierX				=6;
		avgGraph.bezierY				=12;
		avgGraph.valueField				="avg";
		avgGraph.fillColors				=["#EE8335"];
//		avgGraph.visibleInLegend		=false;
//		avgGraph.fillColors				=["#63A0D7","#EE8335"];
		avgGraph.fillAlphas				=0.1;
		avgGraph.balloonText			="[[category]]<br><b><span style='font-size:14px;'>[[value]]</span></b>";
//		avgGraph.markerType				="circle";
	
		// CATEGORY AXIS
		chart.categoryAxis.parseDates = true;
		
		// LEGEND
		var legend						=new AmCharts.AmLegend();
		legend.enabled					=true;
		legend.divId					="selector_chart";
		legend.fontSize					=8;
		legend.useGraphSettings			= true;
		legend.align					= 'center';
		legend.addListener("showItem", abapAlert);
		legend.addListener("hideItem", abapAlert);
		chart.addLegend(legend);
	
		// WRITE
//		chart.write("adjust_chart");
//		var firstOne=$(document.createElement('div')).data('data-source','sdfmon').data('asset','act_wps');
//		abapAssetGetData(firstOne);
	};


	if (AmCharts.isReady) {
		abapChartSetup();
	} else {
		AmCharts.ready(abapChartSetup);
	}

//------------------------------------------------------------------------------
/*
* Definición de Eventos
*/


 /*	var chart = AmCharts.makeChart("adjust_chart", {
		"version":"1.0",
		"type": "serial",
		"theme": "light",
		"marginTop":0,
		"marginRight": 0,
		"marginBottom":0,
		"marginLeft":0,
		"usePrefixes":true,
		"thousandsSeparator":',',
		"decimalSeparator":'.',
		"angle":25,
		"depth3D":5,
		"titles": [
			{
				"alpha":1,
				"bold":false,
				"color":'',
				"id":'',
				"size": 15,
				"tabIndex":'',
				"text": "ERP | Active WPs",
			}
		],
		"graphs": [	
			{
				"id":"avg",
				"balloonText": "[[category]]<br><b><span style='font-size:14px;'>[[value]]</span></b>",
				"bullet": "round",
				"bulletSize": 4,
				"lineColor": "#63A0D7",
				"lineThickness": 2,
		//		"negativeLineColor": "#637bb6",
				"type": "smoothedLine",
				"title":"Promedio",
				"valueField": "avg",
			},
			{
				"id":"max",
				"balloonText": "[[category]]<br><b><span style='font-size:14px;'>[[value]]</span></b>",
				"bullet": "round",
				"bulletSize": 4,
				"lineColor": "#EE8335",
				"lineThickness": 2,
		//		"negativeLineColor": "#637bb6",
				"type": "smoothedLine",
				"title":"Máximo",
				"valueField": "max"
			}
		],
		"dataDateFormat": "YYYY",
		"categoryField": "year",
		//Eje Horizontal
		"categoryAxis": {
			"axisAlpha": 0,
			"gridAlpha": 0,
			"minPeriod": "YYYY",
			"parseDates": true,
			"fontSize":8,
	  //	  "minorGridAlpha": 0.1,
	//		"minorGridEnabled": true,
		},
		//Eje Vertical
		"valueAxes": [{
			//"axisThickness":3,
			//"dashLength":10,
			"gridAlpha":0.1,
			"axisAlpha": 0,
			"axisColor":'#FF0000',
			//"recalculateToPercents":true,
			//"offset":'25',
			"position": "left",
			"title":"# of WPs",
			"titleBold":false,
			"titleFontSize":10,
			"usePrefixes":true,
			"fontSize":8,
		}],
		"legend": {
			"divId":"selector_chart",
			"fontSize":8,
			"enabled": true,
			"useGraphSettings": true,
			"align":'center',
//			"listeners":[{"event":"hideItem", "method":abapAlert}],
		},
//		"dataProvider":getData(),
/*		"dataProvider": [{
			"year": "1950",
			"avg": -0.307,
			"max": -0.5,
		}, {
			"year": "1951",
			"avg": -0.168,
			"max": -0.5,
		}, {
			"year": "1952",
			"avg": -0.073,
			"max": -0.5,
		}, {
			"year": "1953",
			"avg": -0.027,
			"max": -0.5,
		}, {
			"year": "1954",
			"avg": -0.251,
			"max": -0.5,
		}, {
			"year": "1955",
			"avg": -0.281,
			"max": -0.5,
		}, {
			"year": "1956",
			"avg": -0.348,
			"max": -0.5,
		}, {
			"year": "1957",
			"avg": -0.074,
			"max": -0.5,
		}, {
			"year": "1958",
			"avg": -0.011,
			"max": -0.5,
		}, {
			"year": "1959",
			"avg": -0.074,
			"max": -0.045,
		}, {
			"year": "1960",
			"avg": -0.124,
			"max": -0.5,
		}, {
			"year": "1961",
			"avg": -0.024,
			"max": -0.5,
		}, {
			"year": "1962",
			"avg": -0.022,
			"max": -0.5,
		}, {
			"year": "1963",
			"avg": 0,
			"max": -0.5,
		}, {
			"year": "1964",
			"avg": -0.296,
			"max": -0.5,
		}, {
			"year": "1965",
			"avg": -0.217,
			"max": -0.5,
		}, {
			"year": "1966",
			"avg": -0.147,
			"max": -0.5,
		}, {
			"year": "1967",
			"avg": -0.15,
			"max": -0.5,
		}, {
			"year": "1968",
			"avg": -0.16,
			"max": -0.5,
		}, {
			"year": "1969",
			"avg": -0.011,
			"max": -0.5,
		}, {
			"year": "1970",
			"avg": -0.068,
			"max": -0.5,
		}, {
			"year": "1971",
			"avg": -0.19,
			"max": -0.5,
		}, {
			"year": "1972",
			"avg": -0.056,
			"max": -0.5,
		}, {
			"year": "1973",
			"avg": 0.077,
			"max": -0.5,
		}, {
			"year": "1974",
			"avg": -0.213,
			"max": -0.5,
		}, {
			"year": "1975",
			"avg": -0.17,
			"max": -0.5,
		}, {
			"year": "1976",
			"avg": 0.254,
			"max": -0.5,
		}, {
			"year": "1977",
			"avg": 0.019,
			"max": -0.5,
		}, {
			"year": "1978",
			"avg": -0.063,
			"max": -0.54,
		}, {
			"year": "1979",
			"avg": 0.05,
			"max": -0.5,
		}, {
			"year": "1980",
			"avg": 0.077,
			"max": -0.5,
		}, {
			"year": "1981",
			"avg": 0.47,
			"max": -0.5,
		}],

	});
	chart.ready(function() {
			$.alert("liestoco");
	});
//	chart.addListener("init", function() {
//		chart.addListener("drawn", abapAlert);
		chart.legend.addListener("showItem", abapAlert);
		chart.legend.addListener("hideItem", abapAlert);
//	});
*/
});