<?php
defined('ABSPATH') or die("No script kiddies please!");
//------------------------------------------------------------------------------------------------------------------------------------
/**
* 
*/
add_action('wp_head', 'abap_ajaxurl');
function abap_ajaxurl() {

   echo '<script type="text/javascript">
           var ajaxurl = "' . admin_url('admin-ajax.php') . '";
           var aa_url ="'.plugins_url('',__FILE__).'/"
         </script>';
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**
* Process the plugin's shortcode when it is called and return the results
*
* @author Cristian Marin
*/
add_shortcode( "abap_analyzer", 'abap_client_handle_shortcode' );
function abap_client_handle_shortcode($atts){	
	wp_register_style(
		"abap_client_style",
		plugins_url( 'css/client/shortcode.css' , __FILE__ ),
		null,
		"1.0",
		"all"
		);
	wp_enqueue_style( "abap_client_style" );
//---------------------------------------------------------------------------
	wp_register_script(
		'jquery-confirm',
		plugins_url( 'js/jquery-confirm/js/jquery-confirm.js' , __FILE__),
		array('jquery','jquery-ui-core','jquery-ui-datepicker','jquery-ui-progressbar'),
		'1.0'
	);
	wp_enqueue_script(	'jquery-confirm');
//---------------------------------------------------------------------------
	wp_register_script(
		'amcharts',
		plugins_url( 'js/amcharts/amcharts.js' , __FILE__),
		array('jquery'),
		'3.2'
	);
	wp_enqueue_script(	'amcharts');
//---------------------------------------------------------------------------
	wp_register_script(
		'amcharts-serial',
		plugins_url( 'js/amcharts/serial.js' , __FILE__),
		array('amcharts'),
		'3.2'
	);
	wp_enqueue_script(	'amcharts-serial');
//---------------------------------------------------------------------------
	wp_register_script(
		'abap_frontend',
		plugins_url( 'js/client/frontend-min.js' , __FILE__),
		array('jquery','jquery-confirm'),
		'1.0'
	);
	wp_enqueue_script(	'abap_frontend');
//---------------------------------------------------------------------------
	return abap_analizer_create_client_container();
}
//------------------------------------------------------------------------------------------------------------------------------------
function abap_analizer_create_client_container(){
	$output='';
	$output.='<div id="aa_container" data-abap-analysis-id="10">';
		$output.='<div id="aa_loading"></div>';
		$output.='<div id="aa_content"></div>';
	$output.='</div>';
	return $output;
}

//------------------------------------------------------------------------------------------------------------------------------------
function abap_load_intro(){
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
add_action( 'wp_ajax_abap_ajax_get_analysis_ids', 'abap_ajax_get_analysis_ids' );
function abap_ajax_get_analysis_ids(){
	global $ANALYSIS;
	$user=$_POST['user'];
	$response=array();
	$propios='';
	$nopropios='';
	if($user){
		$sql="SELECT
				id,creator,creation_date,sid
			FROM  $ANALYSIS->tbl_name 
			WHERE creator=$user
			ORDER BY creation_date DESC";
		$datos= $ANALYSIS->get_sql($sql);
		foreach($datos as $data){
			$propios.='<li data-analysis-id="'.$data['id'].'">';
				$propios.='<span class="analysis_sid">'.$data['sid'].'</span>';
				$propios.='<span class="analysis_detail">'.$data['creation_date'].'</span>';
			$propios.='</li>';
		}
		$response['ownes'] = $propios;
		$sql="SELECT
				id,creator,creation_date,sid
			FROM  $ANALYSIS->tbl_name 
			WHERE creator!=$user
			ORDER BY creation_date DESC";
		$datos= $ANALYSIS->get_sql($sql);
		foreach($datos as $data){
			$nopropios.='<li data-analysis-id="'.$data['id'].'">';
				$nopropios.='<span class="analysis_sid">'.$data['sid'].'</span>';
				$nopropios.='<span class="analysis_detail">'.$data['creation_date'].'</span>';
			$nopropios.='</li>';
		}
		$response['noownes'] = $nopropios;
		$response['status'] = 'OK';
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
add_action( 'wp_ajax_abap_ajax_upload_sdfmon_file', 'abap_ajax_upload_sdfmon_file' );
function abap_ajax_upload_sdfmon_file(){
	global $SDFMON;
	$response=array();
	$c=0;

	$date=$_POST['year']."/".$_POST['month']."/".$_POST['day'];
	$analysis=$_POST['analysis'];
	if($analysis != 0){
		$fp = fopen($_FILES['sdfmon_file']['tmp_name'], 'rb');
		while(  ($line=fgets($fp))  !==  false){
			if($line[0] == "|"){
				$line=str_replace(' ','',$line);
				$info=explode("|",$line);
				if( count($info) > 20){
					unset($postvs);	
					$postvs=array(
						'id'				=>'',
						'analysis_id'		=>	$analysis,
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
		$response['analysis'] = $analysis;
	}else{
		$response['error'] = 'No hay análisis';
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
add_action( 'wp_ajax_abap_ajax_get_analysis_sdfmon_dates', 'abap_ajax_get_analysis_sdfmon_dates' );
function abap_ajax_get_analysis_sdfmon_dates(){
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
	$message=array();
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
				$chart_type='linearMax';
				$sql="SELECT
						time.date as date,
						MAX(time.sum) as max,
						AVG(time.avg) as avg
					FROM (
						SELECT
							SUM($ASSET) as sum,
							AVG($ASSET) as avg,
							date as date
						FROM $SDFMON->tbl_name
						WHERE analysis_id=$analysis
						GROUP BY date,time
					) AS time
					GROUP BY time.date";
				break;
			case 'rfc_wps':
			case 'free_mem':
				$chart_type='linearMin';
				$sql="SELECT
						time.date as date,
						MIN(time.sum) as min,
						AVG(time.avg) as avg
					FROM (
						SELECT
							SUM($ASSET) as sum,
							AVG($ASSET) as avg,
							date as date
						FROM $SDFMON->tbl_name
						WHERE analysis_id=$analysis
						GROUP BY date,time
					) AS time
					GROUP BY time.date";
				$sql="SELECT
						time.date as date,
						MIN(time.max) as min,
						AVG(time.avg) as avg
					FROM (
						SELECT
							SUM($ASSET) as max,
							AVG($ASSET) as avg,
							date as date
						FROM $SDFMON->tbl_name
						WHERE analysis_id=$analysis
						GROUP BY time,date
					) AS time
					GROUP BY time.date";
				break;
		}
		$sdfmon= $SDFMON->get_sql($sql);
		foreach($sdfmon as $sdfentry){
			array_push($message,
				array(
					"date"	=>	$sdfentry['date'],
					"max"	=>	(isset($sdfentry['max'])?round($sdfentry['max'],0):''),
					"min"	=>	(isset($sdfentry['min'])?round($sdfentry['min'],0):''),
					"avg"	=>	round($sdfentry['avg'],0),
				)
			);			
		}
		$asset=array(
			'act_wps'	=>	"Active WPs",
			'dia_wps'	=>	"Dialog WPs",
			'rfc_wps'	=>	"Free RFC WPs",
		);
		$response['status'] = 'OK';
		$response['chartType'] = $chart_type;
		$response['assetName'] = $asset[$ASSET];
		$response['analysisSid'] = $analysis_detail['sid'];
		$response['message'] = $message;
	}else{
		$response['status'] = 'error';
		$response['message'] = 'No hay analysis';
	}
	echo json_encode($response);
	die();	
}













?>