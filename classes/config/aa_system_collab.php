<?php
defined('ABSPATH') or die("No script kiddies please!");

class SYSTEM_COLLAB_CLASS extends AA_CLASS{

/**
* Esta función es llamada apenas se crea la clase.
* Es utilizada para instanciar las diferentes clases con su información vital.
*
*/
function __construct(){
	global $wpdb;
	global $aa_vars;
	//como se definió en aa_vars
	$this->class_name	= 'system_collab';
	//Utilizadp para validaciones
	$this->plugin_post	= $aa_vars['plugin_post'];
	//Tabla de la clase
	$this->tbl_name		= $wpdb->prefix.$aa_vars[$this->class_name.'_tbl_name'];
	//Versión de DB (para registro y actualización automática)
	$this->db_version	= '1';
	//Reglas actuales de caracteres a nivel de DB.
	//Dado que esto sólo se usa en la cración de la tabla
	//no se guarda como variable de clase.
	$charset_collate	= $wpdb->get_charset_collate();
	//Sentencia SQL de creación (y ajuste) de la tabla de la clase
	$this->crt_tbl_sql	=	"CREATE TABLE ".$this->tbl_name." (
								system_id tinyint(1) unsigned not null,
								user_id bigint(20) unsigned not null,
								PRIMARY KEY (system_id, user_id) 
							) $charset_collate;";
	//Registro de columnas de la tabla utilizado para validaciones y visualización de formatos
	$this->db_fields	= array(
		'system_id' => array(
			'type'			=>'nat_number',
			'required'		=>true,
		),
		'user_id' => array(
			'type'			=>'nat_number',
			'required'		=>true,
		),
	);
	register_activation_hook(WP_PLUGIN_DIR."/abap_analyzer/"."index.php", array( $this, 'db_install') );
//	add_action( 'wp_ajax_add_system_collab',			array( $this , 'add_system_collab'			));
//	add_action( 'wp_ajax_remove_system_collab',			array( $this , 'remove_system_collab'		));
//	add_action( 'wp_ajax_get_system_collab',			array( $this , 'get_system_collab'			));
	add_action( 'wp_ajax_aa_get_system_users',			array( $this , 'aa_get_system_users'		));
	add_action( 'wp_ajax_aa_remove_system_users',		array( $this , 'aa_remove_system_users'		));
	add_action( 'wp_ajax_aa_add_system_users',			array( $this , 'aa_add_system_users'		));
	add_action( 'wp_ajax_aa_search_system_users',		array( $this , 'aa_search_system_users'		));
	add_action( 'wp_ajax_fe_system_collab_get_users',	array( $this , 'fe_system_collab_get_users'	));
	add_action( 'wp_ajax_fe_search_system_users',		array( $this , 'fe_search_system_users'		));
	add_action( 'wp_ajax_fe_add_system_users',			array( $this , 'fe_add_system_users'		));
	add_action( 'wp_ajax_fe_remove_system_users',		array( $this , 'fe_remove_system_users'		));
	
	
}
/*
used by system to display wp_table
*/
public function get_users($system_id=null){
	$user_list=array();
	if($system_id == null){
		return false;
	}
	$sql="SELECT user_id FROM ".$this->tbl_name.' WHERE system_id="'.$system_id.'"';
	foreach( self::get_sql($sql) as $key => $val){
		array_push($user_list, $val['user_id']);
	}
	return $user_list;
}
public function aa_search_system_users(){
	global $SYSTEM;
	$response=array();
	$response['data']=array();
	$postvs=$_POST;
	$collab=self::get_users($postvs['id']);
	array_push($collab,	$SYSTEM->get_single($postvs['id'])['owner_id']);
	$args = array(
		'exclude'			=>$collab,
		'number'			=>	10,
		'search'			=> '*'.esc_attr( $postvs['system-collab-user'] ).'*',
		'search_columns'	=> array( 'user_login', 'user_email'),
	);
	$user_query = new WP_User_Query( $args );
	$user_list = $user_query->get_results();
//	$response['todo']=($user_list);
	$i=0;
	if ( ! empty( $user_list ) ) {
		$response['status'] = 'ok';
		foreach ( $user_list as $user ) {
			$response['data']['elem_'.$user->ID]=array();
			$response['data']['elem_'.$user->ID]['elementId']=$user->ID;
			$response['data']['elem_'.$user->ID]['elementTitle']='<h4 class="list-group-item-heading">'.$user->user_login.'</h4>';
			$response['data']['elem_'.$user->ID]['elementBody']='<p class="list-group-item-text">'.$user->user_email.'</p>';
			$i++;
		}
		$response['elementCount']=$i;
	} else {
		$response['noElementTitle']='<h4 class="list-group-item-heading">No hay elementos</h4>';
		$response['noElementBody']='<p class="list-group-item-text">Trata de buscarlo de otra forma.</p>';
		$response['status'] = 'error';
	}
//	$response['colaboradores']=$collab;
	echo json_encode($response);
	die();
}
public function aa_get_system_users(){
	$response=array();
	$system_id=$_POST['id'];
	$args = array(
		'include'			=>	self::get_users($system_id),
		'number'			=> 10,
		'search_columns'	=> array('ID'),
	);
	if( ! empty( $args['include'])){
		$user_query = new WP_User_Query( $args );
		$user_list = $user_query->get_results();
		if ( ! empty( $user_list ) ) {
			$i=0;
			$response['status'] = 'ok';
			foreach ( $user_list as $user ) {
				$response['data']['elem_'.$user->ID]=array();
				$response['data']['elem_'.$user->ID]['elementId']=$user->ID;
				$response['data']['elem_'.$user->ID]['elementTitle']='<h4 class="list-group-item-heading">'.$user->user_login.'</h4>';
				$response['data']['elem_'.$user->ID]['elementBody']='<p class="list-group-item-text">'.$user->user_email.'</p>';
				$i++;
			}
		$response['elementCount']=$i;
		}
	}else{
		$response['status'] = 'error';
		$response['noElementTitle']='<h4 class="list-group-item-heading">No hay elementos</h4>';
		$response['noElementBody']='<p class="list-group-item-text">Agrega usuarios con el formulario de a continuaci&oacute;n.</p>';
	}
	echo json_encode($response);
	die();
}
public function aa_add_system_users(){
	$insert_array=array(
		'system_id'	=>$_POST['id'],
		'user_id'	=>$_POST['element_id'],
	);
	$response=self::update_class_row('add',$insert_array);
	echo json_encode($response);
	die();
}
public function aa_remove_system_users(){
	$delete_array=array(
		'system_id'	=>$_POST['id'],
		'user_id'	=>$_POST['element_id'],
	);
	$response=self::update_class_row('delete',$delete_array);
	echo json_encode($response);
	die();
}
public function fe_system_collab_get_users(){
	$response=array();
	$system_id=$_POST['system_id'];
	if( intval($system_id)>0 && $system_id!=null ){
		$users=self::get_users($system_id);
		global $SYSTEM;
		$system=$SYSTEM->get_single($system_id);
		$response['users']=array();
		foreach($users as $user){
			$response['users']['user_'.$user]='<a href="#" data-user-id="'.$user.'" data-system-id="'.$system_id.'" title="Eliminar a '.get_userdata($user)->user_email.' como colaborador del sistema: '.$system['sid'].' - '.$system['short_name'].'"><i class="fa fa-user-times fa-fw fa-pull-right"></i> '.get_userdata($user)->user_login.'</a>';
		}
		$response['status']='ok';
		
	}else{
		$response['error']=true;
		$response['message']="Error en la llamada a esta página.";
	}
	echo json_encode($response);
	die();
}
public function special_form($id=null){
	$output='';
	$output.='<form class="form-horizontal" id="aa-ajax-wp-filter"
		data-search-action="aa_search_system_users"
		data-get-action="aa_get_system_users"
		data-add-action="aa_add_system_users"
		data-remove-action="aa_remove_system_users"
		>';
	$output.='<div class="form-group aa-system-wpadmin-form">';
		$output.='<label class="col-sm-2 control-label">Usuarios Colaboradores</label>';
		$output.='<div class="col-sm-10">';
		if($id == null){
			$output.='<p class="help-block">Primero debes crear el sistema. Una vez que ya has creado el sistema puedes editarlo y agregar colaboradores.</p>';
		}else{
			$output.='<ul class="list-group" id="aa-element-list"></ul>';
			$output.='<div class="clearfix"></div>';
			$output.='<p class="lead">Agregar Usuarios</p>';
				$output.='<input type="hidden"
				name="'.$this->plugin_post.'[id]"
					value="'.$id.'"/>';
				$output.='<input type="text" class="form-control" name="'.$this->plugin_post.'[system-collab-user]"/>';
				$output.='<p class="help-block">Ingresa el correo del usuario que deseas agregar a tu lista de colaboradores y selecci&oacute;nalo a continuaci&oacute;n.<br/>Se excluye de los colaboradores al due&ntilde;o (por defecto el creador) del sistema.</p>';
			$output.='<ul class="list-group" id="aa-new-element-list"></ul>';
		}
		$output.='</div>';
	$output.='</div>';
	$output.='<div class="form-group">';
		$output.='<div class="col-sm-2 control-label"></div>';
			$output.='<div class="col-sm-10">';
				$QS = http_build_query(array_merge($_GET, array("action"=>'')));
				$URL=htmlspecialchars("$_SERVER[PHP_SELF]?$QS");
				$output.='<a href="'.$URL.'" class="btn btn-primary">Terminar</a>';
			$output.='</div>';
		$output.='</div>';
	$output.='</div>';
	$output.='</form>';
	return $output;
}
public function fe_search_system_users(){
	global $SYSTEM;
	$system_id=$_POST['system_id'];
	$response=array();
	$text=$_POST['text'];
	$current_users=self::get_users($system_id);
	array_push($current_users,	$SYSTEM->get_single($system_id)['owner_id']);
	$args = array(
		'exclude'			=>$current_users,
		'number'			=>	10,
		'search'			=> '*'.esc_attr( $text ).'*',
		'search_columns'	=> array( 'user_login', 'user_email'),
	);
	$user_query = new WP_User_Query( $args );
	$user_list = $user_query->get_results();
//	$response['todo']=($user_list);
	$i=0;
	if ( ! empty( $user_list ) ) {
		$response['status'] = 'ok';
		foreach ( $user_list as $user ) {
			$response['users']['user_'.$user->ID]='<a href="#" data-user-id="'.$user->ID.'" data-system-id="'.$system_id.'" title="Agregar a '.$user->user_login.' a este sistema"><i class="fa fa-user-plus fa-fw fa-pull-right"></i> '.$user->user_email.'</a>';
			$i++;
		}
		$response['elementCount']=$i;
	} else {
		$response['error'] = true;
		$response['message']='<h4 class="list-group-item-heading">No hay elementos</h4>';
	}
	echo json_encode($response);
	die();
}
public function fe_add_system_users(){
	$insert_array=array(
		'system_id'	=>$_POST['system_id'],
		'user_id'	=>$_POST['user_id'],
	);
	$response=self::update_class_row('add',$insert_array);
	echo json_encode($response);
	die();
}
public function fe_remove_system_users(){
	$delete_array=array(
		'system_id'	=>$_POST['system_id'],
		'user_id'	=>$_POST['user_id'],
	);
	$response=self::update_class_row('delete',$delete_array);
	echo json_encode($response);
	die();
}


//---------------------------------------------------------------------------------------------------------------------------------------------------------
//END OF CLASS	
}

global $SYSTEM_COLLAB;
$SYSTEM_COLLAB =new SYSTEM_COLLAB_CLASS();
?>