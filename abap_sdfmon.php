<?php
defined('ABSPATH') or die("No script kiddies please!");
class SDFMON_CLASS extends ABAP_CLASS{
	
	function __construct(){
		global $wpdb;
		global $abap_vars;
		$this->class_name	= 'sdfmon';
		$this->name_single	= 'Registro SDFMON';
		$this->name_plural	= 'SDFMON';
		$this->icon			= '<i class="fa fa-user"></i>';
		$this->parent_slug	= $abap_vars['main_menu_slug'];							//main, admin, super
		$this->menu_slug	= $abap_vars[$this->class_name.'_menu_slug'];			//Class Menu Slug
		$this->plugin_post	= $abap_vars['plugin_post'];								//used for validations
		$this->capability	= $abap_vars[$this->class_name.'_menu_cap'];				//Class capabilities
		$this->tbl_name		= $wpdb->prefix.$abap_vars[$this->class_name.'_tbl_name'];
		$this->db_version	= '1';
		$charset_collate	= $wpdb->get_charset_collate();
		$this->crt_tbl_sql	=														//SQL Sentence for create Class table
								"CREATE TABLE ".$this->tbl_name." (
									id bigint(20) unsigned not null auto_increment,
									analysis_id tinyint unsigned not null,
									date date not null,
									time time not null,
									servername varchar(50) not null,
									act_wps smallint unsigned not null,
									dia_wps smallint unsigned not null,
									rfc_wps smallint unsigned not null,
									cpu_usr smallint unsigned not null,
									cpu_sys smallint unsigned not null,
									cpu_idle smallint unsigned not null,
									cpu_ava smallint unsigned not null,
									page_in smallint unsigned not null,
									page_out smallint unsigned not null,
									free_mem int unsigned not null,
									em_alloc int unsigned not null,
									em_attach int unsigned not null,
									em_global int unsigned not null,
									heap int unsigned not null,
									priv_mode smallint unsigned not null,
									page_mem int unsigned not null,
									roll_mem int unsigned not null,
									queue_dia smallint unsigned not null,
									queue_upd smallint unsigned not null,
									queue_enq smallint unsigned not null,
									logins smallint unsigned not null,
									sessions smallint unsigned not null,
									UNIQUE KEY id (id)
								) $charset_collate;";
		$this->db_fields	= array(
			/*
			field_name		:	Nombre del campo a nivel de DB
			field_type		:	Tipo de Dato para validacion
			required		:	Flag de obligatoriedad del dato (NOT NULL)
								id
								nat_number
								text
			size			:	Tamaño del campo para formularios (valido solo para inputs tipo texto)
								XS		15%
								S		30%
								M		50%
								L		75%
								XL		100%
			maxchar			:	Máximo número de caracters	(null es indefinido)
			*/
			//field_name		field_type							,required			form_size		maxchar				desc
			'id'				=> array('field_type'=>'id'			,'required'=>true,	'size'=>'XS',	'maxchar'=>null,	'field_desc'=>'id'),
			'analysis_id'		=> array('field_type'=>'number'		,'required'=>true,	'size'=>'M',	'maxchar'=>60,		'field_desc'=>'Nombre Completo'),
			'date'				=> array('field_type'=>'date'		,'required'=>true,	'size'=>'XS',	'maxchar'=>10,		'field_desc'=>'Nombre Corto'),
			'time'				=> array('field_type'=>'time'		,'required'=>true,	'size'=>'XS',	'maxchar'=>10,		'field_desc'=>'Nombre Corto'),
			'servername'		=> array('field_type'=>'text'		,'required'=>false,	'size'=>'XS',	'maxchar'=>10,		'field_desc'=>'Nombre Corto'),
			'act_wps'			=> array('field_type'=>'unmber'		,'required'=>false,	'size'=>'XS',	'maxchar'=>10,		'field_desc'=>'Nombre Corto'),
			'dia_wps'			=> array('field_type'=>'unmber'		,'required'=>false,	'size'=>'XS',	'maxchar'=>10,		'field_desc'=>'Nombre Corto'),
			'rfc_wps'			=> array('field_type'=>'unmber'		,'required'=>false,	'size'=>'XS',	'maxchar'=>10,		'field_desc'=>'Nombre Corto'),
			'cpu_usr'			=> array('field_type'=>'unmber'		,'required'=>false,	'size'=>'XS',	'maxchar'=>10,		'field_desc'=>'Nombre Corto'),
			'cpu_sys'			=> array('field_type'=>'unmber'		,'required'=>false,	'size'=>'XS',	'maxchar'=>10,		'field_desc'=>'Nombre Corto'),
			'cpu_idle'			=> array('field_type'=>'unmber'		,'required'=>false,	'size'=>'XS',	'maxchar'=>10,		'field_desc'=>'Nombre Corto'),
			'cpu_ava'			=> array('field_type'=>'unmber'		,'required'=>false,	'size'=>'XS',	'maxchar'=>10,		'field_desc'=>'Nombre Corto'),
			'page_in'			=> array('field_type'=>'unmber'		,'required'=>false,	'size'=>'XS',	'maxchar'=>10,		'field_desc'=>'Nombre Corto'),
			'page_out'			=> array('field_type'=>'unmber'		,'required'=>false,	'size'=>'XS',	'maxchar'=>10,		'field_desc'=>'Nombre Corto'),
			'free_mem'			=> array('field_type'=>'unmber'		,'required'=>false,	'size'=>'XS',	'maxchar'=>10,		'field_desc'=>'Nombre Corto'),
			'em_alloc'			=> array('field_type'=>'unmber'		,'required'=>false,	'size'=>'XS',	'maxchar'=>10,		'field_desc'=>'Nombre Corto'),
			'em_attach'			=> array('field_type'=>'unmber'		,'required'=>false,	'size'=>'XS',	'maxchar'=>10,		'field_desc'=>'Nombre Corto'),
			'em_global'			=> array('field_type'=>'unmber'		,'required'=>false,	'size'=>'XS',	'maxchar'=>10,		'field_desc'=>'Nombre Corto'),
			'heap'				=> array('field_type'=>'unmber'		,'required'=>false,	'size'=>'XS',	'maxchar'=>10,		'field_desc'=>'Nombre Corto'),
			'priv_mode'			=> array('field_type'=>'unmber'		,'required'=>false,	'size'=>'XS',	'maxchar'=>10,		'field_desc'=>'Nombre Corto'),
			'page_mem'			=> array('field_type'=>'unmber'		,'required'=>false,	'size'=>'XS',	'maxchar'=>10,		'field_desc'=>'Nombre Corto'),
			'roll_mem'			=> array('field_type'=>'unmber'		,'required'=>false,	'size'=>'XS',	'maxchar'=>10,		'field_desc'=>'Nombre Corto'),
			'queue_dia'			=> array('field_type'=>'unmber'		,'required'=>false,	'size'=>'XS',	'maxchar'=>10,		'field_desc'=>'Nombre Corto'),
			'queue_upd'			=> array('field_type'=>'unmber'		,'required'=>false,	'size'=>'XS',	'maxchar'=>10,		'field_desc'=>'Nombre Corto'),
			'queue_enq'			=> array('field_type'=>'unmber'		,'required'=>false,	'size'=>'XS',	'maxchar'=>10,		'field_desc'=>'Nombre Corto'),
			'logins'			=> array('field_type'=>'unmber'		,'required'=>false,	'size'=>'XS',	'maxchar'=>10,		'field_desc'=>'Nombre Corto'),
			'sessions'			=> array('field_type'=>'unmber'		,'required'=>false,	'size'=>'XS',	'maxchar'=>10,		'field_desc'=>'Nombre Corto'),
		);
		register_activation_hook(plugin_dir_path(__FILE__)."index.php", array( $this, 'db_install') );
//		add_action('admin_menu', array( &$this , "abap_register_class_submenu_page" ) );
	}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
//END OF CLASS	
}

global $SDFMON;
$SDFMON =new SDFMON_CLASS();
?>