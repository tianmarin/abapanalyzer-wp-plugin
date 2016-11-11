<?php
defined('ABSPATH') or die("No script kiddies please!");

class CHART_GRAPH_CLASS extends AA_CLASS{

/**
* Esta función es llamada apenas se crea la clase.
* Es utilizada para instanciar las diferentes clases con su información vital.
*
*/
function __construct(){
	global $wpdb;
	global $aa_vars;
	//como se definió en aa_vars
	$this->class_name	= 'chart_graph';
	//Nombre singular para títulos, mensajes a usuario, etc.
//	$this->name_single	= 'Opci&oacute;n de Colaboraci&oacute;n';
	//Nombre plural para títulos, mensajes a usuario, etc.
	$this->name_plural	= 'Gr&aacute;ficos de Chart';
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
								chart_id tinyint(1) unsigned not null,
								graph_id bigint(20) unsigned not null,
								disp_order tinyint(1) unsigned null,
								PRIMARY KEY (chart_id, graph_id) 
							) $charset_collate;";
	//Registro de columnas de la tabla utilizado para validaciones y visualización de formatos
	$this->db_fields	= array(
		'chart_id' => array(
			'type'			=>'dual_id',
			'required'		=>true,
		),
		'graph_id' => array(
			'type'			=>'dual_id',
			'required'		=>true,
		),
		'disp_order' => array(
			'type'			=>'nat_number',
			'required'		=>false,
		),
	);
	register_activation_hook(WP_PLUGIN_DIR."/abap_analyzer/"."index.php", array( $this, 'db_install') );
	add_action( 'wp_ajax_aa_get_chart_graphs',		array( $this , 'aa_get_chart_graphs'		));
	add_action( 'wp_ajax_aa_remove_chart_graphs',	array( $this , 'aa_remove_chart_graphs'		));
	add_action( 'wp_ajax_aa_add_chart_graphs',		array( $this , 'aa_add_chart_graphs'		));
	add_action( 'wp_ajax_aa_search_chart_graphs',	array( $this , 'aa_search_chart_graphs'		));
	add_action( 'wp_ajax_aa_update_chart_graphs',	array( $this , 'aa_update_chart_graphs'		));

}
public function get_graphs($chart_id=null){
	$graph_list=array();
	if($chart_id == null){
		return false;
	}
	$sql="SELECT graph_id FROM ".$this->tbl_name.' WHERE chart_id="'.$chart_id.'" ORDER BY disp_order DESC';
	foreach( self::get_sql($sql) as $key => $val){
		array_push($graph_list, $val['graph_id']);
	}
	return $graph_list;
}
public function aa_search_chart_graphs(){
	global $wpdb;
	global $GRAPH;
	global $ASSET;
	global $GRAPH_FUNCTION;
	global $GRAPH_TYPE;
	global $GRAPH_COLOR;
	$response=array();
	$response['data']=array();
	$postvs=$_POST;
	$graph_ids=self::get_graphs($postvs['id']);
	$graph_cond=array();
	if($postvs['asset']!=0){
		array_push($graph_cond,	"b.id=".$postvs['asset']);
	}
	if($postvs['graph_function']!=0){
		array_push($graph_cond,	"c.id=".$postvs['graph_function']);
	}
	if($postvs['graph_type']!=0){
		array_push($graph_cond,	"d.id=".$postvs['graph_type']);
	}
	$sql="SELECT
				a.id as id,
				a.short_name as short_name,
				b.short_name as asset,
				c.short_name as gfunction,
				d.short_name as type,
				e.hex as color
			FROM 
				((((".$GRAPH->tbl_name." AS a LEFT JOIN ".$ASSET->tbl_name." AS b ON a.asset_id=b.id)
				LEFT JOIN ".$GRAPH_FUNCTION->tbl_name." AS c ON a.graph_function_id=c.id)
				LEFT JOIN ".$GRAPH_TYPE->tbl_name." AS d ON a.graph_type_id=d.id)
				LEFT JOIN ".$GRAPH_COLOR->tbl_name." AS e ON a.graph_color_id=e.id) ";
	if(count($graph_cond)!=0 || count($graph_ids)!=null){
		$sql.=" WHERE ";
		if(count($graph_ids)!=null){
			$sql.="a.id NOT IN (".implode(',', array_map('intval', $graph_ids)).") ";
			if(count($graph_cond)!=0){
				$sql.=" AND ";
			}
		}
		if(count($graph_cond)!=0){
			$sql.=implode(' AND ', $graph_cond);
		}
	}
	$sql.=" LIMIT 10";
	$response['ids']=$graph_ids;
//	$response['sql']=str_replace("\n", '', str_ireplace('	', ' ', $sql));
	$sql;
	$graph_list = $wpdb->get_results( $sql);
	$i=0;
	if ( ! empty( $graph_list ) ) {
		$response['status'] = 'ok';
		foreach ( $graph_list as $graph ) {
			$response['data']['elem_'.$graph->id]=array();
			$response['data']['elem_'.$graph->id]['elementId']=$graph->id;
			$response['data']['elem_'.$graph->id]['elementTitle']='<h4 class="list-group-item-heading" style="color:#'.$graph->color.'">'.$graph->short_name.'</h4>';
			$response['data']['elem_'.$graph->id]['elementBody']='<p class="list-group-item-text"><ol class="breadcrumb">';
			$response['data']['elem_'.$graph->id]['elementBody'].='<li>'.$graph->asset.'</li>';
			$response['data']['elem_'.$graph->id]['elementBody'].='<li>'.$graph->gfunction.'</li>';
			$response['data']['elem_'.$graph->id]['elementBody'].='<li>'.$graph->type.'</li>';
			$response['data']['elem_'.$graph->id]['elementBody'].='</ol></p>';
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
public function aa_get_chart_graphs(){
	global $wpdb;
	global $GRAPH;
	global $ASSET;
	global $GRAPH_FUNCTION;
	global $GRAPH_TYPE;
	global $GRAPH_COLOR;
	$response=array();
	$chart_id=$_POST['id'];
	$graph_ids=self::get_graphs($chart_id);
	if( ! empty($graph_ids)){
		$sql="SELECT
					a.id as id,
					a.short_name as short_name,
					b.short_name as asset,
					c.short_name as gfunction,
					d.short_name as type,
					e.hex as color,
					f.disp_order as disp_order
				FROM 
					(((((".$GRAPH->tbl_name." AS a LEFT JOIN ".$ASSET->tbl_name." AS b ON a.asset_id=b.id)
					LEFT JOIN ".$GRAPH_FUNCTION->tbl_name." AS c ON a.graph_function_id=c.id)
					LEFT JOIN ".$GRAPH_TYPE->tbl_name." AS d ON a.graph_type_id=d.id)
					LEFT JOIN ".$GRAPH_COLOR->tbl_name." AS e ON a.graph_color_id=e.id)
					LEFT JOIN ".$this->tbl_name." AS f ON a.id=f.graph_id)";
		$sql.=" WHERE ";
		$sql.="a.id IN (".implode(',', array_map('intval', $graph_ids)).") ";
		$sql.=" ORDER BY f.disp_order ASC LIMIT 10";
		$response['sql']=str_replace("\n", '', str_ireplace('	', ' ', $sql));
		$graph_list = $wpdb->get_results( $sql);
		if ( ! empty( $graph_list ) ) {
			$i=0;
			$response['status'] = 'ok';
			$response['nose']=$graph_list;
			foreach ( $graph_list as $graph ) {
				$response['data']['elem_'.$graph->id]=array();
				$response['data']['elem_'.$graph->id]['elementId']=$graph->id;
				$response['data']['elem_'.$graph->id]['elementTitle']='<h4 class="list-group-item-heading" style="color:#'.$graph->color.'">'.$graph->short_name.'</h4>';
				$response['data']['elem_'.$graph->id]['elementBody']='<p class="list-group-item-text"><ol class="breadcrumb">';
				$response['data']['elem_'.$graph->id]['elementBody'].='<li>'.$graph->asset.'</li>';
				$response['data']['elem_'.$graph->id]['elementBody'].='<li>'.$graph->gfunction.'</li>';
				$response['data']['elem_'.$graph->id]['elementBody'].='<li>'.$graph->type.'</li>';
				$response['data']['elem_'.$graph->id]['elementBody'].='</ol></p>';
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
public function aa_add_chart_graphs(){
	$number=count(self::get_graphs($_POST['id']))+1;
	$insert_array=array(
		'chart_id'		=>$_POST['id'],
		'graph_id'		=>$_POST['element_id'],
		'disp_order'	=> $number,
	);
	$response=self::update_class_row('add',$insert_array);
	echo json_encode($response);
	die();
}
public function aa_remove_chart_graphs(){
	global $wpdb;
	$order = $wpdb->get_row( $wpdb->prepare( "SELECT disp_order FROM ".$this->tbl_name." WHERE chart_id = %d AND graph_id = %d", $_POST['id'],$_POST['element_id']));

	$delete_array=array(
		'chart_id'		=>$_POST['id'],
		'graph_id'		=>$_POST['element_id'],
		'disp_order'	=>$order->disp_order,
	);
	$response=self::update_class_row('delete',$delete_array);
	echo json_encode($response);
	die();
}
public function aa_update_chart_graphs(){
	$i=1;
	foreach( explode(',', $_POST['element_ids']) as $element_id){
		$update_array=array(
			'chart_id'		=> $_POST['id'],
			'graph_id'		=> $element_id,
			'disp_order'	=> $i,
		);
		$sql="UPDATE ".$this->tbl_name." SET disp_order=".$i." WHERE chart_id=".$_POST['id']." AND graph_id=".$element_id;
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
	$output.='<form class="form-inline"id="aa-ajax-wp-filter"
		data-search-action="aa_search_chart_graphs"
		data-get-action="aa_get_chart_graphs"
		data-add-action="aa_add_chart_graphs"
		data-remove-action="aa_remove_chart_graphs"
		data-update-action="aa_update_chart_graphs"
		>';
	$output.='<div class="form-group aa-system-wpadmin-form"';
		$output.='<div class="col-sm-12">';
		if($id == null){
			$output.='';
		}else{
			$output.='<ul class="list-group aa-sortable" id="aa-element-list"></ul>';
			$output.='<div class="clearfix"></div>';
			$output.='<p class="lead">Agregar Gr&aacute;ficas</p>';
				$output.='<input type="hidden"
				name="'.$this->plugin_post.'[id]"
				value="'.$id.'"
				/>';
				$output.='<div class="form-group">';
					$output.='<label class="sr-only">Elemento</label>';
					$output.='<select class="form-control"  name="'.$this->plugin_post.'[asset]">';
						global $ASSET;
						$options=array();
						foreach($ASSET->get_all() as $key => $value){
							$options[$value['id']] = $value['short_name'];
						}
						if(count($options) == 0){
							$field['options']=array(0 => "No hay informaci&oacute;n");
						}else{
							$field['options']=$options;
						}
						$output.='<option value="0">Seleccionar</option>';
						foreach($field['options'] as $sel_key => $sel_opt){
							$output.='<option value="'.$sel_key.'" ';
							$output.=isset($field['value']) ? ($sel_key == $field['value'] ? " selected " : '') : '';
							$output.='>'.$sel_opt.'</option>';
						}
					$output.='</select>';
				$output.='</div>';
				$output.='<div class="form-group">';
					$output.='<label class="sr-only">Funci&oacute;n</label>';
					$output.='<select class="form-control"  name="'.$this->plugin_post.'[graph_function]">';
						global $GRAPH_FUNCTION;
						$options=array();
						foreach($GRAPH_FUNCTION->get_all() as $key => $value){
							$options[$value['id']] = $value['short_name'];
						}
						if(count($options) == 0){
							$field['options']=array(0 => "No hay informaci&oacute;n");
						}else{
							$field['options']=$options;
						}
						$output.='<option value="0">Seleccionar</option>';
						foreach($field['options'] as $sel_key => $sel_opt){
							$output.='<option value="'.$sel_key.'" ';
							$output.=isset($field['value']) ? ($sel_key == $field['value'] ? " selected " : '') : '';
							$output.='>'.$sel_opt.'</option>';
						}
					$output.='</select>';
				$output.='</div>';
				$output.='<div class="form-group">';
					$output.='<label class="sr-only">Tipo</label>';
					$output.='<select class="form-control" name="'.$this->plugin_post.'[graph_type]">';
						global $GRAPH_TYPE;
						$options=array();
						foreach($GRAPH_TYPE->get_all() as $key => $value){
							$options[$value['id']] = $value['short_name'];
						}
						if(count($options) == 0){
							$field['options']=array(0 => "No hay informaci&oacute;n");
						}else{
							$field['options']=$options;
						}
						$output.='<option value="0" >Seleccionar</option>';
						foreach($field['options'] as $sel_key => $sel_opt){
							$output.='<option value="'.$sel_key.'" ';
							$output.=isset($field['value']) ? ($sel_key == $field['value'] ? " selected " : '') : '';
							$output.='>'.$sel_opt.'</option>';
						}
					$output.='</select>';
				$output.='</div>';
			$output.='<p class="help-block">Ingresa el correo del usuario que deseas agregar a tu lista de colaboradores y selecci&oacute;nalo a continuaci&oacute;n.<br/>Se excluye de los colaboradores al due&ntilde;o (por defecto el creador) del sistema.</p>';
			$output.='<ul class="list-group" id="aa-new-element-list"></ul>';
			$output.='</ul>';
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

global $CHART_GRAPH;
$CHART_GRAPH =new CHART_GRAPH_CLASS();
?>