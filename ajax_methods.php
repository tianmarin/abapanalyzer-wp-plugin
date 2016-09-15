<?php
defined('ABSPATH') or die("No script kiddies please!");


//------------------------------------------------------------------------------------------------------------------------------------
/**
* Enable ajaxurl from frontend side
* http://wordpress.stackexchange.com/questions/190297/ajaxurl-not-defined-on-front-end
*
* @since 1.0
* @author Cristian Marin
*/

add_action('wp_head', 'abap_ajaxurl');
function abap_ajaxurl() {

   echo '<script type="text/javascript">
           var ajaxurl = "' . admin_url('admin-ajax.php') . '";
           var aa_url ="'.plugins_url().'"
         </script>';
}
//------------------------------------------------------------------------------------------------------------------------------------
/**
* Registers and enqueues plugin-specific scripts.
*/
function abap_register_ajaxmethods_scripts() {
	wp_register_script(
		'abap_ajax_methods',
		plugins_url( 'js/client/ajax_methods-min.js' , __FILE__),
		array('jquery', 'jquery-ui-core','jquery-ui-autocomplete','jquery-ui-tooltip','jquery-ui-button'),
		'1.0'
	);
	wp_enqueue_script(	'abap_ajax_methods');
	wp_localize_script(
		'abap_ajax_methods',
		'abap_ajax',
		array( 'ajaxurl' => admin_url( 'admin-ajax.php' ))
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
	$postvs=array(
		'id'			=>	'',
		'creator'		=>	get_current_user_id(),
		'sid'			=>	$_POST['sid'],
		'start_date'	=> '',
		'end_date'	=> '',		
	);
	$id=$ANALYSIS->add_new( $postvs );
//	$id=abap_add_new_class_row($ANALYSIS->tbl_name, $postvs, $ANALYSIS->db_fields, $name );
	$response=array();
	if(is_numeric($id) && $id>0){
		$response['message'] = 'OK';
		$response['id'] = $id;
	}else{
		$response['message'] = 'error';
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

	$date=$_POST['year']."/".$_POST['month']."/".$_POST['day'];
	$analysis=$_POST['analysis'];
	
	$fp = fopen($_FILES['sdfmon_file']['tmp_name'], 'rb');
	
	
	
	while(  ($line=fgets($fp))  !==  false){
		if($line[0] == "|"){
//			$response['uno']=$line;
			$line=str_replace(' ','',$line);
			$info=explode("|",$line);
			if( count($info) > 20){
				$response['dos']=$info[12];
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
				}
			}
		}
	}
	$response['message'] = 'OK';
	$response['date'] = $date;
	$response['analysis'] = $analysis;
	echo json_encode($response);
	die();
}
//------------------------------------------------------------------------------------------------------------------------------------
/**
* Delete SDFMON registries related to SDFMON file
*
* @since 1.0
* @author Cristian Marin
*/
function abap_ajax_delete_sdfmon_file(){
	
}
//------------------------------------------------------------------------------------------------------------------------------------
/**
* 
*
* @since 1.0
* @author Cristian Marin
*/
add_action( 'wp_ajax_abap_ajax_get_analysis_sdfmon_files', 'abap_ajax_get_analysis_sdfmon_files' );
function abap_ajax_get_analysis_sdfmon_files(){
	global $SDFMON;
	global $ANALYSIS;
	if(isset($_POST['analysis']) && $_POST['analysis']!=''){
		$response=array();
		$output='';
		$sql="SELECT
				b.date,a.creator,a.sid,a.creation_date
			FROM $ANALYSIS->tbl_name as a LEFT JOIN $SDFMON->tbl_name as b ON b.analysis_id=a.id
			WHERE a.id=".$_POST['analysis']."
			GROUP BY b.date";
		$sdfmon= $SDFMON->get_sql($sql);
		$creator='1';
		$user=get_user_by("ID",$creator);
		$username=$user["user_login"];
		foreach($sdfmon as $sdfile){
			$output.= '<li>'.$sdfile['sid'].'_'.$username.'_'.$sdfile['creation_date'].'_snapmon_'.$sdfile['date'].'.sdfmon</li>';
		}
		$response['status'] = 'OK';
		$response['message'] = $output;
	}else{
		$response['status'] = 'error';
	}
	echo json_encode($response);
	die();	
}

?>