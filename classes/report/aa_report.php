<?php
defined('ABSPATH') or die("No script kiddies please!");

class REPORT_CLASS extends AA_CLASS{

/**
* Esta función es llamada apenas se crea la clase.
* Es utilizada para instanciar las diferentes clases con su información vital.
*
*/
public function __construct(){
	global $wpdb;
	global $aa_vars;
	//como se definió en aa_vars
	$this->class_name	= 'report';
	//Nombre singular para títulos, mensajes a usuario, etc.
	$this->name_single	= 'Reporte';
	//Nombre plural para títulos, mensajes a usuario, etc.
	$this->name_plural	= 'Reportes';
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
								short_name varchar(30) not null,
								system_id tinyint(1) not null,
								report_type_id tinyint(1) not null,
								creator_id bigint(20) not null,
								creation_datetime datetime not null default current_timestamp,
								owner_id bigint(20) not null,
								start_date date not null,
								end_date date not null,
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
		'short_name' => array(
			'type'			=>'text',
			'required'		=>false,
			'maxchar'		=>30,
			'desc'			=>'Nombre Corto',
			'form-help'		=>'El nombre corto permite identificar el sistema con una personalizaci&oacute;n diferente al SID. <br/>Por ejemplo, para sistemas CLON.<br/>Tamaño m&aacute;ximo: 30 caracteres.',
			'in_wp_table'	=>true,
			'wp_table_lead'	=>true,
		),
		'system_id' => array(
			'type'			=>'select',
			'options'		=> array(),
			'required'		=>true,
			'desc'			=>'Sistema',
			'form-help'		=>'',
			'in_wp_table'	=>true,
			'sp_wp_table'	=>true,
		),
		'report_type_id' => array(
			'type'			=>'select',
			'options'		=> array(),
			'required'		=>true,
			'desc'			=>'Tipo de Reporte',
			'form-help'		=>'',
			'in_wp_table'	=>true,
			'sp_wp_table'	=>true,
		),
		'creator_id' => array(
			'type'			=>'current_user_id',
			'required'		=>false,
			'desc'			=>'Usuario creador',
			'in_wp_table'	=>false,
			'in_form'		=>false,
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
		'owner_id' => array(
			'type'			=>'current_user_id',
			'required'		=>false,
			'maxchar'		=>null,
			'desc'			=>'Usuario due&ntilde;o',
			'form-help'		=>'',
			'in_form'		=>false,
			'in_wp_table'	=>false,
		),
		'start_date' => array(
			'type'			=>'date',
			'required'		=>true,
			'desc'			=>'Fecha de Inicio',
			'form-help'		=>'',
			'in_form'		=>true,
			'in_wp_table'	=>false,
		),
		'end_date' => array(
			'type'			=>'date',
			'required'		=>true,
			'desc'			=>'Fecha de Fin',
			'form-help'		=>'',
			'in_form'		=>true,
			'in_wp_table'	=>false,
		),
		'collab_opt_id' => array(
			'type'			=>'select',
			'options'		=> array(),
			'required'		=>true,
			'desc'			=>'Opci&oacute;n de colaboraci&oacute;n',
			'form-help'		=>'Esta opci&oacute;n permite que diferentes usuarios (<mark>nadie</mark>, <mark>algunos</mark> o <mark>todos</mark>) puedan incluir informaci&oacute;n t&eacute;cnica dentro del sistema.',
			'in_form'		=>true,
			'sp_form'		=>true,
			'in_wp_table'	=>true,
			'sp_wp_table'	=>true,
		),
	);
//	echo plugin_dir_path(__FILE__)."index.php";
	register_activation_hook(WP_PLUGIN_DIR."/abap_analyzer/"."index.php", array( $this, 'db_install') );
//	register_activation_hook(plugin_dir_path(__FILE__)."index.php", array( $this, 'db_install') );
	add_action('admin_menu', 						array( $this , "register_submenu_page"		));
	add_action( 'wp_ajax_search_report_users', 		array( $this , 'search_report_users'		));
	add_action( 'wp_ajax_fe_preview_report',		array( $this , 'fe_preview_report'			));
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
		global $REPORT_COLLAB;
		$collab=$REPORT_COLLAB->get_users($id);
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
public function get_reports_by_system($system_id){
	$report_list=array();
	if($system_id == null){
		return false;
	}
	$sql="SELECT id FROM ".$this->tbl_name.' WHERE system_id="'.$system_id.'"';
	foreach( self::get_sql($sql) as $key => $val){
		array_push($report_list, $val['id']);
	}
	return $report_list;
	
}
protected function report_collab(){
	global $REPORT_COLLAB;
	$id=$_GET['item'];
	return $REPORT_COLLAB->special_form($id);
}
protected function sp_form_system_id(){
	global $SYSTEM;
	$response = array();
	foreach($SYSTEM->get_all() as $key => $value){
		$response[$value['id']] = $value['sid'].' ('.$value['short_name'].')';
	}
	return $response;
//	$this->db_fields['collab_opt_id']['options'] = $COLLAB_OPT->get_all();
}
protected function sp_wp_table_system_id($id){
    global $SYSTEM;
    $response = $SYSTEM->get_single($id);
    return $response['sid'].' ('.$response['short_name'].')';
}

protected function sp_form_report_type_id(){
	global $REPORT_TYPE;
	$response = array();
	foreach($REPORT_TYPE->get_all() as $key => $value){
		$response[$value['id']] = $value['short_name'];
	}
	return $response;
//	$this->db_fields['collab_opt_id']['options'] = $COLLAB_OPT->get_all();
}
protected function sp_wp_table_report_type_id($id){
    global $REPORT_TYPE;
    $response = $REPORT_TYPE->get_single($id);
    return $response['short_name'];
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
/*
* fe_preview_report
*
* This is it!
* This functions is the most important and the main purpose of this WP Plugin.
*
* It generates the json structure of the report (according to its report_type).
*
*
* @author: Cristian Marin
*/
public function fe_preview_report(){
	$response=array();
	$report_id=$_POST['report_id'];
	$report=self::get_single($report_id);
	$response['report']=$report;
	global $REPORT_TYPE_SECTION;
	$sections=$REPORT_TYPE_SECTION->get_sections($report['report_type_id']);
	$response['sections']=array();
	global $SECTION;
	foreach($sections as $section_id){
		$section=$SECTION->get_single($section_id);
		global $SECTION_CHART;
		$charts=$SECTION_CHART->get_charts($section_id);
		$section['charts']=array();
		global $CHART;
		foreach($charts as $chart_id){
			$chart=$CHART->get_single($chart_id);
			$section['charts']['chart_'.$chart_id]=$chart;
		}
		$response['sections']['section_'.$section_id]=$section;
	}
//	self::write_log($response);
	echo json_encode($response);
	die();	

}
//END OF CLASS	
}

global $REPORT;
$REPORT =new REPORT_CLASS();



//add_action( 'admin_notices', array( $REPORT, 'db_install_error')  );
?>