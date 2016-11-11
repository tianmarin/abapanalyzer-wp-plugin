<?php
defined('ABSPATH') or die("No script kiddies please!");

class SYSTEM_CLASS extends AA_CLASS{

/**
* Esta función es llamada apenas se crea la clase.
* Es utilizada para instanciar las diferentes clases con su información vital.
*
*/
public function __construct(){
	global $wpdb;
	global $aa_vars;
	//como se definió en aa_vars
	$this->class_name	= 'system';
	//Nombre singular para títulos, mensajes a usuario, etc.
	$this->name_single	= 'Sistema';
	//Nombre plural para títulos, mensajes a usuario, etc.
	$this->name_plural	= 'Sistemas';
	//Identificador de menú padre
	$this->parent_slug	= $aa_vars['main_menu_slug'];
	//Identificador de submenú de la clase
	$this->menu_slug	= $aa_vars[$this->class_name.'_menu_slug'];
	//Utilizadp para validaciones
	$this->plugin_post	= $aa_vars['plugin_post'];
	//Permisos de usuario a nivel de backend WordPRess
	$this->capability	= $aa_vars[$this->class_name.'_menu_cap'];
	//Tabla de la clase
	$this->tbl_name		= $wpdb->prefix.$aa_vars[$this->class_name.'_tbl_name'];
	//Versión de DB (para registro y actualización automática)
	$this->db_version	= '1.0';
	//Reglas actuales de caracteres a nivel de DB.
	//Dado que esto sólo se usa en la cración de la tabla
	//no se guarda como variable de clase.
	$charset_collate	= $wpdb->get_charset_collate();
	//Sentencia SQL de creación (y ajuste) de la tabla de la clase
	$this->crt_tbl_sql	=	"CREATE TABLE ".$this->tbl_name." (
								id tinyint(1) unsigned not null auto_increment,
								sid varchar(30) not null,
								short_name varchar(30) null,
								creator_id bigint(20) not null,
								creation_datetime datetime not null default current_timestamp,
								owner_id bigint(20) not null,
								collab_opt_id tinyint(1) unsigned not null,
								UNIQUE KEY id (id)
							) $charset_collate;";
	$this->db_fields	= array(
		/*
		field_name		:	Nombre del campo a nivel de DB
		field_type		:	Tipo de Dato para validacion
							- id
							- text
							- percentage
							- number
							- nat_number
							- timestamp
							- date
							- time
							- bool
							- radio
							- select
		options			:	Value for options
							key => arraay{val,disabled)
		disabled		:	just to show information
							disabled
							form
							static (as bootstrap form-control-static)
		placeholder		:	Nombre para poner en los formatos
		min				:	valor numérico mínimo
		max				:	valor numérico máximo
		in_form			:	Flag de mostrar el campo en los formatos
							true|false
		in_table		:	Flag de mostrar el campo en las tablas
							true|false
		table_
		form_size		:	Form input size (bootstrap in form-group class)
							form-group-lg
							null
							form-group-sm
		required		:	Flag de obligatoriedad del dato (NOT NULL)
							true|false
		maxchar			:	Máximo número de caracters (valido solo para inputs tipo texto)
							- número
							- null (es indefinido)
		field_desc		:	Descripción del campo utilizado en formualrios y mensajes al usuario
		*/
		'id' => array(
			'type'			=>'id',
			'required'		=>true,
			'maxchar'		=>null,
			'desc'			=>'id',
			'form-help'		=>'',
			'in_form'		=>false,
			'in_wp_table'	=>true,
		),
		'sid' => array(
			'type'			=>'text',
			'required'		=>true,
			'maxchar'		=>3,
			'desc'			=>'SID',
			'form-help'		=>'El identificador del sistema tambi&eacute;n es utilizado en los gr&aacute;ficos y validado contra archivos de configuraci&oacute;n.',
			'in_wp_table'	=>true,
			'wp_table_lead'	=>true,
//			'form_size'		=>'form-group-lg',
		),
		'short_name' => array(
			'type'			=>'text',
			'required'		=>false,
			'maxchar'		=>30,
			'desc'			=>'Nombre Corto',
			'form-help'		=>'El nombre corto permite identificar el sistema con una personalizaci&oacute;n diferente al SID. <br/>Por ejemplo, para sistemas CLON.<br/>Tamaño m&aacute;ximo: 30 caracteres.',
			'in_wp_table'	=>true,
		),
		'creator_id' => array(
			'type'			=>'current_user_id',
			'required'		=>false,
			'maxchar'		=>null,
			'desc'			=>'Usuario creador',
			'form-help'		=>'',
			'in_form'		=>false,
			'in_wp_table'	=>true,
			'in_wp_table'	=>true,
			'sp_wp_table'	=>true,
		),
		'owner_id' => array(
			'type'			=>'current_user_id',
			'required'		=>false,
			'maxchar'		=>null,
			'desc'			=>'Usuario due&ntilde;o',
			'form-help'		=>'',
			'in_form'		=>false,
			'in_wp_table'	=>true,
			'sp_wp_table'	=>true,
		),
		'creation_datetime' => array(
			'type'			=>'timestamp',
			'required'		=>false,
			'maxchar'		=>60,
			'desc'			=>'Registro de creación',
			'form-help'		=>null,
			'in_form'		=>false,
			'in_wp_table'	=>false,
		),
		'collab_opt_id' => array(
			'type'			=>'select',
			'options'		=> array(),
			'source'		=>'',
			'required'		=>true,
			'desc'			=>'Opci&oacute;n de colaboraci&oacute;n',
			'form-help'		=>'Esta opci&oacute;n permite que diferentes usuarios (<mark>nadie</mark>, <mark>algunos</mark> o <mark>todos</mark>) puedan incluir informaci&oacute;n t&eacute;cnica dentro del sistema.',
			'in_form'		=>true,
			'sp_form'		=>true,
			'in_wp_table'	=>true,
			'sp_wp_table'	=>true,
		),
	);
	register_activation_hook(WP_PLUGIN_DIR."/abap_analyzer/"."index.php", array( $this, 'db_install') );
	add_action('admin_menu', 						array( $this , "register_submenu_page"		));
	add_action( 'wp_ajax_search_system_users', 		array( $this , 'search_system_users'		));
	add_action( 'wp_ajax_fe_system_list',			array( $this , 'fe_system_list'				));
	add_action( 'wp_ajax_fe_system_info',			array( $this , 'fe_system_info'				));
}
protected function sp_wp_table_creator_id($user_id){
	$user_info = get_userdata($user_id);
      return  $user_info->user_login.'<br/><small>('.$user_info->user_email.')</small>';
}
protected function sp_wp_table_owner_id($user_id){
	$user_info = get_userdata($user_id);
      return  $user_info->user_login.'<br/><small>('.$user_info->user_email.')</small>';
}
protected function sp_wp_table_collab_opt_id($opt,$id){
	$response="Error";
	if($opt == NULL || $opt == 1){
		//none
		$response ='<div class="text-center"><i class="fa fa-user-times fa-fw" aria-hidden="true"></i></div>';
	}elseif($opt == 2){
		global $SYSTEM_COLLAB;
		$collab=$SYSTEM_COLLAB->get_users($id);
		//some
		$response ='<div class="text-center"><i class="fa fa-user-plus fa-fw" aria-hidden="true"></i>';
		$QS = http_build_query(array_merge($_GET, array("action"=>$this->class_name.'_collab',"item"=>$id)));
		$URL=htmlspecialchars("$_SERVER[PHP_SELF]?$QS");
		$response.='<a href="'.$URL.'" class="">Modificar</a>';
		$response.='';
		$response.='<br/><small>('.sizeof($collab).' colaboradores)</small></div>';
	}elseif($opt == 3){
		//all
		$response ='<div class="text-center"><i class="fa fa-users fa-fw fa-2x" aria-hidden="true"></i></div>';
	}
	return $response;
}
protected function system_collab(){
	global $SYSTEM_COLLAB;
	$id=$_GET['item'];
	return $SYSTEM_COLLAB->special_form($id);
}
protected function sp_form_collab_opt_id(){
	global $COLLAB_OPT;
	$response = array();
	foreach($COLLAB_OPT->get_all() as $key => $value){
		$response[$value['id']] = $value['short_name'];
	}
	return $response;
//	$this->db_fields['collab_opt_id']['options'] = $COLLAB_OPT->get_all();
}
public function fe_system_list(){
	$response=array();
	$response['data']=array();
	$i=0;
//	$response['all']=$this->get_all();
	foreach( $this->get_all() as $key => $value){
		$response['data']['elem_'.$key]=array();
		$response['data']['elem_'.$key]['id']=$value['id'];
		$response['data']['elem_'.$key]['sid']=$value['sid'];
		$response['data']['elem_'.$key]['shortName']=$value['short_name'];
		$response['data']['elem_'.$key]['owner']=get_userdata($value['owner_id'])->user_email;
		$i++;
	}
	$response['elementCount']=$i;
	$response['status']='ok';
	echo json_encode($response);
	die();	
}
public function fe_system_info(){
	$response=array();
	$response['system']=array();
	$system=$this->get_single($_POST['system_id']);
	$response['system']['id']=$system['id'];
	$response['system']['sid']=$system['sid'];
	$response['system']['shortName']=$system['short_name'];
	$response['system']['collab']='<div class="collab"><h2>Colaboradores</h2>';
	if($system['collab_opt_id'] == NULL || $system['collab_opt_id'] == 1){
		$response['system']['collab'].='<p>';
//		$response['system']['collab'].='<i class="fa fa-user-times fa-fw" aria-hidden="true"></i>';
		$response['system']['collab'].='Este sistema no tiene colaboradores.';
		$response['system']['collab'].='</p>';
	}elseif($system['collab_opt_id'] == 2){
		$response['system']['collab'].='<p>';
//		$response['system']['collab'].='<i class="fa fa-user-times fa-fw" aria-hidden="true"></i>';
		global $SYSTEM_COLLAB;
		$collab=$SYSTEM_COLLAB->get_users($system['id']);
		$response['system']['collab'].='Este sistema tiene '.sizeof($collab).' colaboradores.';
		$response['system']['collab'].='</p>';
		
	}else{
		$response['system']['collab'].='<p>';
//		$response['system']['collab']='<i class="fa fa-users fa-fw fa-2x" aria-hidden="true"></i>';
		$response['system']['collab'].='Este sistema permite que todos los usuarios agreguen informaci&oacute;n.';
		$response['system']['collab'].='</p>';
		
	}
	$response['system']['collab'].='</div>';
	$response['dataSuppliers']=array();
	$response['dataSuppliers']['sdfmon']=array();
	$response['dataSuppliers']['sdfmon']['title']="Snapshot Monitoring";
	$response['dataSuppliers']['sdfmon']['firstDate']="23/08/2016";
	$response['dataSuppliers']['sdfmon']['lastDate']="12/11/2016";
	$response['dataSuppliers']['sdfmon']['editLink']="#sdfmon-setup/".$system['id'];
	$response['dataSuppliers']['sdfmon']['editText']="Modificar";
	$response['status']='ok';
	$response['reports']=array();
	global $REPORT;
	$report_list=$REPORT->get_reports_by_system($system['id']);
	foreach($report_list as $report_id){
		$report=$REPORT->get_single($report_id);
		$response['reports']['rep_'.$report_id]=array();
		$response['reports']['rep_'.$report_id]['id']=$report_id;
		$response['reports']['rep_'.$report_id]['shortName']=$report['short_name'];
		$response['reports']['rep_'.$report_id]['editLink']="#edit-report/".$report_id;
		$response['reports']['rep_'.$report_id]['editText']="Modificar";
		$response['reports']['rep_'.$report_id]['viewLink']="#report-preview/".$report_id;
		$response['reports']['rep_'.$report_id]['viewText']="Visualizar";
	}
	echo json_encode($response);
	die();	
	
}
//END OF CLASS	
}

global $SYSTEM;
$SYSTEM =new SYSTEM_CLASS();



//add_action( 'admin_notices', array( $SYSTEM, 'db_install_error')  );
?>