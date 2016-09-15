<?php
/*
 * Plugin Name: ABAP Analyzer
 * Plugin URI: https://github.com/tianmarin/sap-basis-parameters-wp-plugin
 * Description: This Plugin, has been developed by Cristian Marin, towards SAP NetWeaver Systems Administration.
 * It helps to automate the creation of a performance analysis report.
 * Version: 1.0.0
 * Author: Cristian Marin
 * Author URI: http://twitter.com/cmarin
 */
defined('ABSPATH') or die("No script kiddies please!");


require_once("functions.php");
//require_once("ajax_methods.php");
//require_once("js_methods.php");

global $wpdb;

//Global variable for Class, useful for accessing class functions as well as a global variable store
global $abap_vars;

$abap_vars = array(
//Plugin Conf. Variables
	'DEBUG'								=> TRUE,
	'plugin_option_name'				=> 'abap_options',							//Plugin Option (Wordpress default) name
	'plugin_post'						=> 'Y21hcmlu',								//(base64_encode(cmarin) security 'from' request
	'plugin_shortcode'					=> 'abap',									//used by plugin association in shortcodes
//DataBase Tables
#Parameters
	'analysis'				."_tbl_name"	=> 'z_abap_'	.'analysis',
	'abap_param'			."_tbl_name"	=> 'z_abap_'	.'abap_param',
	'abap_param_type'		."_tbl_name"	=> 'z_abap_'	.'abap_param_type',
	'abap_param_location'	."_tbl_name"	=> 'z_abap_'	.'abap_param_location',
#System Info
	'sdfmon'				."_tbl_name"	=> 'z_abap_'	.'sdfmon',
	'st02'					."_tbl_name"	=> 'z_abap_'	.'st02',
	'cpu'					."_tbl_name"	=> 'z_abap_'	.'cpu',
//Menu Slugs
	'main'					."_menu_slug"		=> 'z_abap_'.'main',
//	'admin'					."_menu_slug"		=> 'z_ita_'.'admin',
//	'super'					."_menu_slug"		=> 'z_ita_'.'super',
	'analysis'				."_menu_slug"		=> 'z_abap_'.'analysis'				.'_menu',
	'sdfmon'				."_menu_slug"		=> 'z_abap_'.'sdfmon'				.'_menu',

//Menu Capabilities
	'main'					."_menu_cap"		=> 'manage_options',
//	'admin'					."_menu_cap"		=> 'administrators',
//	'super'					."_menu_cap"		=> 'administrators',
	'analysis'				."_menu_cap"		=> 'edit_others_pages',
	'sdfmon'				."_menu_cap"		=> 'edit_others_pages',
);





//---------------------------------------------------------------------------------------------------------------------------------------------------------
if($abap_vars['DEBUG']):
	define( 'DIEONDBERROR', true );
endif;




//---------------------------------------------------------------------------------------------------------------------------------------------------------

add_action( 'admin_menu', 'abap_register_menu_style' );
function abap_register_menu_style(){
	wp_register_style('abap_admin_style', plugins_url('css/admin/admin.css', __FILE__) );
	wp_enqueue_style('abap_admin_style');
//	wp_register_script("lesscss",plugins_url( 'js/less.js' , __FILE__ ));
//	wp_enqueue_script("lesscss");
//	echo '<link rel="stylesheet/less" type="text/css" media="all" href="'.plugins_url( 'css/style.css' , __FILE__ ).'">';

}

add_action( 'admin_menu', 'abap_register_main_menu' );
function abap_register_main_menu(){
	global $abap_vars;
	$page_title	="ABAP Analyzer Main Options";
	$menu_title	="ABAP Analyzer";
	$capability	=$abap_vars['main_menu_cap'];
	$menu_slug	=$abap_vars['main_menu_slug'];
	$function	="abap_main_menu";
	$icon_url	='dashicons-exerpt-view';
	$position	="100";
	add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
}


function abap_main_menu() {
	echo '<div class="wrap">';
	echo '<h2>NOVIS</h2>';
//	require_once(plugins_url('intro.php', __FILE__));//no debería ser este el correcto?
	require_once("intro.php");
	echo '</div>';
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
require_once("main_class.php");
require_once("abap_analysis.php");
require_once("abap_sdfmon.php");
//---------------------------------------------------------------------------------------------------------------------------------------------------------
//require_once("shortcodes.php");
require_once("functions-client.php");

?>
