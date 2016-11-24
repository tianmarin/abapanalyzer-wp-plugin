<?php
defined('ABSPATH') or die("No script kiddies please!");

class GRAPH_FUNCTION_CLASS extends AA_CLASS{

/**
* Esta función es llamada apenas se crea la clase.
* Es utilizada para instanciar las diferentes clases con su información vital.
*
*/
function __construct(){
	global $wpdb;
	global $aa_vars;
	//como se definió en aa_vars
	$this->class_name	= 'graph_function';
	//Nombre singular para títulos, mensajes a usuario, etc.
//	$this->name_single	= 'Opci&oacute;n de Colaboraci&oacute;n';
	//Nombre plural para títulos, mensajes a usuario, etc.
//	$this->name_plural	= 'Opciones de Colaboraci&oacute;n';
	//Identificador de menú padre
//	$this->parent_slug	= $aa_vars['main_menu_slug'];
	//Identificador de submenú de la clase
//	$this->menu_slug	= $aa_vars[$this->class_name.'_menu_slug'];
	//Utilizadp para validaciones
	$this->plugin_post	= $aa_vars['plugin_post'];
	//Permisos de usuario a nivel de backend WordPRess
//	$this->capability	= $aa_vars[$this->class_name.'_menu_cap'];
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
	register_activation_hook(WP_PLUGIN_DIR."/abap_analyzer/"."index.php", array( $this, 'db_install') );
	register_activation_hook(WP_PLUGIN_DIR."/abap_analyzer/"."index.php", array( $this, 'db_install_data') );
}
public function db_install_data(){
	global $wpdb;
	$count =intval($wpdb->get_var( "SELECT COUNT(*) FROM ".$this->tbl_name));
	if($count == 0){
		$wpdb->insert(
			$this->tbl_name,
			array(
				'code'			=> 'min',
				'short_name'	=> 'M&iacute;nimo',
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'code'			=> 'max',
				'short_name'	=> 'M&aacute;ximo',
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'code'			=> 'avg',
				'short_name'	=> 'Promedio',
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'code'			=> 'sum',
				'short_name'	=> 'Sumatoria',
			) 
		);
	}
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
//END OF CLASS	
}

global $GRAPH_FUNCTION;
$GRAPH_FUNCTION =new GRAPH_FUNCTION_CLASS();
?>