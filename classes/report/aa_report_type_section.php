<?php
defined('ABSPATH') or die("No script kiddies please!");

class REPORT_TYPE_SECTION_CLASS extends AA_CLASS{

/**
* Esta función es llamada apenas se crea la clase.
* Es utilizada para instanciar las diferentes clases con su información vital.
*
*/
function __construct(){
	global $wpdb;
	global $aa_vars;
	//como se definió en aa_vars
	$this->class_name	= 'report_type_section';
	//Nombre singular para títulos, mensajes a usuario, etc.
//	$this->name_single	= 'Opci&oacute;n de Colaboraci&oacute;n';
	//Nombre plural para títulos, mensajes a usuario, etc.
	$this->name_plural	= 'Secci&oacute;n de Tipo de Reporte';
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
								report_type_id tinyint(1) unsigned not null,
								section_id tinyint(1) unsigned not null,
								disp_order tinyint(1) unsigned null,
								PRIMARY KEY (report_type_id, section_id) 
							) $charset_collate;";
	//Registro de columnas de la tabla utilizado para validaciones y visualización de formatos
	$this->db_fields	= array(
		'report_type_id' => array(
			'type'			=>'dual_id',
			'required'		=>true,
		),
		'section_id' => array(
			'type'			=>'dual_id',
			'required'		=>true,
		),
		'disp_order' => array(
			'type'			=>'nat_number',
			'required'		=>false,
		),
	);
	register_activation_hook(WP_PLUGIN_DIR."/abap_analyzer/"."index.php", array( $this, 'db_install') );
	add_action( 'wp_ajax_aa_get_report_type_sections',		array( $this , 'aa_get_report_type_sections'		));
	add_action( 'wp_ajax_aa_remove_report_type_sections',	array( $this , 'aa_remove_report_type_sections'		));
	add_action( 'wp_ajax_aa_add_report_type_sections',		array( $this , 'aa_add_report_type_sections'		));
	add_action( 'wp_ajax_aa_search_report_type_sections',	array( $this , 'aa_search_report_type_sections'		));
	add_action( 'wp_ajax_aa_update_report_type_sections',	array( $this , 'aa_update_report_type_sections'		));

}
public function get_sections($report_type_id=null){
	$section_list=array();
	if($report_type_id == null){
		return false;
	}
	$sql="SELECT section_id FROM ".$this->tbl_name.' WHERE report_type_id="'.$report_type_id.'" ORDER BY disp_order ASC';
	foreach( self::get_sql($sql) as $key => $val){
		array_push($section_list, $val['section_id']);
	}
	return $section_list;
}
public function aa_search_report_type_sections(){
	global $wpdb;
	global $SECTION;
	$response=array();
	$response['data']=array();
	$postvs=$_POST;
	$section_ids=self::get_sections($postvs['id']);
	$section_cond=array();
	
	$sql="SELECT
			b.id as id,
			b.title as title,
			b.short_name as short_name
			FROM 
				(".$this->tbl_name." as a RIGHT JOIN ".$SECTION->tbl_name." as b ON a.section_id=b.id)
			WHERE ";
	if(count($section_ids)!=null){
		$sql.="b.id NOT IN (".implode(',', array_map('intval', $section_ids)).") AND ";
	}
	$sql.="b.title LIKE '%".$postvs['section_search']."%'";
	$sql.=" LIMIT 10";
	$response['ids']=$section_ids;
	$response['sql']=str_replace("\n", '', str_ireplace('	', ' ', $sql));
	$section_list = $wpdb->get_results( $sql);
	$i=0;
	if ( ! empty( $section_list ) ) {
		$response['status'] = 'ok';
		foreach ( $section_list as $section ) {
			$response['data']['elem_'.$section->id]=array();
			$response['data']['elem_'.$section->id]['elementId']=$section->id;
			$response['data']['elem_'.$section->id]['elementTitle']='<h4 class="list-group-item-heading">'.$section->short_name.'</h4>';
			$response['data']['elem_'.$section->id]['elementBody']='<p class="list-group-item-text">'.$section->title.'</p>';
			$i++;
		}
		$response['elementCount']=$i;
	} else {
		$response['noElementTitle']='<h4 class="list-group-item-heading">No hay elementos</h4>';
		$response['noElementBody']='<p class="list-group-item-text">Trata de buscarlo de otra forma.</p>';
		$response['status'] = 'error';
	}
//	$response['colaboradores']=$collab;
	echo json_encode($response);
	die();
}
public function aa_get_report_type_sections(){
	global $wpdb;
	global $SECTION;
	
	$response=array();
	$report_type_id=$_POST['id'];
	$section_ids=self::get_sections($report_type_id);
	if( ! empty($section_ids)){
	$sql="SELECT
			b.id as id,
			b.title as title,
			b.short_name as short_name
			FROM 
				(".$this->tbl_name." as a RIGHT JOIN ".$SECTION->tbl_name." as b ON a.section_id=b.id)
			WHERE ";
		$sql.="b.id IN (".implode(',', array_map('intval', $section_ids)).") ";
		$sql.=" ORDER BY a.disp_order ASC LIMIT 10";
		$response['sql']=str_replace("\n", '', str_ireplace('	', ' ', $sql));
		$section_list = $wpdb->get_results( $sql);
		if ( ! empty( $section_list ) ) {
			$i=0;
			$response['status'] = 'ok';
			$response['nose']=$section_list;
			foreach ( $section_list as $section ) {
				$response['data']['elem_'.$section->id]=array();
				$response['data']['elem_'.$section->id]['elementId']=$section->id;
				$response['data']['elem_'.$section->id]['elementTitle']='<h4 class="list-group-item-heading">'.$section->short_name.'</h4>';
				$response['data']['elem_'.$section->id]['elementBody']='<p class="list-group-item-text">'.$section->title.'</p>';
				$i++;
			}
			$response['elementCount']=$i;
		}
	}else{
		$response['status'] = 'error';
		$response['noElementTitle']='<h4 class="list-group-item-heading">No hay elementos</h4>';
		$response['noElementBody']='<p class="list-group-item-text">Agrega gr&aacute;ficas con el formulario de a continuaci&oacute;n.</p>';
	}
	echo json_encode($response);
	die();
}
public function aa_add_report_type_sections(){
	$number=count(self::get_sections($_POST['id']))+1;
	$insert_array=array(
		'report_type_id'		=>$_POST['id'],
		'section_id'		=>$_POST['element_id'],
		'disp_order'	=> $number,
	);
	$response=self::update_class_row('add',$insert_array);
	echo json_encode($response);
	die();
}
public function aa_remove_report_type_sections(){
	global $wpdb;
	$order = $wpdb->get_row( $wpdb->prepare( "SELECT disp_order FROM ".$this->tbl_name." WHERE report_type_id = %d AND section_id = %d", $_POST['id'],$_POST['element_id']));

	$delete_array=array(
		'report_type_id'		=>$_POST['id'],
		'section_id'		=>$_POST['element_id'],
		'disp_order'	=>$order->disp_order,
	);
	$response=self::update_class_row('delete',$delete_array);
	echo json_encode($response);
	die();
}
public function aa_update_report_type_sections(){
	$i=1;
	foreach( explode(',', $_POST['element_ids']) as $element_id){
		$update_array=array(
			'report_type_id'		=> $_POST['id'],
			'section_id'		=> $element_id,
			'disp_order'	=> $i,
		);
		$sql="UPDATE ".$this->tbl_name." SET disp_order=".$i." WHERE report_type_id=".$_POST['id']." AND section_id=".$element_id;
		self::get_sql($sql);
//		$response=self::update_class_row('edit',$update_array);
		$i++;
	}
	$response['status']='ok';
	echo json_encode($response);
	die();
}

public function special_form($id=null){
	$output='';
	$output.='<form class="form-horizontal" id="aa-ajax-wp-filter"
		data-search-action="aa_search_report_type_sections"
		data-get-action="aa_get_report_type_sections"
		data-add-action="aa_add_report_type_sections"
		data-remove-action="aa_remove_report_type_sections"
		data-update-action="aa_update_report_type_sections"
		>';
	$output.='<div class="form-group aa-system-wpadmin-form">';
		$output.='<label class="col-sm-2 control-label">Usuarios Colaboradores</label>';
		$output.='<div class="col-sm-10">';
		if($id == null){
			$output.='<p class="help-block">Primero debes crear el fd. Una vez que ya has creado el sistema puedes editarlo y agregar colaboradores.</p>';
		}else{
			$output.='<ul class="list-group aa-sortable" id="aa-element-list"></ul>'; 
			$output.='<div class="clearfix"></div>';
			$output.='<p class="lead">Agregar Charts</p>';
				$output.='<input type="hidden"
				name="'.$this->plugin_post.'[id]"
					value="'.$id.'"/>';
				$output.='<input type="text" class="form-control" name="'.$this->plugin_post.'[section_search]"/>';
				$output.='<p class="help-block">Ingresa el correo del usuario que deseas agregar a tu lista de colaboradores y selecci&oacute;nalo a continuaci&oacute;n.<br/>Se excluye de los colaboradores al due&ntilde;o (por defecto el creador) del sistema.</p>';
			$output.='<ul class="list-group" id="aa-new-element-list"></ul>';
		}
		$output.='</div>';
	$output.='</div>';
	$output.='<div class="form-group">';
		$output.='<div class="col-sm-2 control-label"></div>';
			$output.='<div class="col-sm-10">';
				$QS = http_build_query(array_merge($_GET, array("action"=>'')));
				$URL=htmlspecialchars("$_SERVER[PHP_SELF]?$QS");
				$output.='<a href="'.$URL.'" class="btn btn-primary">Terminar</a>';
			$output.='</div>';
		$output.='</div>';
	$output.='</div>';
	$output.='</form>';
	return $output;
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
//END OF CLASS	
}

global $REPORT_TYPE_SECTION;
$REPORT_TYPE_SECTION =new REPORT_TYPE_SECTION_CLASS();
?>