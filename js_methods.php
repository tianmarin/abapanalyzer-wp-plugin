<?php
defined('ABSPATH') or die("No script kiddies please!");

/**
* Registers and enqueues plugin-specific scripts.
*/









//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**
* Form to add a single Class registry to the system
*
* @since 1.0
* @author Cristian Marin
*/
function abap_register_script_formvalidator($form_id) {
	wp_register_script(
		'formvalidator',
		plugins_url( 'js/form-validator/jquery.form-validator.min.js' , __FILE__),
		array('jquery'),
		'1.0'
	);
	wp_register_script(
		'abap_formvalidator',
		plugins_url( 'js/formvalidator.js' , __FILE__),
		array('formvalidator','jquery'),
		'1.0'
	);
	wp_enqueue_script(	'abap_formvalidator' );
}
