<?php
defined('ABSPATH') or die("No script kiddies please!");

class AA_REPORT_TYPE_CLASS extends AA_CLASS{

/**
* Esta función es llamada apenas se crea la clase.
* Es utilizada para instanciar las diferentes clases con su información vital.
*
*/
public function __construct(){
	global $wpdb;
	global $aa_vars;
	//como se definió en aa_vars
	$this->class_name	= 'report_type';
	//Nombre singular para títulos, mensajes a usuario, etc.
	$this->name_single	= 'Tipo de Reporte';
	//Nombre plural para títulos, mensajes a usuario, etc.
	$this->name_plural	= 'Tipos de Reporte';
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
								short_name varchar(30) null,
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
			'required'		=>true,
			'maxchar'		=>30,
			'desc'			=>'Nombre Corto',
			'form-help'		=>'El nombre corto permite identificar la secci&oacute;n.<br/>Tamaño m&aacute;ximo: 30 caracteres.',
			'in_wp_table'	=>true,
			'wp_table_lead'	=>true,
		),
		'section' => array(
			'type'			=>'display',
			'required'		=>false,
			'desc'			=>'Secciones',
			'in_form'		=>false,
			'in_wp_table'	=>true,
			'sp_wp_table'	=>true,
		),
	);
	register_activation_hook(WP_PLUGIN_DIR."/abap_analyzer/"."index.php", array( $this, 'db_install') );
	register_activation_hook(WP_PLUGIN_DIR."/abap_analyzer/"."index.php", array( $this, 'db_install_data') );
	add_action('admin_menu', array( $this , "register_submenu_page" ) );
}
protected function sp_wp_table_section($value=null,$id=null){
	global $AA_REPORT_TYPE_SECTION;
	$sections=$AA_REPORT_TYPE_SECTION->get_sections($id);
	$response ='';
	$QS = http_build_query(array_merge($_GET, array("action"=>$this->class_name.'_section',"item"=>$id)));
	$URL=htmlspecialchars('?'.$QS);
	$response.='<a href="'.$URL.'" class="">Modificar</a>';
	$response.='';
	$response.='<br/><small>('.sizeof($sections).' secciones)</small></div>';
	return $response;
}

protected function report_type_section(){
	global $AA_REPORT_TYPE_SECTION;
	$id=$_GET['item'];
	return $AA_REPORT_TYPE_SECTION->special_form($id);
}
public function db_install_data(){
	global $wpdb;
	$count =intval($wpdb->get_var( "SELECT COUNT(*) FROM ".$this->tbl_name));
	if($count == 0){
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'					=> 1,
				'short_name'			=> 'Estilo 1',
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'					=> 2,
				'short_name'			=> 'Estilo 2',
			) 
		);
	}
}

//END OF CLASS	
}

global $AA_REPORT_TYPE;
$AA_REPORT_TYPE =new AA_REPORT_TYPE_CLASS();

?>