<?php
defined('ABSPATH') or die("No script kiddies please!");
//------------------------------------------------------------------------------
/**
* Process the plugin's shortcode when it is called
* Loads the:
* -Client Styles files (CSS)
* -Client script files (JAVASCRIPT)
* -Client basic structure (HTML)
*
*
* @author Cristian Marin
*/
add_shortcode( "abap_analyzer", 'abap_client_handle_shortcode' );
function abap_client_handle_shortcode($atts){	
//Registered in the template file... the shortcode seems to be executed after  the wp_enqueue_scripts action
	wp_register_style(
		"aa_client_style",
		plugins_url( 'css/client/aa-shortcode.css' , __FILE__ ),
		null,
		"1.0",
		"all"
		);
	wp_enqueue_style("aa_client_style" );
	//-----------------------------------------------------
	wp_register_script(
		'jquery-confirm',
		plugins_url( 'js/jquery-confirm/js/jquery-confirm.js' , __FILE__),
		array('jquery','jquery-ui-core','jquery-ui-datepicker','jquery-ui-progressbar'),
		'1.0'
	);
//	wp_enqueue_script('jquery-confirm');
	//-----------------------------------------------------
	wp_register_script(
		'amcharts',
		plugins_url( 'js/amcharts/amcharts.js' , __FILE__),
		array('jquery'),
		'3.2'
	);
//	wp_enqueue_script('amcharts');
	//-----------------------------------------------------
	wp_register_script(
		'amcharts-serial',
		plugins_url( 'js/amcharts/serial.js' , __FILE__),
		array('amcharts'),
		'3.2'
	);
//	wp_enqueue_script('amcharts-serial');
	//-----------------------------------------------------
	wp_register_script(
		'scrollex',
		plugins_url( 'js/jquery.scrollex-master/jquery.scrollex.min.js' , __FILE__),
		array('jquery'),
		'2'
	);
//	wp_enqueue_script('scrollex');
	//-----------------------------------------------------
	wp_register_script(
		'scrolly',
		plugins_url( 'js/jquery.scrolly.min.js' , __FILE__),
		array('jquery'),
		'1'
	);
//	wp_enqueue_script('scrollex');
	//-----------------------------------------------------
	wp_register_script(
		'bootstrap',
		plugins_url( 'css/bootstrap-master/dist/js/bootstrap.min.js' , __FILE__),
		array('jquery'),
		'1'
	);
//	wp_enqueue_script('scrollex');
	//-----------------------------------------------------
	wp_register_script(
		'file-saver',
		plugins_url( 'js/word/FileSaver.js' , __FILE__),
		array('jquery'),
		'1'
	);
	//-----------------------------------------------------
	wp_register_script(
		'word',
		plugins_url( 'js/word/jquery.wordexport.js' , __FILE__),
		array('jquery','file-saver'),
		'1'
	);
//	wp_enqueue_script('scrollex');
	//-----------------------------------------------------
	wp_register_script(
		'abap_frontend',
		plugins_url( 'js/client/aa_client_script.js' , __FILE__),
		array('jquery-confirm','amcharts-serial','jquery-ui-accordion','jquery-ui-sortable','scrollex','scrolly','bootstrap','word'),
		'1.0'
	);
	wp_enqueue_script('abap_frontend');
	wp_localize_script(
		'abap_frontend',
		'aaServerData',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'aaUrl' => plugins_url('',__FILE__).'/'
		)
	);
}
//------------------------------------------------------------------------------------------------------------------------------------
/**
* Create/Register new ANALYSIS
*
* @since 1.0
* @author Cristian Marin
*/
add_action( 'wp_ajax_abap_ajax_create_analysis', 'abap_ajax_create_analysis' );
function abap_ajax_create_analysis(){
	global $ANALYSIS;
	$response=array();
	$postvs=array(
		'id'			=>	'',
		'creator'		=>	get_current_user_id(),
		'sid'			=>	$_POST['sid'],
		'start_date'	=> '',
		'end_date'	=> '',		
	);
	$id=$ANALYSIS->add_new( $postvs );
	if(is_numeric($id) && $id>0){
		$response['status'] = 'OK';
		$response['id'] = $id;
		$response['analysisId'] = $id;
	}else{
		$response['status'] = 'error';
	}
	echo json_encode($response);
	die();
}
//------------------------------------------------------------------------------------------------------------------------------------
/**
* Get list of analysis
*
* @since 1.0
* @author Cristian Marin
*/
add_action( 'wp_ajax_abap_ajax_get_analysis_info', 'abap_ajax_get_analysis_info' );
function abap_ajax_get_analysis_info(){
	global $ANALYSIS;
	$response=array();
	$analysis=$_POST['analysisId'];
	if($analysis){
		$analysis_data=$ANALYSIS->get_single($analysis);
		$response['analysisData'] = $analysis_data;
		$response['status'] = 'OK';
	}else{
		$response['status'] = 'error';
		$response['message'] = 'No hay ID de Análisis';
	}
	echo json_encode($response);
	die();	
}
//------------------------------------------------------------------------------------------------------------------------------------
/**
* Get the days in which there are already data as in sdfmon files
*
* @since 1.0
* @author Cristian Marin
*/
add_action( 'wp_ajax_abap_ajax_get_analysis_dates', 'abap_ajax_get_analysis_dates' );
function abap_ajax_get_analysis_dates(){
	global $SDFMON;
	$response=array();
	$message=array();
	if(isset($_POST['analysis']) && $_POST['analysis']!=''){
		$sql="SELECT
				date
			FROM  $SDFMON->tbl_name 
			WHERE analysis_id=".$_POST['analysis']."
			GROUP BY date
			ORDER BY date ASC";
		$sdfmon= $SDFMON->get_sql($sql);
		foreach($sdfmon as $sdfentry){
			array_push($message,$sdfentry['date']);
		}
		$response['status'] = 'OK';
		$response['message'] = $message;
	}else{
		$response['status'] = 'error';
	}
	echo json_encode($response);
	die();	
}
//------------------------------------------------------------------------------------------------------------------------------------
/**
* Loads a SDFMON file
*
* @since 1.0
* @author Cristian Marin
*/
add_action( 'wp_ajax_abap_ajax_upload_file', 'abap_ajax_upload_file' );
function abap_ajax_upload_file(){
	global $SDFMON;
	$response=array();
	$c=0;

	$date=$_POST['year']."/".$_POST['month']."/".$_POST['day'];
	$analysis_id=$_POST['analysis_id'];
	if($analysis_id != 0){
		$fp = fopen($_FILES['file']['tmp_name'], 'rb');
		while(  ($line=fgets($fp))  !==  false){
			if($line[0] == "|"){
				$line=str_replace(' ','',$line);
				$info=explode("|",$line);
				if( count($info) > 20){
					unset($postvs);	
					$postvs=array(
						'id'				=>'',
						'analysis_id'		=>	$analysis_id,
						'date'				=>	$date,
						'time'				=>	$info[1],
						'servername'		=>	$info[2],
						'act_wps'			=>	$info[3],
						'dia_wps'			=>	$info[4],
						'rfc_wps'			=>	$info[5],
						'cpu_usr'			=>	$info[6],
						'cpu_sys'			=>	$info[7],
						'cpu_idle'			=>	$info[8],
						'cpu_ava'			=>	$info[9],
						'page_in'			=>	$info[10],
						'page_out'			=>	$info[11],
						'free_mem'			=>	$info[12],
						'em_alloc'			=>	$info[13],
						'em_attach'			=>	$info[14],
						'em_global'			=>	$info[15],
						'heap'				=>	$info[16],
						'priv_mode'			=>	$info[17],
						'page_mem'			=>	$info[18],
						'roll_mem'			=>	$info[19],
						'queue_dia'			=>	$info[20],
						'queue_upd'			=>	$info[21],
						'queue_enq'			=>	$info[22],
						'logins'			=>	$info[23],
						'sessions'			=>	$info[24],
					);
					if(is_numeric($postvs['sessions'])){
						$SDFMON->add_new($postvs);
						$c++;
					}
				}
			}
		}
		$response['status'] = 'OK';
		$response['message'] = 'OK';
		$response['date'] = $date;
		$response['counter'] = $c;
		$response['analysis_id'] = $analysis_id;
	}else{
		$response['error'] = 'No hay análisis';
	}
	echo json_encode($response);
	die();
}
//------------------------------------------------------------------------------------------------------------------------------------
/**
* Get list of analysis
*
* @since 1.0
* @author Cristian Marin
*/
add_action( 'wp_ajax_abap_ajax_get_analysis', 'abap_ajax_get_analysis' );
function abap_ajax_get_analysis(){
	global $ANALYSIS;
	$user=get_current_user_id();
	$response=array();
	$analysisList='';
	if($user){
		$sql="SELECT
				id,creator,creation_date,sid
			FROM  $ANALYSIS->tbl_name 
			WHERE creator=$user
			ORDER BY creation_date DESC";
		$datos= $ANALYSIS->get_sql($sql);
		foreach($datos as $data){
			$analysisList.='<li>';
				$analysisList.='<header>';
					$analysisList.='<span class="title">'.$data['sid'].'</span>';
					$analysisList.='<span class="creation-date">'.$data['creation_date'].'</span>';
				$analysisList.='</header>';
				$analysisList.='<article>';
					$analysisList.='<section>';
						$analysisList.='<ol>';
							$analysisList.='<li>';
								$analysisList.='<span><i class="fa fa-calendar" aria-hidden="true"></i> Fecha de Inicio:</span>';
								$analysisList.='<span>UNDEFINED</span>';
							$analysisList.='</li>';
							$analysisList.='<li>';
								$analysisList.='<span><i class="fa fa-calendar" aria-hidden="true"></i> Fecha de Fin:</span>';
								$analysisList.='<span>UNDEFINED</span>';
							$analysisList.='</li>';
						$analysisList.='</ol>';
						$analysisList.='<ul class="aa-selector-options"data-analysis-id="'.$data['id'].'">';
							$analysisList.='<li data-selector-option="editor">Editar An&aacute;lisis</li>';
							$analysisList.='<li data-selector-option="adjust">Ajustar An&aacute;lisis</li>';
							$analysisList.='<li data-selector-option="report">Obtener reporte</li>';
							$analysisList.='<li class="delete" data-selector-option="delete">Eliminar</li>';
						$analysisList.='</ul>';
					$analysisList.='</section>';
				$analysisList.='</article>';
			$analysisList.='</li>';
		}
		$response['analysisData'] = $analysisList;
		$response['status'] = 'OK';
	}else{
		$response['status'] = 'error';
		$response['message'] = '&iquest;Has iniciado sesi&oacute;n?';
	}
	echo json_encode($response);
	die();	
}
//------------------------------------------------------------------------------------------------------------------------------------
/**
* Get the days in which there are already data as in sdfmon files
*
* @since 1.0
* @author Cristian Marin
*/
add_action( 'wp_ajax_abap_get_chart_data_provider', 'abap_get_chart_data_provider' );
function abap_get_chart_data_provider(){
	global $SDFMON;
	global $ANALYSIS;
	$response=array();
	$data=array();
	$analysis=$_POST['analysis'];
	if(isset($analysis) && $analysis!=''){
		$analysis_detail=$ANALYSIS->get_single($analysis);
		$ASSET=$_POST['asset'];
		switch($ASSET){
			case 'act_wps':
			case 'dia_wps':
			case 'page_in':
			case 'page_out':
			case 'em_alloc':
			case 'em_attach':
			case 'em_global':
			case 'heap':
			case 'priv_mode':
			case 'page_mem':
			case 'roll_mem':
			case 'queue_dia':
			case 'queue_upd':
			case 'queue_enq':
			case 'logins':
			case 'sessions':
			case 'rfc_wps':
			case 'free_mem':
				$chart_type='linear';
				$sql="SELECT
						time.date as date,
						MIN(time.sum) as min,
						MAX(time.sum) as max,
						MAX(time.sum)/.8 as proj,
						AVG(time.avg) as avg,
						p95 as p95
					FROM (
						SELECT
							SUM($ASSET) as sum,
							AVG($ASSET) as avg,
							CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(
								GROUP_CONCAT($ASSET ORDER BY $ASSET SEPARATOR ','),
								',', 95/100 * COUNT(*) + 1), ',', -1) AS DECIMAL) as 'p95',
							date as date
						FROM $SDFMON->tbl_name
						WHERE analysis_id=$analysis
						GROUP BY date,time
					) AS time
					GROUP BY time.date";
				break;
			default:
		}
		$sdfmon= $SDFMON->get_sql($sql);
		foreach($sdfmon as $sdfentry){
			array_push($data,
				array(
					"date"	=>	$sdfentry['date'],
					"proj"	=>	round($sdfentry['proj'],0),
					"max"	=>	round($sdfentry['max'],0),
					"min"	=>	round($sdfentry['min'],0),
					"avg"	=>	round($sdfentry['avg'],0),
				)
			);			
		}
		$asset=array(
			'act_wps'	=>	"Active WPs",
			'dia_wps'	=>	"Dialog WPs",
			'rfc_wps'	=>	"Free RFC WPs",
		);
		$response['config']=array(
			'max'	=> array(
				'show'	=> true,
			),
			'min'	=> array(
				'show'	=> false,
			),
			'avg'	=> array(
				'show'	=> true,
			),
		);
		$response['status'] = 'OK';
		$response['chartType'] = $chart_type;
		$response['assetName'] = $asset[$ASSET];
		$response['analysisSid'] = $analysis_detail['sid'];
		$response['data'] = $data;
	}else{
		$response['status'] = 'error';
		$response['message'] = 'No hay analysis';
	}
	echo json_encode($response);
	die();	
}

?>