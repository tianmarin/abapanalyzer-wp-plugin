<?php
defined('ABSPATH') or die("No script kiddies please!");

class AA_ASSET_CLASS extends AA_CLASS{

/**
* Esta función es llamada apenas se crea la clase.
* Es utilizada para instanciar las diferentes clases con su información vital.
*
*/
public function __construct(){
	global $wpdb;
	global $aa_vars;
	//como se definió en aa_vars
	$this->class_name	= 'asset';
	//Nombre singular para títulos, mensajes a usuario, etc.
	$this->name_single	= 'Activo';
	//Nombre plural para títulos, mensajes a usuario, etc.
	$this->name_plural	= 'Activos';
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
								source_id tinyint(1) not null,
								col_name varchar(20) not null,
								short_name varchar(50) null,
								description text not null,
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
		'source_id' => array(
			'type'			=>'select',
			'required'		=>true,
			'desc'			=>'Fuente',
			'form-help'		=>'La Fuente va relacionada con el archivo y/o transacci&oacute;n de SAP que genera este activo.',
			'in_wp_table'	=>true,
			'wp_table_lead'	=>false,
			'in_form'		=>true,
			'sp_form'		=>true,
			'in_wp_table'	=>true,
			'sp_wp_table'	=>true,
		),
		'col_name' => array(
			'type'			=>'text',
			'required'		=>true,
			'maxchar'		=>20,
			'desc'			=>'Nombre de Columna',
			'form-help'		=>'El nombre de columna registrado en el sistema "ABAP ANALYZER".',
			'in_wp_table'	=>true,
			'wp_table_lead'	=>true,
		),
		'short_name' => array(
			'type'			=>'text',
			'required'		=>true,
			'maxchar'		=>50,
			'desc'			=>'Nombre corto',
			'form-help'		=>'',
			'in_wp_table'	=>true,
		),
		'description' => array(
			'type'			=>'textarea',
			'required'		=>true,
			'maxchar'		=>30,
			'desc'			=>'Descripción',
			'form-help'		=>'',
			'in_wp_table'	=>false,
		),
	);
	register_activation_hook(WP_PLUGIN_DIR."/abap_analyzer/"."index.php", array( $this, 'db_install') );
	register_activation_hook(WP_PLUGIN_DIR."/abap_analyzer/"."index.php", array( $this, 'db_install_data') );
//	add_action('admin_menu', array( $this , "register_submenu_page" ) );
}
protected function sp_wp_table_source_id($id){
	global $ASSET_SOURCE;
	$response=$ASSET_SOURCE->get_single($id);
	return 	'<i class="fa fa-external-link fa-fw" aria-hidden="true"></i> '.$response['short_name'];
}
protected function sp_form_source_id(){
	global $ASSET_SOURCE;
	$response = array();
	foreach($ASSET_SOURCE->get_all() as $key => $value){
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
				'source_id'			=> 1,
				'col_name'			=> 'act_wps',
				'short_name'		=> 'Active WPs',
			) 
		);	
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 2,
				'source_id'			=> 1,
				'col_name'			=> 'dia_wps',
				'short_name'		=> 'Dialog Active WPs',
			) 
		);	
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 3,
				'source_id'			=> 1,
				'col_name'			=> 'rfc_wps',
				'short_name'		=> 'Free WPs for RFC',
			) 
		);	
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 4,
				'source_id'			=> 1,
				'col_name'			=> 'cpu_usr',
				'short_name'		=> 'User CPU',
			) 
		);	
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 5,
				'source_id'			=> 1,
				'col_name'			=> 'cpu_sys',
				'short_name'		=> 'System CPU',
			) 
		);	
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 6,
				'source_id'			=> 1,
				'col_name'			=> 'cpu_idle',
				'short_name'		=> 'Idle CPU',
			) 
		);	
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 7,
				'source_id'			=> 1,
				'col_name'			=> 'cpu_ava',
				'short_name'		=> 'Available CPU',
			) 
		);	
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 8,
				'source_id'			=> 1,
				'col_name'			=> 'page_in',
				'short_name'		=> 'Pagging In (SWAP)',
			) 
		);	
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 9,
				'source_id'			=> 1,
				'col_name'			=> 'page_out',
				'short_name'		=> 'Pagging Out (SWAP)',
			) 
		);	
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 10,
				'source_id'			=> 1,
				'col_name'			=> 'free_mem',
				'short_name'		=> 'Free Memory',
			) 
		);	
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 11,
				'source_id'			=> 1,
				'col_name'			=> 'em_alloc',
				'short_name'		=> 'Allocated EM',
			) 
		);	
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 12,
				'source_id'			=> 1,
				'col_name'			=> 'em_attach',
				'short_name'		=> 'Attached EM',
			) 
		);	
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 13,
				'source_id'			=> 1,
				'col_name'			=> 'em_global',
				'short_name'		=> 'Global EM',
			) 
		);	
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 14,
				'source_id'			=> 1,
				'col_name'			=> 'heap',
				'short_name'		=> 'Heap Memory',
			) 
		);	
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 15,
				'source_id'			=> 1,
				'col_name'			=> 'priv_mode',
				'short_name'		=> 'Priv Mode Wps',
			) 
		);	
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 16,
				'source_id'			=> 1,
				'col_name'			=> 'page_mem',
				'short_name'		=> 'Page Memory',
			) 
		);	
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 17,
				'source_id'			=> 1,
				'col_name'			=> 'roll_mem',
				'short_name'		=> 'Roll Memory',
			) 
		);	
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 18,
				'source_id'			=> 1,
				'col_name'			=> 'queue_dia',
				'short_name'		=> 'Dialog Queue',
			) 
		);	
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 19,
				'source_id'			=> 1,
				'col_name'			=> 'queue_upd',
				'short_name'		=> 'Update Queue',
			) 
		);	
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 20,
				'source_id'			=> 1,
				'col_name'			=> 'queue_enq',
				'short_name'		=> 'Enqueue Queue',
			) 
		);	
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 21,
				'source_id'			=> 1,
				'col_name'			=> 'logins',
				'short_name'		=> 'Logins',
			) 
		);	
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'				=> 22,
				'source_id'			=> 1,
				'col_name'			=> 'sessions',
				'short_name'		=> 'Sessions',
			) 
		);	
	}
}

//END OF CLASS	
}

global $ASSET;
$ASSET =new AA_ASSET_CLASS();
?>