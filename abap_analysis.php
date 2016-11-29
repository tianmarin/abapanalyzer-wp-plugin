<?php
defined('ABSPATH') or die("No script kiddies please!");
class ANALYSIS_CLASS extends ABAP_CLASS{
	
	function __construct(){
		global $wpdb;
		global $abap_vars;
		$this->class_name	= 'analysis';
		$this->name_single	= 'Analisis';
		$this->name_plural	= 'Analisis';
		$this->icon			= '<i class="fa fa-user"></i>';
		$this->parent_slug	= $abap_vars['main_menu_slug'];							//main, admin, super
		$this->menu_slug	= $abap_vars[$this->class_name.'_menu_slug'];			//Class Menu Slug
		$this->plugin_post	= $abap_vars['plugin_post'];								//used for validations
		$this->capability	= $abap_vars[$this->class_name.'_menu_cap'];				//Class capabilities
		$this->tbl_name		= $wpdb->prefix.$abap_vars[$this->class_name.'_tbl_name'];
		$this->db_version	= '2';
		$charset_collate	= $wpdb->get_charset_collate();
		$this->crt_tbl_sql	=														//SQL Sentence for create Class table
								"CREATE TABLE ".$this->tbl_name." (
									id tinyint unsigned not null auto_increment,
									creator varchar(60) not null,
									creation_date datetime not null default current_timestamp,
									sid varchar(30) not null,
									start_date date null,
									end_date date null,
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
			//field_name			field_type						,required			form_size		maxchar				desc
			'id'			=> array('field_type'=>'id'				,'required'=>true,	'size'=>'XS',	'maxchar'=>null,	'field_desc'=>'id'),
			'creator'		=> array('field_type'=>'text'			,'required'=>false,	'size'=>'M',	'maxchar'=>60,		'field_desc'=>'ID de Usuario'),
			'creation_date'	=> array('field_type'=>'timestamp'		,'required'=>false,	'size'=>'M',	'maxchar'=>60,		'field_desc'=>'CreationDateTime'),
			'sid'			=> array('field_type'=>'sid'			,'required'=>false,	'size'=>'XS',	'maxchar'=>null,	'field_desc'=>'SID'),
			'start_date'	=> array('field_type'=>'start_date'		,'required'=>false,	'size'=>'XS',	'maxchar'=>null,	'field_desc'=>'Start Date'),
			'end_date'		=> array('field_type'=>'end_date'		,'required'=>false,	'size'=>'XS',	'maxchar'=>null,	'field_desc'=>'END Date'),
			
		);
		register_activation_hook(plugin_dir_path(__FILE__)."index.php", array( $this, 'db_install') );
		add_action('admin_menu', array( &$this , "abap_register_class_submenu_page" ) );
	}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
	/**
	* Creates the main content of the administation page
	*
	* @global wpdb $wpdb
	* @since 1.0
	* @author Cristian Marin
	* @package WordPress
	*/
	function class_menu_main(){
		//Global
		global $wpdb;
		global $abap_vars;
		$DB=$this->db_fields;
		$rows = self::get_all();
		$contents=array();
		foreach($rows as $row):
			array_push($contents,
				array(
					$row['id'],
					$row['sid'],
					$row['creation_date'],
					)
				);
		endforeach;
		$title		= "Listado de ".$this->name_plural;
		$add_new	= "Agregar nuevo";
		$titles		= array('id',$DB['sid']['field_desc'],$DB['creation_date']['field_desc']);
		abap_class_menu_main(
			$title,
			$this->menu_slug,
			$add_new,
			$this->plugin_post,
			$titles,
			$contents
		);
	}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
	/**
	* Form to add a single Class registry to the system
	*
	* @since 1.0
	* @author Cristian Marin
	*/
	public function new_form(){
		$DB=$this->db_fields;
		$fields=array();
		foreach($DB as $key => $field):
			if($key!='id'):
				array_push($fields,
					array(
						'field_name'	=>	$key,
						'field_desc'	=>	$field['field_desc'],
						'field_type'	=>	$field['field_type'],
						'required'		=>	$field['required'],
						'size'			=>	$field['size'],
						'field_value'	=>	'',
						'maxchar'		=>	$field['maxchar']
					)
				);
			endif;
		endforeach;
		$title = "Agregar nuevo ".$this->name_single;
		abap_register_script_formvalidator($this->menu_slug);
		abap_class_form(
			"add",
			$title,
			$this->menu_slug,
			$this->plugin_post,
			$fields
		);		

	}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
	/**
	* Form to edit a single Class registry to the system
	*
	* @since 1.0
	* @author Cristian Marin
	*/
	function edit_form($id=0){
		$item=self::get_single($id);
		$DB=$this->db_fields;
		$fields=array();
		foreach($DB as $key => $field):
			if($key!='id'):
				array_push($fields,
					array(
						'field_name'	=>	$key,
						'field_desc'	=>	$field['field_desc'],
						'field_type'	=>	$field['field_type'],
						'required'		=>	$field['required'],
						'size'			=>	$field['size'],
						'field_value'	=>	$item[$key],
						'maxchar'		=>	$field['maxchar']
					)
				);
			endif;
		endforeach;
		$title="Edabapr ".$this->name_single;
		abap_register_script_formvalidator($this->menu_slug);
		abap_class_form(
			"update",
			$title,
			$this->menu_slug,
			$this->plugin_post,
			$fields,
			$id
		);
	}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
//END OF CLASS	
}

global $ANALYSIS;
$ANALYSIS =new ANALYSIS_CLASS();
?>