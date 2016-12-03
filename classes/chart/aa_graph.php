<?php
defined('ABSPATH') or die("No script kiddies please!");

class AA_GRAPH_CLASS extends AA_CLASS{

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
	register_activation_hook(WP_PLUGIN_DIR."/abap_analyzer/"."index.php", array( $this, 'db_install_data') );
	add_action('admin_menu', array( $this , "register_submenu_page" ) );
}
protected function sp_wp_table_asset_id($id){
	global $AA_ASSET;
	$response = $AA_ASSET->get_single($id);
	return $response['short_name'];
}
protected function sp_form_asset_id(){
	global $AA_ASSET;
	$response = array();
	foreach($AA_ASSET->get_all() as $key => $value){
		$response[$value['id']] = $value['short_name'];
	}
	return $response;
}
protected function sp_wp_table_graph_function_id($id){
	global $AA_GRAPH_FUNCTION;
	$response = $AA_GRAPH_FUNCTION->get_single($id);
	return $response['short_name'];
}
protected function sp_form_graph_function_id(){
	global $AA_GRAPH_FUNCTION;
	$response = array();
	foreach($AA_GRAPH_FUNCTION->get_all() as $key => $value){
		$response[$value['id']] = $value['short_name'];
	}
	return $response;
}
protected function sp_wp_table_graph_type_id($id){
	global $AA_GRAPH_TYPE;
	$response = $AA_GRAPH_TYPE->get_single($id);
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
	global $AA_GRAPH_TYPE;
	$response = array();
	foreach($AA_GRAPH_TYPE->get_all() as $key => $value){
		$response[$value['id']] = $value['short_name'];
	}
	return $response;
}
protected function sp_wp_table_graph_color_id($id){
	global $AA_GRAPH_COLOR;
	$response = $AA_GRAPH_COLOR->get_single($id);
	return '<span style="color:#'.$response['hex'].';" >'.$response['short_name']."</span>";
}
protected function sp_form_graph_color_id(){
	global $AA_GRAPH_COLOR;
	$response = array();
	foreach($AA_GRAPH_COLOR->get_all() as $key => $value){
		$response[$value['id']] = $value['short_name'];
	}
	return $response;
}
public function db_install_data(){
	global $wpdb;
	$count =intval($wpdb->get_var( "SELECT COUNT(*) FROM ".$this->tbl_name));
	if($count == 0){
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 1,
				'short_name'		=> 'MAX ACT WPS',
				'name'				=> 'Max. Active WPs',
				'asset_id'			=> 1,
				'graph_function_id'	=> 2,
				'graph_type_id'		=> 4,
				'graph_color_id'	=> 1,
			) 
		);

		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 2,
				'short_name'		=> 'AVG ACT WPs',
				'name'				=> 'Avg. Active WPs',
				'asset_id'			=> 1,
				'graph_function_id'	=> 3,
				'graph_type_id'		=> 4,
				'graph_color_id'	=> 2,
			) 
		);

		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 3,
				'short_name'		=> 'MAX DIA ACT WPs',
				'name'				=> 'Max. Active DIA WPs',
				'asset_id'			=> 2,
				'graph_function_id'	=> 2,
				'graph_type_id'		=> 4,
				'graph_color_id'	=> 1,
			) 
		);

		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 4,
				'short_name'		=> 'AVG DIA ACT WPs',
				'name'				=> 'Avg. Active DIA WPs',
				'asset_id'			=> 2,
				'graph_function_id'	=> 3,
				'graph_type_id'		=> 4,
				'graph_color_id'	=> 2,
			) 
		);

		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 5,
				'short_name'		=> 'AVG RFC WPs',
				'name'				=> 'Avg. Available RFC WPs',
				'asset_id'			=> 3,
				'graph_function_id'	=> 3,
				'graph_type_id'		=> 4,
				'graph_color_id'	=> 2,
			) 
		);

		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 6,
				'short_name'		=> 'MIN RFC WPs',
				'name'				=> 'Min. Available RFC WPs',
				'asset_id'			=> 3,
				'graph_function_id'	=> 1,
				'graph_type_id'		=> 4,
				'graph_color_id'	=> 3,
			) 
		);

		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 7,
				'short_name'		=> 'MAX DIA QUEUE',
				'name'				=> 'Max. Queue DIA Lenght',
				'asset_id'			=> 18,
				'graph_function_id'	=> 2,
				'graph_type_id'		=> 4,
				'graph_color_id'	=> 1,
			) 
		);

		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 8,
				'short_name'		=> 'AVG DIA QUEUE',
				'name'				=> 'Avg. Queue DIA Lenght',
				'asset_id'			=> 18,
				'graph_function_id'	=> 3,
				'graph_type_id'		=> 4,
				'graph_color_id'	=> 2,
			) 
		);

		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 9,
				'short_name'		=> 'MAX UPD QUEUE',
				'name'				=> 'Max. Queue UPD Lenght',
				'asset_id'			=> 19,
				'graph_function_id'	=> 2,
				'graph_type_id'		=> 4,
				'graph_color_id'	=> 1,
			) 
		);

		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 10,
				'short_name'		=> 'AVG UPD QUEUE',
				'name'				=> 'Avg. Queue UPD Lenght',
				'asset_id'			=> 19,
				'graph_function_id'	=> 3,
				'graph_type_id'		=> 4,
				'graph_color_id'	=> 2,
			) 
		);

		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 11,
				'short_name'		=> 'MAX SESSIONS',
				'name'				=> 'Max. Sessions',
				'asset_id'			=> 22,
				'graph_function_id'	=> 2,
				'graph_type_id'		=> 4,
				'graph_color_id'	=> 1,
			) 
		);

		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 12,
				'short_name'		=> 'AVG SESSIONS',
				'name'				=> 'Avg. Sessions',
				'asset_id'			=> 22,
				'graph_function_id'	=> 3,
				'graph_type_id'		=> 4,
				'graph_color_id'	=> 2,
			) 
		);

		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 13,
				'short_name'		=> 'MAX EM',
				'name'				=> 'Max. EM Usage',
				'asset_id'			=> 12,
				'graph_function_id'	=> 2,
				'graph_type_id'		=> 4,
				'graph_color_id'	=> 1,
			) 
		);

		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 14,
				'short_name'		=> 'AVG EM',
				'name'				=> 'Avg. EM Usage',
				'asset_id'			=> 12,
				'graph_function_id'	=> 3,
				'graph_type_id'		=> 4,
				'graph_color_id'	=> 2,
			) 
		);

		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 15,
				'short_name'		=> 'MAX HEAP',
				'name'				=> 'Max. Heap Memory Usage',
				'asset_id'			=> 14,
				'graph_function_id'	=> 2,
				'graph_type_id'		=> 4,
				'graph_color_id'	=> 1,
			) 
		);

		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 16,
				'short_name'		=> 'AVG HEAP',
				'name'				=> 'Avg. Heap Memory Usage',
				'asset_id'			=> 14,
				'graph_function_id'	=> 3,
				'graph_type_id'		=> 4,
				'graph_color_id'	=> 2,
			) 
		);

		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 17,
				'short_name'		=> 'MAX PAGE MEM',
				'name'				=> 'Max. Page Memory Usage',
				'asset_id'			=> 16,
				'graph_function_id'	=> 2,
				'graph_type_id'		=> 4,
				'graph_color_id'	=> 1,
			) 
		);

		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 18,
				'short_name'		=> 'AVG PAGE MEM',
				'name'				=> 'Avg. Page Memory Usage',
				'asset_id'			=> 16,
				'graph_function_id'	=> 3,
				'graph_type_id'		=> 4,
				'graph_color_id'	=> 2,
			) 
		);

		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 19,
				'short_name'		=> 'MAX ROLL MEM',
				'name'				=> 'Max. Roll Memory Usage',
				'asset_id'			=> 17,
				'graph_function_id'	=> 2,
				'graph_type_id'		=> 4,
				'graph_color_id'	=> 1,
			) 
		);

		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 20,
				'short_name'		=> 'AVG ROLL MEM',
				'name'				=> 'Avg. Roll Memory Usage',
				'asset_id'			=> 17,
				'graph_function_id'	=> 3,
				'graph_type_id'		=> 4,
				'graph_color_id'	=> 2,
			) 
		);

		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 21,
				'short_name'		=> 'MAX ENQ QUEUE',
				'name'				=> 'Max. Enqueue Queue',
				'asset_id'			=> 20,
				'graph_function_id'	=> 2,
				'graph_type_id'		=> 4,
				'graph_color_id'	=> 1,
			) 
		);

		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 22,
				'short_name'		=> 'AVG ENQ QUEUE',
				'name'				=> 'Avg. Enqueue Queue',
				'asset_id'			=> 20,
				'graph_function_id'	=> 3,
				'graph_type_id'		=> 4,
				'graph_color_id'	=> 2,
			) 
		);

		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 23,
				'short_name'		=> 'User CPU',
				'name'				=> 'CPU USuario',
				'asset_id'			=> 4,
				'graph_function_id'	=> 3,
				'graph_type_id'		=> 1,
				'graph_color_id'	=> 1,
			) 
		);

		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 24,
				'short_name'		=> 'System CPU',
				'name'				=> 'CPU Sistema',
				'asset_id'			=> 5,
				'graph_function_id'	=> 3,
				'graph_type_id'		=> 1,
				'graph_color_id'	=> 2,
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 25,
				'short_name'		=> 'Idle CPU',
				'name'				=> 'CPU Idle',
				'asset_id'			=> 6,
				'graph_function_id'	=> 3,
				'graph_type_id'		=> 1,
				'graph_color_id'	=> 4,
			) 
		);

		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 26,
				'short_name'		=> 'Sumatoria sesiones',
				'name'				=> 'Sumatoria sesiones',
				'asset_id'			=> 22,
				'graph_function_id'	=> 1,
				'graph_type_id'		=> 2,
				'graph_color_id'	=> 4,
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 27,
				'short_name'		=> 'P95 Active WPs',
				'name'				=> 'P95 Active WPs',
				'asset_id'			=> 1,
				'graph_function_id'	=> 5,
				'graph_type_id'		=> 1,
				'graph_color_id'	=> 1,
			) 
		);
	}
}


//END OF CLASS	
}

global $AA_GRAPH;
$AA_GRAPH =new AA_GRAPH_CLASS();
?>