<?php
defined('ABSPATH') or die("No script kiddies please!");

class SECTION_CLASS extends AA_CLASS{

/**
* Esta función es llamada apenas se crea la clase.
* Es utilizada para instanciar las diferentes clases con su información vital.
*
*/
public function __construct(){
	global $wpdb;
	global $aa_vars;
	//como se definió en aa_vars
	$this->class_name	= 'section';
	//Nombre singular para títulos, mensajes a usuario, etc.
	$this->name_single	= 'Secci&oacute;n';
	//Nombre plural para títulos, mensajes a usuario, etc.
	$this->name_plural	= 'Secciones';
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
								short_name varchar(15) null,
								title varchar(30) null,
								intro text null,
								outro text null,
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
			'maxchar'		=>15,
			'desc'			=>'Nombre Corto',
			'form-help'		=>'El nombre corto permite identificar la secci&oacute;n.<br/>Tamaño m&aacute;ximo: 15 caracteres.',
			'in_wp_table'	=>true,
			'wp_table_lead'	=>true,
		),
		'title' => array(
			'type'			=>'text',
			'required'		=>false,
			'maxchar'		=>30,
			'desc'			=>'T&iacute;tulo',
			'form-help'		=>'El texto que será desplegado como cabecera de la secci&oaute;n.<br/>Tamaño m&aacute;ximo: 30 caracteres.',
			'in_wp_table'	=>true,
		),
		'intro' => array(
			'type'			=>'textarea',
			'required'		=>false,
			'desc'			=>'Introdduci&oacute;n',
			'form-help'		=>'',
			'in_wp_table'	=>false,
		),
		'outro' => array(
			'type'			=>'textarea',
			'required'		=>false,
			'desc'			=>'Texto de Cierre',
			'form-help'		=>'',
			'in_wp_table'	=>false,
		),
		'chart' => array(
			'type'			=>'display',
			'required'		=>false,
			'desc'			=>'Charts',
			'in_form'		=>false,
			'in_wp_table'	=>true,
			'sp_wp_table'	=>true,
		),
	);
	register_activation_hook(WP_PLUGIN_DIR."/abap_analyzer/"."index.php", array( $this, 'db_install') );
	add_action('admin_menu', array( $this , "register_submenu_page" ) );
}
protected function sp_wp_table_chart($value=null,$id=null){
	global $SECTION_CHART;
	$charts=$SECTION_CHART->get_charts($id);
	$response ='';
	$QS = http_build_query(array_merge($_GET, array("action"=>$this->class_name.'_chart',"item"=>$id)));
	$URL=htmlspecialchars("$_SERVER[PHP_SELF]?$QS");
	$response.='<a href="'.$URL.'" class="">Modificar</a>';
	$response.='';
	$response.='<br/><small>('.sizeof($charts).' charts)</small></div>';
	return $response;
}

protected function section_chart(){
	global $SECTION_CHART;
	$id=$_GET['item'];
	return $SECTION_CHART->special_form($id);
}
//END OF CLASS	
}

global $SECTION;
$SECTION =new SECTION_CLASS();

?>