<?php
defined('ABSPATH') or die("No script kiddies please!");

class GRAPH_CLASS extends AA_CLASS{

/**
* Esta función es llamada apenas se crea la clase.
* Es utilizada para instanciar las diferentes clases con su información vital.
*
*/
public function __construct(){
	global $wpdb;
	global $aa_vars;
	//como se definió en aa_vars
	$this->class_name	= 'graph';
	//Nombre singular para títulos, mensajes a usuario, etc.
	$this->name_single	= 'Gr&aacute;fico';
	//Nombre plural para títulos, mensajes a usuario, etc.
	$this->name_plural	= 'Gr&aacute;ficos';
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
								name varchar(80) null,
								asset_id tinyint(1) unsigned not null,
								graph_function_id tinyint(1) unsigned not null,
								graph_type_id tinyint(1) unsigned not null,
								graph_color_id tinyint(1) unsigned not null,
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
			'form-help'		=>'El nombre corto es el mejor identificador del cada gráfico en el sistema.<br/>Tamaño m&aacute;ximo: 30 caracteres.',
			'in_wp_table'	=>true,
			'wp_table_lead'	=>true,
		),
		'name' => array(
			'type'			=>'text',
			'required'		=>false,
			'maxchar'		=>80,
			'desc'			=>'Nombre Completo',
			'form-help'		=>'<br/>Tamaño m&aacute;ximo: 80 caracteres.',
			'in_wp_table'	=>false,
		),
		'asset_id' => array(
			'type'			=>'select',
			'options'		=> array(),
			'source'		=>'',
			'required'		=>true,
			'desc'			=>'Elemento',
			'form-help'		=>'',
			'in_form'		=>true,
			'sp_form'		=>true,
			'in_wp_table'	=>true,
			'sp_wp_table'	=>true,
		),
		'graph_function_id' => array(
			'type'			=>'select',
			'options'		=> array(),
			'source'		=>'',
			'required'		=>true,
			'desc'			=>'Tipo de Funci&oacute;n',
			'form-help'		=>'',
			'in_form'		=>true,
			'sp_form'		=>true,
			'in_wp_table'	=>true,
			'sp_wp_table'	=>true,
		),
		'graph_type_id' => array(
			'type'			=>'select',
			'options'		=> array(),
			'source'		=>'',
			'required'		=>true,
			'desc'			=>'Tipo de Gr&aacute;fico',
			'form-help'		=>'',
			'in_form'		=>true,
			'sp_form'		=>true,
			'in_wp_table'	=>true,
			'sp_wp_table'	=>true,
		),
		'graph_color_id' => array(
			'type'			=>'select',
			'options'		=> array(),
			'source'		=>'',
			'required'		=>true,
			'desc'			=>'Color',
			'form-help'		=>'',
			'in_form'		=>true,
			'sp_form'		=>true,
			'in_wp_table'	=>true,
			'sp_wp_table'	=>true,
		),
	);
	register_activation_hook(WP_PLUGIN_DIR."/abap_analyzer/"."index.php", array( $this, 'db_install') );
	add_action('admin_menu', array( $this , "register_submenu_page" ) );
}
protected function sp_wp_table_asset_id($id){
	global $ASSET;
	$response = $ASSET->get_single($id);
	return $response['short_name'];
}
protected function sp_form_asset_id(){
	global $ASSET;
	$response = array();
	foreach($ASSET->get_all() as $key => $value){
		$response[$value['id']] = $value['short_name'];
	}
	return $response;
}
protected function sp_wp_table_graph_function_id($id){
	global $GRAPH_FUNCTION;
	$response = $GRAPH_FUNCTION->get_single($id);
	return $response['short_name'];
}
protected function sp_form_graph_function_id(){
	global $GRAPH_FUNCTION;
	$response = array();
	foreach($GRAPH_FUNCTION->get_all() as $key => $value){
		$response[$value['id']] = $value['short_name'];
	}
	return $response;
}
protected function sp_wp_table_graph_type_id($id){
	global $GRAPH_TYPE;
	$response = $GRAPH_TYPE->get_single($id);
	switch($response['code']){
		case 'line':
			$icon ='<i class="fa fa-line-chart fa-fw" aria-hidden="true"></i>';
			break;
		case 'column':
			$icon ='<i class="fa fa-bar-chart fa-fw" aria-hidden="true"></i>';
			break;
		case 'step':
			$icon ='<i class="fa fa-bar-chart fa-fw" aria-hidden="true"></i>';
			break;
		case 'smoothedLine':
			$icon ='<i class="fa fa-area-chart fa-fw" aria-hidden="true"></i>';
			break;
	}
	return $icon.' '.$response['short_name'];
}
protected function sp_form_graph_type_id(){
	global $GRAPH_TYPE;
	$response = array();
	foreach($GRAPH_TYPE->get_all() as $key => $value){
		$response[$value['id']] = $value['short_name'];
	}
	return $response;
}
protected function sp_wp_table_graph_color_id($id){
	global $GRAPH_COLOR;
	$response = $GRAPH_COLOR->get_single($id);
	return '<span style="color:#'.$response['hex'].';" >'.$response['short_name']."</span>";
}
protected function sp_form_graph_color_id(){
	global $GRAPH_COLOR;
	$response = array();
	foreach($GRAPH_COLOR->get_all() as $key => $value){
		$response[$value['id']] = $value['short_name'];
	}
	return $response;
}
//END OF CLASS	
}

global $GRAPH;
$GRAPH =new GRAPH_CLASS();
//add_action( 'admin_notices', array( $SYSTEM, 'db_install_error')  );
?>