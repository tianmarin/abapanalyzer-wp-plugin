<?php
defined('ABSPATH') or die("No script kiddies please!");

class AA_SECTION_CHART_CLASS extends AA_CLASS{

/**
* Esta función es llamada apenas se crea la clase.
* Es utilizada para instanciar las diferentes clases con su información vital.
*
*/
function __construct(){
	global $wpdb;
	global $aa_vars;
	//como se definió en aa_vars
	$this->class_name	= 'section_chart';
	//Nombre singular para títulos, mensajes a usuario, etc.
//	$this->name_single	= 'Opci&oacute;n de Colaboraci&oacute;n';
	//Nombre plural para títulos, mensajes a usuario, etc.
	$this->name_plural	= 'Chart de Secci&oacute;n';
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
								section_id tinyint(1) unsigned not null,
								chart_id tinyint(1) unsigned not null,
								disp_order tinyint(1) unsigned null,
								PRIMARY KEY (section_id, chart_id) 
							) $charset_collate;";
	//Registro de columnas de la tabla utilizado para validaciones y visualización de formatos
	$this->db_fields	= array(
		'section_id' => array(
			'type'			=>'dual_id',
			'required'		=>true,
		),
		'chart_id' => array(
			'type'			=>'dual_id',
			'required'		=>true,
		),
		'disp_order' => array(
			'type'			=>'nat_number',
			'required'		=>false,
		),
	);
	register_activation_hook(WP_PLUGIN_DIR."/abap_analyzer/"."index.php", array( $this, 'db_install') );
	register_activation_hook(WP_PLUGIN_DIR."/abap_analyzer/"."index.php", array( $this, 'db_install_data') );
	add_action( 'wp_ajax_aa_get_section_charts',		array( $this , 'aa_get_section_charts'		));
	add_action( 'wp_ajax_aa_remove_section_charts',		array( $this , 'aa_remove_section_charts'	));
	add_action( 'wp_ajax_aa_add_section_charts',		array( $this , 'aa_add_section_charts'		));
	add_action( 'wp_ajax_aa_search_section_charts',		array( $this , 'aa_search_section_charts'	));
	add_action( 'wp_ajax_aa_update_section_charts',		array( $this , 'aa_update_section_charts'	));

}
public function get_charts($section_id=null){
	$chart_list=array();
	if($section_id == null){
		return false;
	}
	$sql="SELECT chart_id FROM ".$this->tbl_name.' WHERE section_id="'.$section_id.'" ORDER BY disp_order ASC';
	foreach( self::get_sql($sql) as $key => $val){
		array_push($chart_list, $val['chart_id']);
	}
	return $chart_list;
}
public function aa_search_section_charts(){
	global $wpdb;
	global $AA_CHART;
	$response=array();
	$response['data']=array();
	$postvs=$_POST;
	$chart_ids=self::get_charts($postvs['id']);
	$chart_cond=array();
	
	$sql="SELECT
			b.id as id,
			b.title as title
			FROM 
				(".$this->tbl_name." as a RIGHT JOIN ".$AA_CHART->tbl_name." as b ON a.chart_id=b.id)
			WHERE ";
	if(count($chart_ids)!=null){
		$sql.="b.id NOT IN (".implode(',', array_map('intval', $chart_ids)).") AND ";
	}
	$sql.="b.title LIKE '%".$postvs['chart_search']."%'";
	$sql.=" LIMIT 10";
	$response['ids']=$chart_ids;
	$response['sql']=str_replace("\n", '', str_ireplace('	', ' ', $sql));
	$chart_list = $wpdb->get_results( $sql);
	$i=0;
	if ( ! empty( $chart_list ) ) {
		$response['status'] = 'ok';
		foreach ( $chart_list as $chart ) {
			$response['data']['elem_'.$chart->id]=array();
			$response['data']['elem_'.$chart->id]['elementId']=$chart->id;
			$response['data']['elem_'.$chart->id]['elementTitle']='<h4 class="list-group-item-heading">'.$chart->title.'</h4>';
			$response['data']['elem_'.$chart->id]['elementBody']='<p class="list-group-item-text">'.$chart->title.'</p>';
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
public function aa_get_section_charts(){
	global $wpdb;
	global $AA_CHART;
	
	$response=array();
	$section_id=$_POST['id'];
	$chart_ids=self::get_charts($section_id);
	if( ! empty($chart_ids)){
	$sql="SELECT
			b.id as id,
			b.title as title
			FROM 
				(".$this->tbl_name." as a RIGHT JOIN ".$AA_CHART->tbl_name." as b ON a.chart_id=b.id)
			WHERE ";
		$sql.="b.id IN (".implode(',', array_map('intval', $chart_ids)).") ";
		$sql.=" ORDER BY a.disp_order ASC";
		$response['sql']=str_replace("\n", '', str_ireplace('	', ' ', $sql));
		$chart_list = $wpdb->get_results( $sql);
		if ( ! empty( $chart_list ) ) {
			$i=0;
			$response['status'] = 'ok';
			$response['nose']=$chart_list;
			foreach ( $chart_list as $chart ) {
				$response['data']['elem_'.$chart->id]=array();
				$response['data']['elem_'.$chart->id]['elementId']=$chart->id;
				$response['data']['elem_'.$chart->id]['elementTitle']='<h4 class="list-group-item-heading">'.$chart->title.'</h4>';
				$response['data']['elem_'.$chart->id]['elementBody']='<p class="list-group-item-text">'.$chart->title.'</p>';
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
public function aa_add_section_charts(){
	$number=count(self::get_charts($_POST['id']))+1;
	$insert_array=array(
		'section_id'		=>$_POST['id'],
		'chart_id'		=>$_POST['element_id'],
		'disp_order'	=> $number,
	);
	$response=self::update_class_row('add',$insert_array);
	echo json_encode($response);
	die();
}
public function aa_remove_section_charts(){
	global $wpdb;
	$order = $wpdb->get_row( $wpdb->prepare( "SELECT disp_order FROM ".$this->tbl_name." WHERE section_id = %d AND chart_id = %d", $_POST['id'],$_POST['element_id']));

	$delete_array=array(
		'section_id'		=>$_POST['id'],
		'chart_id'		=>$_POST['element_id'],
		'disp_order'	=>$order->disp_order,
	);
	$response=self::update_class_row('delete',$delete_array);
	echo json_encode($response);
	die();
}
public function aa_update_section_charts(){
	$i=1;
	foreach( explode(',', $_POST['element_ids']) as $element_id){
		$update_array=array(
			'section_id'		=> $_POST['id'],
			'chart_id'		=> $element_id,
			'disp_order'	=> $i,
		);
		$sql="UPDATE ".$this->tbl_name." SET disp_order=".$i." WHERE section_id=".$_POST['id']." AND chart_id=".$element_id;
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
		data-search-action="aa_search_section_charts"
		data-get-action="aa_get_section_charts"
		data-add-action="aa_add_section_charts"
		data-remove-action="aa_remove_section_charts"
		data-update-action="aa_update_section_charts"
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
				$output.='<input type="text" class="form-control" name="'.$this->plugin_post.'[chart_search]"/>';
				$output.='<p class="help-block">Ingresa el correo del usuario que deseas agregar a tu lista de colaboradores y selecci&oacute;nalo a continuaci&oacute;n.<br/>Se excluye de los colaboradores al due&ntilde;o (por defecto el creador) del sistema.</p>';
			$output.='<ul class="list-group" id="aa-new-element-list"></ul>';
		}
		$output.='</div>';
	$output.='</div>';
	$output.='<div class="form-group">';
		$output.='<div class="col-sm-2 control-label"></div>';
			$output.='<div class="col-sm-10">';
				$QS = http_build_query(array_merge($_GET, array("action"=>'')));
				$URL=htmlspecialchars('?'.$QS);
				$output.='<a href="'.$URL.'" class="btn btn-primary">Terminar</a>';
			$output.='</div>';
		$output.='</div>';
	$output.='</div>';
	$output.='</form>';
	return $output;
}//---------------------------------------------------------------------------------------------------------------------------------------------------------
public function db_install_data(){
	global $wpdb;
	$count =intval($wpdb->get_var( "SELECT COUNT(*) FROM ".$this->tbl_name));
	if($count == 0){
		$wpdb->insert(
			$this->tbl_name,
			array(
				'section_id'		=> 1,
				'chart_id'			=> 1,
				'disp_order'		=> 1,
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'section_id'		=> 1,
				'chart_id'			=> 2,
				'disp_order'		=> 3,
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'section_id'		=> 1,
				'chart_id'			=> 3,
				'disp_order'		=> 5,
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'section_id'		=> 1,
				'chart_id'			=> 4,
				'disp_order'		=> 6,
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'section_id'		=> 1,
				'chart_id'			=> 5,
				'disp_order'		=> 7,
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'section_id'		=> 1,
				'chart_id'			=> 6,
				'disp_order'		=> 8,
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'section_id'		=> 1,
				'chart_id'			=> 13,
				'disp_order'		=> 2,
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'section_id'		=> 1,
				'chart_id'			=> 14,
				'disp_order'		=> 4,
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'section_id'		=> 1,
				'chart_id'			=> 15,
				'disp_order'		=> 9,
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'section_id'		=> 2,
				'chart_id'			=> 7,
				'disp_order'		=> 1,
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'section_id'		=> 2,
				'chart_id'			=> 8,
				'disp_order'		=> 2,
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'section_id'		=> 2,
				'chart_id'			=> 9,
				'disp_order'		=> 3,
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'section_id'		=> 2,
				'chart_id'			=> 10,
				'disp_order'		=> 4,
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'section_id'		=> 3,
				'chart_id'			=> 11,
				'disp_order'		=> 1,
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'section_id'		=> 4,
				'chart_id'			=> 12,
				'disp_order'		=> 1,
			) 
		);
	}
}


//---------------------------------------------------------------------------------------------------------------------------------------------------------
//END OF CLASS	
}

global $AA_SECTION_CHART;
$AA_SECTION_CHART =new AA_SECTION_CHART_CLASS();
?>