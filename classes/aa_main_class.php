<?php
defined('ABSPATH') or die("No script kiddies please!");

/**
* AA_CLASS
*
* Default Class to be extended.
*
* Variables
*
* Functions:
*	db_install()
*	aa_register_class_submenu_page()
*	class_submenu_page()
*	check_post_vars()
*	add_new()
*	update()
*	bulk_delete()
*	get_all()
*	get_single()
*	get_sql()
*
* 	Pendientes
*	- Funciones de registro/log de actividades y errores
*	- Funciones de notificación al usuario administrador
*	
*	
*	
*		CLASS					EXTERNAL			AJAX			EXTEND				CLASS
*	variables										
*	db_install()												<--	__construct
*	register_submenu_page()										<--	__construct
*	bluid_submenu_page()							
*	eval_post_vars()									
*	check_form_values()
*	update_class_row()
*	show_table()										
*	show_form(add|edit)							
*	select_rows(pagination)		X					
*	get_row()					X													<--	show_form
*	get_sql()					X					
*	delete_row()										
*	ajax_function()												<--	__construct
*
*
* $_GET Variables
* - action
* - actioncode
* - item (es el ID)
*
*
* $_POST Variables
* - $this->pluginpost
*	- action
*	- acctincode
*	- form details
*
*
*
*/


abstract class AA_CLASS{
	public 			$class_name;	//como se definió en aa_vars
	protected 		$name_single;	//Nombre singular para títulos, mensajes a usuario, etc.
	protected 		$name_plural;	//Nombre plural para títulos, mensajes a usuario, etc.
	protected 		$parent_slug;	//Identificador de menú padre
	protected 		$menu_slug;		//Identificador de submenú de la clase
	protected 		$plugin_post;	//Utilizadp para validaciones (deprecated)
	protected 		$p_post;		//Utilizadp para validaciones
	protected 		$capability;	//Permisos de usuario a nivel de backend WordPRess
	public	 		$tbl_name;		//Tabla de la clase
	protected 		$db_version;	//Versión de DB (para registro y actualización automática)
	protected 		$crt_tbl_sql;	//Sentencia SQL de creación (y ajuste) de la tabla de la clase
	protected 		$db_fields;		//Registro de columnas de la tabla utilizado para validaciones y visualización de formatos
//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**
* Create or update the Class DB Table structure
*
* This function launches with the register_activation_hook. It executes the
* 'create table' sentence. If the Class table_db_version is higher or
* non-existent.
*
* @since 1.0
* @author Cristian Marin
*/
public function db_install(){
	$current_db_version = get_option( $this->tbl_name."_db_version");
	if( $current_db_version == false || $current_db_version < $this->db_version ){
//	if( true ){
		//Registrar y notificar 'UPGRADE' exitoso
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta($this->crt_tbl_sql);
//		echo $sql;
		update_option( $this->tbl_name."_db_version" , $this->db_version );
//		add_action( 'admin_notices', array( $this, 'db_install_success')  );
//		do_action(array( $this, 'db_install_success'));

	}else{
//		add_action( 'admin_notices', array( $this, 'db_install_error')  );
//		do_action(array( $this, 'db_install_error'));
		//registrar que no fue necesario actualizar nada
//PHP Warning:  Illegal offset type in /Users/cristian/Dropbox/Developer/Development/WP_PLUGINS/wp-includes/plugin.php on line 457
//PHP Warning:  Illegal offset type in isset or empty in /Users/cristian/Dropbox/Developer/Development/WP_PLUGINS/wp-includes/plugin.php on line 468

	}
	return true;
}
public function db_install_success() {
	echo '<div class="alert alert-success notice notice-success is-dismissible" role="alert"> Tabla'.$this->tbl_name.' creada exitosamente</div>';
}
public function db_install_error() {
	echo '<div class="alert alert-danger notice notice-error is-dismissible" role="alert"> Error al crear tabla'.$this->tbl_name.'</div>';
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**
* Registers the Class submenu in the WordPress system.
*
* This function is called by the Wordpress 'admin_menu' action.
*
* @since 1.0
* @author Cristian Marin
*/
public function register_submenu_page() {
	global $aa_vars;
	$parent_slug	=$this->parent_slug;
	$page_title		='Administraci&oacute;n de '.$this->name_plural;
	$menu_title		=$this->name_plural;
	$capability		=$this->capability;
	$menu_slug		=$this->menu_slug;
	$function		=array( $this , "bluid_submenu_page" );

	add_submenu_page(
		$parent_slug,
		$page_title,
		$menu_title,
		$capability,
		$menu_slug,
		$function
	);
}

//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**
* Generates the basic Class Wordpress Admin Adminitration page.
* This function evaluates the $_GET variables in order to correctly display the
* desire content.
* It also calls the eval_post_vars() function to evaluates and execute POST
* functions
* This function is called by the Wordpress 'add_submenu_page' function.
*
* @since 1.0
* @author Cristian Marin
*/
public function bluid_submenu_page(){
	wp_register_script(
		'aa_WPADMIN',
		plugins_url( '../js/admin/admin-min.js' ,  __FILE__ ),
		array('jquery','jquery-ui-sortable'),
		'1.0'
	);
	wp_enqueue_script('aa_WPADMIN');
	wp_localize_script(
		'aa_WPADMIN',
		'aaWPADMIN',
		array(
			'ppost' => $this->plugin_post,
		)
	);
	$output='<div class="bootstrap-wrapper">';
	$output.='<div class="container-fluid">';
		$output.='<div class="page-header">';
			$QS = http_build_query(array_merge($_GET, array(
					"action"		=>'add',
					'actioncode' 	=> wp_create_nonce("add"),
					)));
			$URL=htmlspecialchars("$_SERVER[PHP_SELF]?$QS");

//			$link.='?page='.$this->menu_slug.'&action=add&actioncode='.wp_create_nonce($element['id']."add");
			$link=$URL;
			$output.='<h2>Administraci&oacute;n de  '.$this->name_plural.' <small>(<a href="'.$link.'">Crear nuevo</a>)</small></h2>';
		$output.='</div>';
//	$output.='</div>';
	$action = ( isset( $_GET["action"] ) ) ? $_GET["action"] : "";
	$item = ( isset( $_GET["item"] ) ) ? $_GET["item"] : "";
	$actioncode = ( isset( $_GET["actioncode"] ) ) ? $_GET["actioncode"] : "";

	
	$output.=self::eval_post_vars('post');
	switch($action){
		case 'add':
			$output.=$this->show_form("add");
			break;
		case 'edit':
			$output.=$this->show_form("edit",$item );				
			break;
		case null:
		case '':
			$output.=$this->show_table();
			break;
		default:
			$output.=$this->special_wp_admin_page($action);
	}
	$output.="</div>";
	$output.="</div>";
	echo $output;
}
protected function special_wp_admin_page($action){
	if(method_exists($this, $action)){
		return call_user_func_array(array($this, $action), array());
	}else{
		return false;
	}
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**
* Check the content of the REQUEST (GET and POST) variables and subsequently
* choses an action to perform.
* The main POST variable with all the content would be the self $plugin_post
* variable.
*
* @since 1.0
* @author Cristian Marin
*/
protected function eval_post_vars($method = null){
	global $aa_vars;
	$response='';
	if ($method == 'post'){
		$post = isset( $_POST[$this->plugin_post] ) ? $_POST[$this->plugin_post] : null;
	}else{
		$post = isset( $_REQUEST[$this->plugin_post] ) ? $_REQUEST[$this->plugin_post] : null;
	}
//	$response.=$post;
	if( $post != '' ){
		if( isset( $post["action"] ) && isset( $post["actioncode"] ) ){
			if( wp_verify_nonce( $post["actioncode"], $post["action"] ) ){
				switch ( $post["action"] ){
					case "add":
						$query=self::update_class_row('add',$post);
						if($query['status'] == 'ok'){
							$response = '<div class="alert alert-success" role="alert">'.$query['message'].'</div>';
						}else{
							$response = '<div class="alert alert-warning" role="alert">'.$query['message'].'</div>';
						}
						break;
					case "bulkdelete":
						self::bulk_delete( $post );
						break;
					case "edit":
						$query=self::update_class_row('edit',$post);
						if($query['status'] == 'ok'){
							$response = '<div class="alert alert-success" role="alert">'.$query['message'].'</div>';
						}else{
							$response = '<div class="alert alert-warning" role="alert">'.$query['message'].'</div>';
						}
						break;
				}
			}else{
				$response = '<div class="alert alert-danger" role="alert">Error de validación de seguridad (wp_verify_nonce).</div>';
			}
		}else{
			$response = '<div class="alert alert-danger" role="alert">Error de validación de variables post (action & actioncode).</div>';			
		}
	}
	return $response;
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**
* Adds or Updates a single Class row into the database.
* This function evaluates the input array regarding the class $db_fields.
* Every db_field input is analyzed according to its 'type' definition, and prepares
* non-user inputs (e.g. timestamp, id)
*
* This function returns an array with status, and the id of the inserted row.
*
* @since 1.0
* @author Cristian Marin
*/
protected function update_class_row($action="edit", $postvs ){
	global $wpdb;
	$response = array();
	if(self::check_form_values($postvs)){
		$insertArray 	= array();
		$editArray		= array();
		$editArray_eval	= array();
		$whereArray		= array();
		foreach($this->db_fields as $key => $db_field){
			switch($db_field['type']){
				case 'id':
					$insertArray[$key]='';
					$whereArray=array($key => intval($postvs[$key]));
					break;
				case 'dual_id':
					$insertArray[$key] = $whereArray[$key] = intval($postvs[$key]);
					break;
				case 'timestamp':
					$insertArray[$key]=current_time( 'mysql');
					break;
				case 'current_user_id':
					$insertArray[$key]=intval(get_current_user_id());
					break;
				case 'nat_number':
					$insertArray[$key] = $editArray[$key] = intval($postvs[$key]);
					break;
				case 'bool':
					if(isset($postvs[$key])):
						$insertArray[$key] = $editArray[$key] = 1;
					else:
						$insertArray[$key] = $editArray[$key] = 0;
					endif;
					break;
				case 'exclude':
				case 'display':
					break;
				default:
					$insertArray[$key] = $editArray[$key] = strip_tags(stripslashes( $postvs[$key] ));
			}
		}
		$response['insert']=$insertArray;
		$response['edit']=$editArray;
		$response['where']=$whereArray;
		if($action == 'add'){
			if ( $wpdb->insert( $this->tbl_name, $insertArray ) ){
				$response['id']=$wpdb->insert_id;
				$response['status']='ok';
				$response['message']="El nuevo ".$this->name_single." ha sido guardado.";
			}else{
				$response['status']='error';
				$response['message']="Hubo un error al agregar el nuevo ".$this->name_single."; intenta nuevamente. :)";
			}
		}elseif($action == 'edit'){
			$result = $wpdb->update($this->tbl_name,$editArray,$whereArray);
			if( $result === false ){
				$response['status']='error';
				$response['message']="Hubo un error al editar el ".$this->name_single."; intenta nuevamente. :)";
			}elseif ( $result == 0){
				$response['status']='error';
				$response['message']="Los valores son iguales. ".$this->name_single." no modificado.";
			}else{
				$response['status']='ok';
				$response['message']=$this->name_single." editado exitosamente.";
			}
		}elseif($action == 'delete'){
			$result = $wpdb->delete($this->tbl_name,$editArray,$whereArray);
			if( $result === false ){
				$response['status']='error';
				$response['message']="Hubo un error al eliminar el ".$this->name_single."; intenta nuevamente. :)";
			}elseif ( $result == 0){
				$response['status']='error';
				$response['message']="No hay ".$this->name_plural." que eliminar.";
			}else{
				$response['status']='ok';
				$response['message']=$this->name_single." eliminado exitosamente.";
			}
		}
	}else{
		$response['status']='error';
		$response['message']="No has ingresado los datos correctos para ";
		$response['message'].=(($action=='add')?'un nuevo ':'editar un').$this->name_single."; intenta nuevamente. :)";
	}
	return $response;
	
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**
* Check the correct values of the required input variables according to
* Class definition by $db_fields.
*
* @since 1.0
* @author Cristian Marin
*/
protected function check_form_values( $postvs=null){
	foreach($this->db_fields as $key => $db_field){
		if($db_field['required']==true){
			switch($db_field['type']){
				case 'id':
					break;
				case 'number':
					if (!is_numeric($postvs[$key])):
						return false;
					endif;
					break;
				case 'nat_number':
					if (!is_numeric($postvs[$key]) || $postvs[$key]<1):
						return false;
					endif;
					break;
				case 'select':
					if (!is_numeric($postvs[$key]) || $postvs[$key]<1):
						return false;
					endif;
					break;
				case 'text':
					if (!is_string($postvs[$key]) || $postvs[$key]==''):
						return false;
					endif;
					break;
				default:
			}
		}
	}
	return true;
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**
* Creates the main content of the administation page
*
* @global wpdb $wpdb
* @since 1.0
* @author Cristian Marin
* @package WordPress
*/
function show_table(){
	//Global
	global $wpdb;
	global $aa_vars;
	$output='';
	$output.='<table class="table table-striped table-condensed">';
	$output.='<thead>';
	$output.='<tr>';
	foreach($this->db_fields as $key => $db_field){
		if(isset($db_field['in_wp_table']) &&  $db_field['in_wp_table'] == true){
			$output.='<th>';
			$output.='<p class="text-uppercase ">'.$db_field['desc'].'</p>';
			$output.='</th>';
		}
	}
	$output.='</tr>';
	$output.='</thead>';
	$output.='<tbody>';
	$content=self::select_rows();
	$output.=$content['tbody'];
	$output.='</tbody>';
	$output.='</table>';
	$output.=$content['pagination'];
	return $output;
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**
* Selects a subset of the class registry, based on the $_REQUEST variables.
*
*
*
* @since 1.0
* @author Cristian Marin
*/
protected function select_rows($elem_per_page = 10){
	global $wpdb;
	global $aa_vars;
	$response=array();
	$sql_elements=array();
	$display_elements=array();
	settype($tbody,'string');
	settype($nav,'string');
	$current_page=isset($_REQUEST['pageno']) ? $_REQUEST['pageno'] : 1;
	foreach($this->db_fields as $key => $db_field){
		if(isset($db_field['in_wp_table']) &&  $db_field['in_wp_table'] == true && $db_field['type'] != 'display'){
			array_push($sql_elements, $key);
		}
	}

	$sql="SELECT ".implode(",", $sql_elements)." 
			FROM ".$this->tbl_name."
			LIMIT ".$elem_per_page*($current_page-1).",".$elem_per_page;
	$elements=self::get_sql($sql);
	foreach($elements as $element){
		$tbody.="<tr>";
		foreach($this->db_fields as $key => $db_field){
			if($db_field['type'] == 'display'){
				$element[$key]=true;
			}
		}
		foreach($element as $key => $value){
			$tbody.='<td>';
//			$tbody.=' key: '.$key.'<br/>';
//			$tbody.=' value: '.$value.'<br/>';
//			$tbody.=' id: '.$element['id'].'<br/>';
			if(isset($this->db_fields[$key]['sp_wp_table']) && $this->db_fields[$key]['sp_wp_table'] == true){
				if(method_exists($this, 'sp_wp_table_'.$key)){
					$tbody.=call_user_func_array(array($this, 'sp_wp_table_'.$key), array($value,$element['id']));
				}else{
					$tbody.=$value;
				}
			}else{
				$tbody.=$value;
			}
			
			if(isset($this->db_fields[$key]['wp_table_lead']) && $this->db_fields[$key]['wp_table_lead'] == true){
				$tbody.='<div class="row-actions">';
					$tbody.='<span class="edit">';
						$tbody.='<a href="?page='.$this->menu_slug.'&action=edit&item=';
						$tbody.=$element['id'].'&actioncode='.wp_create_nonce($element['id']."edit").'" ';
//						$tbody.='class="btn btn-default btn-sm"';
						$tbody.='>';
							$tbody.='Editar';
						$tbody.='</a>';
					$tbody.='</span>';
				$tbody.='</div>';
			}
			$tbody.='</td>';
		}
		$tbody.="</tr>";
	}
	$count =intval($wpdb->get_var( "SELECT COUNT(*) FROM ".$this->tbl_name));
	$total_pages=ceil($count / $elem_per_page );
	if($total_pages > 1){
		$nav='<nav aria-label="...">';
			$nav.='<ul class="pagination">';
				$prev=($current_page-1 > 1)?$current_page-1 : 1;
				$QS = http_build_query(array_merge($_GET, array("pageno"=>$prev)));
				$URL=htmlspecialchars("$_SERVER[PHP_SELF]?$QS");
				$nav.='<li class="'.($current_page==1 ? 'disabled' : '').'"><a href="'.$URL.'" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
				for($i=1; $i <= $total_pages ; $i++){
					$QS = http_build_query(array_merge($_GET, array("pageno"=>$i)));
					$URL=htmlspecialchars("$_SERVER[PHP_SELF]?$QS");
					$nav.='<li class="'.($current_page == $i ? 'active' : '').'"><a href="'.$URL.'">'.$i.'<span class="sr-only">(current)</span></a></li>';
				}
				$post=($current_page+1 < $total_pages)?$current_page+1 : $total_pages;
				$QS = http_build_query(array_merge($_GET, array("pageno"=>$post)));
				$URL=htmlspecialchars("$_SERVER[PHP_SELF]?$QS");
				$nav.='<li class="'.($total_pages==$current_page ? 'disabled' : '') .'"><a href="'.$URL.'" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
			$nav.='</ul>';
		$nav.='</nav>';
	}
	$response['tbody']=$tbody;
	$response['pagination']=$nav;
	return $response;
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**
* Read a single Class registry from the database
* $output_style
*  + ARRAY_A	:array of rows (associative array (column => value, ...))
*  + ARRAY_N	:array of rows (numerically indexed array (0 => value, ...))
*  + OBJECT		:array of rows (object. ( ->column = value ))
*  + OBJECT_K	:associative array of row objects keyed by the value of each row's first column's value. Duplicate keys are discarded.
* @since 1.0
* @author Cristian Marin
*/
function get_all($output_style='ARRAY_A'){
	global $wpdb;
	global $aa_vars;
	$output = $wpdb->get_results( "SELECT * FROM ".$this->tbl_name,$output_style);
	if($aa_vars['DEBUG']):
		$wpdb->show_errors();
	endif;
	return $output;
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**
* Read a single Class registry from the database
*
* $id			: id of the wanted row
* $output_style
*  + ARRAY_A	:array of rows (associative array (column => value, ...))
*  + ARRAY_N	:array of rows (numerically indexed array (0 => value, ...))
*  + OBJECT		:array of rows (object. ( ->column = value ))
*  + OBJECT_K	:associative array of row objects keyed by the value of each row's first column's value. Duplicate keys are discarded.
* @since 1.0
* @author Cristian Marin
*/
function get_single( $id = 0 , $output_style = 'ARRAY_A' ){
	global $wpdb;
	global $aa_vars;
	$output = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$this->tbl_name." WHERE `id` = %d", $id ),$output_style );
	if($aa_vars['DEBUG']):
		$wpdb->show_errors();
	endif;
	return $output;
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**
* Read all Class registry from the database
*
* @since 1.0
* @author Cristian Marin
*/
function get_sql($sql , $output_style = 'ARRAY_A' ){
	global $wpdb;
	global $aa_vars;
	//Protects from SQL injection and names with apostrophes
	//$sql = $mysqli->real_escape_string($sql);
	$output=$wpdb->get_results( $sql, $output_style );
	if($aa_vars['DEBUG']):
		$wpdb->show_errors();
	endif;
	return $output;
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**
* Form to add/edit a single Class registry
*
* @since 1.0
* @author Cristian Marin
*/
function show_form(
					$type=null,				//add,update
					$item=null,				//id a editar
					$menu_slug=null,
					$plugin_post=null,
					$fields=null,
					$id=null){
	global $wpdb;
	$tbody='';
	switch($type){
		case 'edit':
			$title='Editar ';
			$subtitle=' <small>(id: '.$item.')</small>';
			$sql="SELECT * FROM ".$this->tbl_name." WHERE id=".$item;
			$element=self::get_single( $item);
			foreach( $this->db_fields as $key => $field){
				if($this->db_fields[$key]['type']!='display'){
					$this->db_fields[$key]['value']=$element[$key];					
				}
			}
			break;
			//Seleccionar valores
		case 'add':
			$title='Crear nuevo ';
			$subtitle='';
			break;
	}
	$output='';
	$output.='<div class="row">';
		$output.='<div class="page-header">';
			$output.='<h2>'.$title.$this->name_single.$subtitle.'</h2>';
		$output.='</div>';
		$output.='<form
					class="form-horizontal"
					action="?page='.$this->menu_slug.'"
					method="post"
					id="'.$this->menu_slug.'"
					>';
	$output.='<input type="hidden" name="'.$this->plugin_post.'[action]" value="'.$type.'" />';
	$output.=wp_nonce_field( $type, $this->plugin_post."[actioncode]");
	if(isset($item)){
	$output.='<input type="hidden" name="'.$this->plugin_post.'[id]" value="'.$item.'" />';
		
	}
	
//	wp_create_nonce($element['id']."edit")
	foreach ( $this->db_fields as $key => $field ){
		$desc=isset($field['desc'])?$field['desc']:'';
		$form_size=(isset($field['form_size']))?$field['form_size']:'';
		$value=(isset($field['value']))?$field['value']:'';
		$value=(isset($field['value']))?'value="'.$field['value'].'"':'';
		$min=(isset($field['min']))?$field['min']:'';
		$max=(isset($field['max']))?$field['max']:'';
		$id=$this->plugin_post.'['.$key.']';
		$required=($field['required']==TRUE)?'data-validation="true"':'';
		$placeholder=(isset($field['placeholder']))?'placeholder="'.$field['placeholder'].'"':'placeholder="'.$field['desc'].'"';

		if( isset($field['in_form']) && $field['in_form'] == false){
//			$output.='<input type="hidden" id="'.$id.'" name="'.$id.'" value="'.$id.'" />';
		}else{
			$output.='<div class="form-group '.$form_size.'">';
				$output.='<label for="'.$id.'" class="col-sm-2 control-label">'.$desc.'</label>';
				$output.='<div class="col-sm-10">';
				switch($field['type']){
					case 'date':
						$output.='<input
										type="date"
										class="form-control"
										id="'.$id.'"
										name="'.$id.'"
										'.$placeholder.'
										'.$required.'
										'.$value.'
										maxlength="'.$field['maxchar'].'"
										/>';
						break;
					case 'text':
						$output.='<input
										type="text"
										class="form-control"
										id="'.$id.'"
										name="'.$id.'"
										'.$placeholder.'
										'.$required.'
										'.$value.'
										maxlength="'.$field['maxchar'].'"
										/>';
						break;
					case 'textarea':
						$output.='<textarea
										rows="4"
										class="form-control"
										id="'.$id.'"
										name="'.$id.'"
										'.$required.'
										>'.( isset($field['value']) ? $field['value'] : '').'</textarea>';
						break;
					case 'bool':
						$output.='<input
										type="checkbox"
										class="form-control aa-admin-check"
										id="'.$id.'"
										name="'.$id.'"
										'.(isset($field['value']) && ($field['value'] != false)?'checked':'').'
										/>';
							$output.='<label for="'.$id.'">'.$desc.'</label>';
						break;
					case 'number':
						$output.='<input
										type="number"
										class="form-control"
										id="'.$id.'"
										name="'.$id.'"
										placeholder="'.$placeholder.'"
										'.$required.'
										'.$value.'
										min="'.$field['min'].'"
										max="'.$field['max'].'"
										/>';
						break;
					case 'nat_number':
						$output.='<input
										type="number"
										class="form-control"
										id="'.$id.'"
										name="'.$id.'"
										placeholder="'.$placeholder.'"
										'.$required.'
										'.$value.'
										min="'.$field['1'].'"
										max="'.$field['max'].'"
										/>';
						break;
					case 'percentage':
						$output.='<div class="input-group">';
						$output.='<input
										type="number"
										class="form-control"
										id="'.$id.'"
										name="'.$id.'"
										placeholder="'.$placeholder.'"
										'.$required.'
										'.$value.'
										min="'.$field['min'].'"
										max="'.$field['max'].'"
										/>';
						$output.='<span class="input-group-addon" id="basic-addon2">%</span>';
						$output.='</div>';
						break;
						case 'select':
							if(method_exists($this, 'sp_form_'.$key)){
								$options=call_user_func_array(array($this, 'sp_form_'.$key),array());
								if(count($options) == 0){
									$field['options']=array(0 => "No hay informaci&oacute;n");
								}else{
									$field['options']=$options;
								}
							}else{
								$field['options']=array(0 => "No hay informaci&oacute;n");
							}
							$output.='<select
										class="form-control"
										id="'.$id.'"
										name="'.$id.'"
										'.$required.'
										">';
							$output.='<option value="0" disabled>Seleccionar</option>';
							foreach($field['options'] as $sel_key => $sel_opt){
								$output.='<option value="'.$sel_key.'" ';
								
								$output.=isset($field['value']) ? ($sel_key == $field['value'] ? " selected " : '') : '';
								$output.='>'.$sel_opt.'</option>';
							}
							$output.='</select>';
						break;

				}
					$output.='<p class="help-block">'.$field['form-help'].'</p>';
				$output.='</div>';
			$output.='</div>';
			
		}
	}
//	if(method_exists($this, 'special_form')){
//		$output.=$this->special_form($item);
//	}
	$output.='<div class="form-group '.$form_size.'">';
		$output.='<div class="col-sm-2 control-label"></div>';
			$output.='<div class="col-sm-10">';
				$QS = http_build_query(array_merge($_GET, array("action"=>'')));
				$URL=htmlspecialchars("$_SERVER[PHP_SELF]?$QS");
				$output.='<a href="'.$URL.'" class="btn btn-default">Cancelar</a>';
				$msg=($type=="add")?"Agregar":"Editar";
				$output.='<button type="submit" class="btn btn-primary">'.$msg.'</button>';
			$output.='</div>';
		$output.='</div>';
	$output.='</div>';
	$output.='</form>';
	$output.='</div>';
	return $output;
}
protected function write_log($log){
	if ( is_array( $log ) || is_object( $log ) ) {
		error_log( print_r( $log, true ) );
	}else{
		error_log( $log );
	}
}
//END OF CLASS	
}



/*
Psuedo codigo


Desplegar reporte:
	foreach section (en el orden que el usuario indicó o por defecto en el orden que tiene el sistema)
		(si existe) mostrar texto de introducción
		foreach chart
			generar chart
			foreach grafico
				seleccionar la información tomando en cuenta las fechas
				desplegar gráfico (oculto o visible) en el formato que se indica
			endforeach
		endforeach
	endforeach



La información desplegada depende del tipo de reporte
cada tipo de reporte debe indicar la información


























*/
?>