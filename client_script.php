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
add_shortcode( "abap_analyzer", 'aa_client_handle_shortcode' );
function aa_client_handle_shortcode($atts){	
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
		'3.0.1'
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
		'amcharts-responsive',
		plugins_url( 'js/amcharts/plugins/responsive/responsive.min.js' , __FILE__),
		array('amcharts'),
		'3.2'
	);
//	wp_enqueue_script('amcharts-responsive');
	//-----------------------------------------------------
	wp_register_script(
		'scrollex',
		plugins_url( 'js/jquery.scrollex-master/jquery.scrollex.min.js' , __FILE__),
		array('jquery'),
		'2'
	);
	//-----------------------------------------------------
	wp_register_script(
		'scrolly',
		plugins_url( 'js/jquery.scrolly.min.js' , __FILE__),
		array('jquery'),
		'1'
	);
	//-----------------------------------------------------
	wp_register_script(
		'bootstrap',
		plugins_url( 'css/bootstrap-master/dist/js/bootstrap.min.js' , __FILE__),
		array('jquery'),
		'1'
	);
	//-----------------------------------------------------
	wp_register_script(
		'abap_frontend',
		plugins_url( 'js/client/aa_client_script-min.js' , __FILE__),
		array('jquery-confirm','amcharts-serial','amcharts-responsive','jquery-ui-accordion','jquery-ui-sortable','scrollex','scrolly','bootstrap'),
		'1.0'
	);
	wp_enqueue_script('abap_frontend');
	wp_localize_script(
		'abap_frontend',
		'aaServerData',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'aaUrl' => plugins_url('',__FILE__).'/',
			'userId' => wp_get_current_user()->user_login
		)
	);
}
//------------------------------------------------------------------------------------------------------------------------------------

?>