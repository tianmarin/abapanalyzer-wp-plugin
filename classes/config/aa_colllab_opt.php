<?php
defined('ABSPATH') or die("No script kiddies please!");

class AA_COLLAB_OPT_CLASS extends AA_CLASS{

/**
* Esta función es llamada apenas se crea la clase.
* Es utilizada para instanciar las diferentes clases con su información vital.
*
*/
function __construct(){
	global $wpdb;
	global $aa_vars;
	//como se definió en aa_vars
	$this->class_name	= 'collab_opt';
	//Nombre singular para títulos, mensajes a usuario, etc.
	$this->name_single	= 'Opci&oacute;n de Colaboraci&oacute;n';
	//Nombre plural para títulos, mensajes a usuario, etc.
	$this->name_plural	= 'Opciones de Colaboraci&oacute;n';
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
	$this->db_version	= '1';
	//Reglas actuales de caracteres a nivel de DB.
	//Dado que esto sólo se usa en la cración de la tabla
	//no se guarda como variable de clase.
	$charset_collate	= $wpdb->get_charset_collate();
	//Sentencia SQL de creación (y ajuste) de la tabla de la clase
	$this->crt_tbl_sql	=	"CREATE TABLE ".$this->tbl_name." (
								id tinyint(1) unsigned not null auto_increment,
								code varchar(4) not null,
								short_name varchar(30) null,
								UNIQUE KEY id (id)
							) $charset_collate;";
	//Registro de columnas de la tabla utilizado para validaciones y visualización de formatos
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
		'code' => array(
			'type'			=>'text',
			'required'		=>true,
			'desc'			=>'C&oacute;digo',
			'form-help'		=>'',
			'maxchar'		=>4,
			'in_wp_table'	=>true,
			'wp_table_lead'	=>true,
		),
		'short_name' => array(
			'type'			=>'text',
			'required'		=>false,
			'maxchar'		=>30,
			'desc'			=>'Nombre Corto',
			'form-help'		=>'Nombre amigable para desplegar al usuario.<br/>Tamaño m&aacute;ximo: 30 caracteres.',
			'in_wp_table'	=>true,
		),
	);
//	echo plugin_dir_path(__FILE__)."index.php";
//	echo WP_PLUGIN_DIR."/abap_analyzer/"."index.php";
	register_activation_hook(WP_PLUGIN_DIR."/abap_analyzer/"."index.php", array( $this, 'db_install') );
	register_activation_hook(WP_PLUGIN_DIR."/abap_analyzer/"."index.php", array( $this, 'db_install_data') );
//	register_activation_hook(plugin_dir_path(__FILE__)."index.php", array( $this, 'db_install') );
//	add_action('admin_menu', array( $this , "register_submenu_page" ) );
}
public function db_install_data(){
	global $wpdb;
	$count =intval($wpdb->get_var( "SELECT COUNT(*) FROM ".$this->tbl_name));
	if($count == 0){
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'			=> 1,
				'code'			=> 'none',
				'short_name'	=> 'Solo yo',
			) 
		);	
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'			=> 2,
				'code'			=> 'some',
				'short_name'	=> 'Colaboradores',
			) 
		);	
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'			=> 3,
				'code'			=> 'all',
				'short_name'	=> 'Toda la organizaci&oacute;n',
			) 
		);
	}
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
//END OF CLASS	
}

global $COLLAB_OPT;
$COLLAB_OPT =new AA_COLLAB_OPT_CLASS();
?>