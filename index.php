<?php
/*
 * Plugin Name: ABAP Analyzer
 * Plugin URI: https://github.com/tianmarin/sap-basis-parameters-wp-plugin
 * Description: This Plugin, has been developed by Cristian Marin, towards SAP NetWeaver Systems Administration.
 * It helps to automate the creation of a performance analysis report.
 * Version: 0.9.0
 * Author: Cristian Marin
 * Author URI: http://twitter.com/cmarin
 */
defined('ABSPATH') or die("No script kiddies please!");

//require functions and config classes
//require_once("functions.php");
require_once("classes/pagetemplater.php");
//require_once("ajax_methods.php");
//require_once("js_methods.php");

global $wpdb;
//Global variable for Class, useful for accessing class functions as well as a global variable store
global $aa_vars;

$aa_vars = array(
//Plugin Conf. Variables
	'DEBUG'								=> TRUE,
	'plugin_option_name'				=> 'aa_options',							//Plugin Option (Wordpress default) name
	'plugin_post'						=> 'Y21hcmlu',								//(base64_encode(cmarin) security 'from' request
	'plugin_shortcode'					=> 'aa',									//used by plugin association in shortcodes
//DataBase Tables
	'system'				."_tbl_name"	=> 'z_aa_'		.'system',
	#Configuration
	'collab_opt'			."_tbl_name"	=> 'z_aa_'		.'collab_opt',
	'system_collab'			."_tbl_name"	=> 'z_aa_'		.'system_collab',
	'time_group'			."_tbl_name"	=> 'z_aa_'		.'time_group',
	#Data Sources
	'asset_source'			."_tbl_name"	=> 'z_aa_'		.'asset_source',
	'asset'					."_tbl_name"	=> 'z_aa_'		.'asset',
	'sdfmon'				."_tbl_name"	=> 'z_aa_'		.'sdfmon',
	#Reports
	'report'				."_tbl_name"	=> 'z_aa_'		.'report',
	'report_type'			."_tbl_name"	=> 'z_aa_'		.'report_type',
	'report_type_section'	."_tbl_name"	=> 'z_aa_'		.'report_type_section',
	'report_collab'			."_tbl_name"	=> 'z_aa_'		.'report_collab',
	'section'				."_tbl_name"	=> 'z_aa_'		.'section',
	'section_chart'			."_tbl_name"	=> 'z_aa_'		.'section_chart',
	#Charts
	'chart'					."_tbl_name"	=> 'z_aa_'		.'chart',
	'graph'					."_tbl_name"	=> 'z_aa_'		.'graph',
	'graph_type'			."_tbl_name"	=> 'z_aa_'		.'graph_type',
	'graph_color'			."_tbl_name"	=> 'z_aa_'		.'graph_color',
	'graph_function'		."_tbl_name"	=> 'z_aa_'		.'graph_function',
	'chart_stack'			."_tbl_name"	=> 'z_aa_'		.'chart_stack',
	'chart_graph'			."_tbl_name"	=> 'z_aa_'		.'chart_graph',
//Menu Slugs
	'main'					."_menu_slug"	=> 'aa_'		.'main',
	'system'				."_menu_slug"	=> 'aa_'		.'system'			.'_menu',
	'collab_opt'			."_menu_slug"	=> 'aa_'		.'collab_opt'		.'_menu',
	'asset'					."_menu_slug"	=> 'aa_'		.'asset'			.'_menu',
	'graph'					."_menu_slug"	=> 'aa_'		.'graph'			.'_menu',
	'chart'					."_menu_slug"	=> 'aa_'		.'chart'			.'_menu',
	'section'				."_menu_slug"	=> 'aa_'		.'section'			.'_menu',
	'report_type'			."_menu_slug"	=> 'aa_'		.'report_type'		.'_menu',
	'report'				."_menu_slug"	=> 'aa_'		.'report'			.'_menu',
	'sdfmon'				."_menu_slug"	=> 'aa_'		.'sdfmon'			.'_menu',

//Menu Capabilities
	'main'					."_menu_cap"		=> 'manage_options',
//	'admin'					."_menu_cap"		=> 'administrators',
//	'super'					."_menu_cap"		=> 'administrators',
	'system'				."_menu_cap"		=> 'edit_others_pages',
	'collab_opt'			."_menu_cap"		=> 'edit_others_pages',
	'asset'					."_menu_cap"		=> 'edit_others_pages',
	'graph'					."_menu_cap"		=> 'edit_others_pages',
	'chart'					."_menu_cap"		=> 'edit_others_pages',
	'section'				."_menu_cap"		=> 'edit_others_pages',
	'report_type'			."_menu_cap"		=> 'edit_others_pages',
	'report'				."_menu_cap"		=> 'edit_others_pages',
	'sdfmon'				."_menu_cap"		=> 'edit_others_pages',
);





//---------------------------------------------------------------------------------------------------------------------------------------------------------
if($aa_vars['DEBUG']):
	define( 'DIEONDBERROR', true );
endif;




//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**
* Registra el estilo (CSS) de adminsitración en el backend de WordPress
*
*/
add_action( 'admin_menu', 'aa_register_admin_style' );
function aa_register_admin_style(){
	wp_register_style('aa_admin_style', plugins_url('css/admin/admin.css', __FILE__) );
	wp_enqueue_style('aa_admin_style');
//	wp_enqueue_script('admin_js_bootstrap_hack', plugins_url('js/admin/bootstrap-hack-min.js', __FILE__), false, '1.0.0', false);
}
/**
* Registra el menú básico de Adminsitración en el backend de WordPress
*
*/
add_action( 'admin_menu', 'aa_register_main_menu' );
function aa_register_main_menu(){
	global $aa_vars;
	$page_title	="ABAP Analyzer Main Options";
	$menu_title	="ABAP Analyzer";
	$capability	=$aa_vars['main_menu_cap'];
	$menu_slug	=$aa_vars['main_menu_slug'];
	$function	="aa_main_menu";
	$icon_url	='dashicons-exerpt-view';
	$position	="100";
	add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
}

/**
* Despliega el menú básico de Adminsitración en el backend de WordPress
*
*/
function aa_main_menu() {
	echo '<div class="wrap">';
	echo '<h2>Menú Iincial</h2>';
	require_once("intro.php");
	echo '</div>';
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
require_once("classes/aa_main_class.php");
require_once("classes/aa_system.php");
require_once("classes/config/aa_colllab_opt.php");
require_once("classes/config/aa_time_group.php");
require_once("classes/config/aa_system_collab.php");
require_once("classes/chart/aa_graph_type.php");
require_once("classes/chart/aa_graph_color.php");
require_once("classes/chart/aa_graph_function.php");
require_once("classes/chart/aa_chart_stack.php");
require_once("classes/source/aa_asset_source.php");
require_once("classes/source/aa_asset.php");
require_once("classes/chart/aa_chart_graph.php");
require_once("classes/chart/aa_graph.php");
require_once("classes/chart/aa_chart.php");
require_once("classes/report/aa_section_chart.php");
require_once("classes/report/aa_section.php");
require_once("classes/report/aa_report_type_section.php");
require_once("classes/report/aa_report_type.php");
require_once("classes/config/aa_report_collab.php");
require_once("classes/report/aa_report.php");
require_once("classes/source/aa_sdfmon.php");
//require_once("aa_sdfmon.php");
//---------------------------------------------------------------------------------------------------------------------------------------------------------
//require_once("shortcodes.php");
//require_once("functions-client.php");
require_once 'client_script.php';

?>
