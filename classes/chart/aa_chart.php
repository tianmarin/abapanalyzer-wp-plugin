<?php
defined('ABSPATH') or die("No script kiddies please!");

class AA_CHART_CLASS extends AA_CLASS{

/**
* Esta función es llamada apenas se crea la clase.
* Es utilizada para instanciar las diferentes clases con su información vital.
*
*/
public function __construct(){
	global $wpdb;
	global $aa_vars;
	//como se definió en aa_vars
	$this->class_name	= 'chart';
	//Nombre singular para títulos, mensajes a usuario, etc.
	$this->name_single	= 'Chart';
	//Nombre plural para títulos, mensajes a usuario, etc.
	$this->name_plural	= 'Charts';
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
	$this->db_version	= '0.9';
	//Reglas actuales de caracteres a nivel de DB.
	//Dado que esto sólo se usa en la cración de la tabla
	//no se guarda como variable de clase.
	$charset_collate	= $wpdb->get_charset_collate();
	//Sentencia SQL de creación (y ajuste) de la tabla de la clase
	$this->crt_tbl_sql	=	"CREATE TABLE ".$this->tbl_name." (
								id tinyint(1) unsigned not null auto_increment,
								title varchar(40) not null,
								stack_id tinyint(1) unsigned not null,
								legend bool null,
								summary_table bool null,
								group_by_instance bool null,
								time_group_id tinyint(1) unsigned not null,
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
		'title' => array(
			'type'			=>'text',
			'required'		=>true,
			'maxchar'		=>40,
			'in_form'		=>true,
			'desc'			=>'T&iacute;tulo',
			'form-help'		=>'El t&iacute;tulo será desplegado en el gráfico despues del SID.<br/>Tamaño m&aacute;ximo: 40 caracteres.',
			'in_wp_table'	=>true,
			'wp_table_lead'	=>true,
		),
		'stack_id' => array(
			'type'			=>'select',
			'options'		=> array(),
			'required'		=>true,
			'desc'			=>'Apliar',
			'form-help'		=>'No se como explicarlo',
			'in_form'		=>true,
			'sp_form'		=>true,
			'in_wp_table'	=>true,
			'sp_wp_table'	=>true,
		),
		'legend' => array(
			'type'			=>'bool',
			'required'		=>true,
			'desc'			=>'Leyenda',
			'form-help'		=>'Mostrar Leyenda al final del gr&aacute;fico',
			'in_form'		=>true,
			'sp_form'		=>true,
			'in_wp_table'	=>true,
			'sp_wp_table'	=>true,
		),
		'summary_table' => array(
			'type'			=>'bool',
			'required'		=>true,
			'desc'			=>'Tabla de resumen',
			'form-help'		=>'Mostrar tabla de resumen despu&eacute;s del gr&aacute;fico.',
			'in_form'		=>true,
			'sp_form'		=>true,
			'in_wp_table'	=>true,
			'sp_wp_table'	=>true,
		),
		'group_by_instance' => array(
			'type'			=>'bool',
			'required'		=>true,
			'desc'			=>'Agrupar por instancia',
			'form-help'		=>'Agrupar la informaci&oacute;n por instancias.',
			'in_form'		=>true,
			'sp_form'		=>true,
			'in_wp_table'	=>true,
			'sp_wp_table'	=>true,
		),
		'time_group_id' => array(
			'type'			=>'select',
			'options'		=> array(),
			'source'		=>'',
			'required'		=>true,
			'desc'			=>'Intervalo',
			'form-help'		=>'El intervalo indica como se agrupar&aacute;n los datos. Es importante considerar que dependiendo del origen de los datos, hay diferentes indicadores que no pueden ser mostrados en intervalos peque&ntilde;os.<br/>Por ejemplo la informaci&oacute;n de la ST02 solo genera informaci&ocute;n en una base diaria.',
			'in_form'		=>true,
			'sp_form'		=>true,
			'in_wp_table'	=>true,
			'sp_wp_table'	=>true,
		),
		'graph' => array(
			'type'			=>'display',
			'required'		=>false,
			'desc'			=>'Gr&aacute;ficos',
			'in_form'		=>false,
			'in_wp_table'	=>true,
			'sp_wp_table'	=>true,
		),
	);
	register_activation_hook(WP_PLUGIN_DIR."/abap_analyzer/"."index.php", array( $this, 'db_install') );
	register_activation_hook(WP_PLUGIN_DIR."/abap_analyzer/"."index.php", array( $this, 'db_install_data') );
	add_action('admin_menu'							,array( $this , "register_submenu_page"		));
	add_action( 'wp_ajax_fe_build_chart'			,array( $this , 'fe_build_chart'			));
}
protected function sp_wp_table_stack_id($id){
    global $CHART_STACK;
    $response = $CHART_STACK->get_single($id);
    return $response['short_name'];
}
protected function sp_form_stack_id(){
    global $CHART_STACK;
    $response = array();
    foreach($CHART_STACK->get_all() as $key => $value){
        $response[$value['id']] = $value['short_name'];
    }
    return $response;
}
protected function sp_wp_table_time_group_id($id){
    global $TIME_GROUP;
    $response = $TIME_GROUP->get_single($id);
    return $response['short_name'];
}
protected function sp_form_time_group_id(){
    global $TIME_GROUP;
    $response = array();
    foreach($TIME_GROUP->get_all() as $key => $value){
        $response[$value['id']] = $value['short_name'];
    }
    return $response;
}
protected function sp_wp_table_legend($val){
    if($val == 1){
        return'<i class="fa fa-check-square-o fa-fw" aria-hidden="true"></i>';
    }
    return'<i class="fa fa-square-o fa-fw" aria-hidden="true"></i>';
}
protected function sp_wp_table_summary_table($val){
    if($val){
        return'<i class="fa fa-check-square-o fa-fw" aria-hidden="true"></i>';
    }
    return'<i class="fa fa-square-o fa-fw" aria-hidden="true"></i>';
}
protected function sp_wp_table_group_by_instance($val){
    if($val){
        return'<i class="fa fa-check-square-o fa-fw" aria-hidden="true"></i>';
    }
    return'<i class="fa fa-square-o fa-fw" aria-hidden="true"></i>';
}
protected function sp_wp_table_graph($value=null,$id=null){
	global $CHART_GRAPH;
	$graphs=$CHART_GRAPH->get_graphs($id);
	$response ='';
	$QS = http_build_query(array_merge($_GET, array("action"=>$this->class_name.'_graph',"item"=>$id)));
	$URL=htmlspecialchars('?'.$QS);
	$response.='<a href="'.$URL.'" class="">Modificar</a>';
	$response.='';
	$response.='<br/><small>('.sizeof($graphs).' gr&aacute;ficas)</small></div>';
	return $response;
}
protected function chart_graph(){
	global $CHART_GRAPH;
	$id=$_GET['item'];
	return $CHART_GRAPH->special_form($id);
}
public function fe_build_chart(){
	$response=array();
	$chart_id=$_POST['chart_id'];
	$report_id=$_POST['report_id'];
	if(NULL != $chart_id){
		$chart=self::get_single($chart_id);
		
		global $REPORT;
		$report=$REPORT->get_single($report_id);
		
		global $CHART_GRAPH;
		$graphs_id=$CHART_GRAPH->get_graphs($chart_id);
		
		global $CHART_STACK;
		$stack = $CHART_STACK->get_single($chart['stack_id']);
		$chart['stackable']=$stack['code'];

		global $TIME_GROUP;
		$time_group = $TIME_GROUP->get_single($chart['time_group_id']);
		$chart['timeGroup']=$time_group['code'];
		
		$chart['graphs']=array();
		$data=array();
//		self::write_log($chart_id);
		set_time_limit(600);
		foreach($graphs_id as $graph_id){
			/*
			por cada gráfico obtengo
			short_name varchar(30) not null,
			name varchar(80) null,
			asset_id tinyint(1) unsigned not null,
			graph_function_id tinyint(1) unsigned not null,
			graph_type_id tinyint(1) unsigned not null,
			graph_color_id tinyint(1) unsigned not null,
			*/
			global $GRAPH;
			$graph=$GRAPH->get_single($graph_id);


			global $ASSET;
			$asset=$ASSET->get_single($graph['asset_id']);
			$asset_source_id=$asset['source_id'];
			$graph['asset']=$asset['col_name'];

			global $GRAPH_COLOR;
			$color=$GRAPH_COLOR->get_single($graph['graph_color_id']);
			$graph['graphColor']=$color['hex'];

			global $GRAPH_TYPE;
			$type=$GRAPH_TYPE->get_single($graph['graph_type_id']);
			$graph['graphType']=$type['code'];

			global $GRAPH_FUNCTION;
			$function=$GRAPH_FUNCTION->get_single($graph['graph_function_id']);
			$graph['graphFunction']=$function['code'];

			$graph['valueField']=$graph['asset'].'-'.$graph['graphFunction'].'-'.$graph['graphType'].'-'.$graph['graphColor'];
			$graph['valueField']='valueField_'.$graph_id;

			global $SDFMON;
			if( $function['code'] == 'p95' ){
				$count="SELECT count(*) from $SDFMON->tbl_name 
						WHERE system_id=".$report['system_id']." ";
					if($report['start_date'] != null && $report['start_date'] != '0000-00-00'){
						$sql.=" AND date > '".$report['start_date']."' ";
					}
					if($report['end_date'] != null && $report['end_date'] != '0000-00-00'){
						$sql.=" AND date < '".$report['end_date']."' ";
					}
					$sql.="
							GROUP BY date,time";
					if($chart['group_by_instance']){
						$sql.=",servername";
					}
				
			}else{
				$sql="SELECT
						time.date as date,
						time.time as time,
						".strtoupper($function['code'])."(time.sum) as ".$function['code']."
					FROM (
						SELECT
							SUM(".$graph['asset'].") as sum,
							date as date,
							time as time
						FROM $SDFMON->tbl_name
						WHERE system_id=".$report['system_id']." ";
				if($report['start_date'] != null && $report['start_date'] != '0000-00-00'){
					$sql.=" AND date > '".$report['start_date']."' ";
				}
				if($report['end_date'] != null && $report['end_date'] != '0000-00-00'){
					$sql.=" AND date < '".$report['end_date']."' ";
				}
				$sql.="
						GROUP BY date,time";
				if($chart['group_by_instance']){
					$sql.=",servername";
				}
				$sql.="
					) AS time";
			}
			switch($time_group['code']){
				case "hourly":
					$sql.=" GROUP BY HOUR(time.time)";
					break;
				case "daily":
					$sql.=" GROUP BY time.date";
					break;
				case "weekly":
					$sql.=" GROUP BY YEARWEEK(time.date)";
					break;
				case "monthly":
					$sql.=" GROUP BY MONTH(time.date)";
					break;
				case "quarter":
					$sql.=" GROUP BY QUARTER(time.date)";
					break;
			}
//			self::write_log($sql);

			$sdfmon=self::get_sql($sql);
			foreach($sdfmon as $sdfentry){
				switch($time_group['code']){
					case "hourly":
						$key = array_search($sdfentry['time'], array_column($data, 'time'),TRUE);
						break;
					default:
						$key = array_search($sdfentry['date'], array_column($data, 'date'),TRUE);
				}
				
				if($key !== FALSE){
					$data[$key][$graph['valueField']]=round($sdfentry[$function['code']]);
				}else{
					switch($time_group['code']){
						case "hourly":
							array_push($data,
								array(
									"time"					=>	$sdfentry['time'],
									$graph['valueField']	=>	round($sdfentry[$function['code']]),
								)
							);
							break;
						default:
							array_push($data,
								array(
									"date"					=>	$sdfentry['date'],
									$graph['valueField']	=>	round($sdfentry[$function['code']]),
								)
							);
					}
				}
			}
			$chart['graphs']['graph_'.$graph_id]=$graph;
		}
		$chart['data']=$data;
		$response['chart']=$chart;
	}else{
		$response['error']=1;
	}
//	self::write_log($response);
	$response['status']=$chart_id;
	echo json_encode($response);
	die();		
}
public function db_install_data(){
	global $wpdb;
	$count =intval($wpdb->get_var( "SELECT COUNT(*) FROM ".$this->tbl_name));
	if($count == 0){
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'					=> 1,
				'title'					=> 'WPs Activos por día',
				'stack_id'				=> 1,
				'legend'				=> 1,
				'summary_table'			=> 0,
				'group_by_instance'		=> 0,
				'time_group_id'			=> 2,
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'					=> 2,
				'title'					=> 'WPs de Dialogo Activos por día',
				'stack_id'				=> 1,
				'legend'				=> 1,
				'summary_table'			=> 0,
				'group_by_instance'		=> 0,
				'time_group_id'			=> 2,
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'					=> 3,
				'title'					=> 'DAY RFC AVAL',
				'stack_id'				=> 1,
				'legend'				=> 1,
				'summary_table'			=> 0,
				'group_by_instance'		=> 0,
				'time_group_id'			=> 2,
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'					=> 4,
				'title'					=> 'DAY DIA QUEUE',
				'stack_id'				=> 1,
				'legend'				=> 1,
				'summary_table'			=> 0,
				'group_by_instance'		=> 0,
				'time_group_id'			=> 2,
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'					=> 5,
				'title'					=> 'DAY UPD QUEUE',
				'stack_id'				=> 1,
				'legend'				=> 1,
				'summary_table'			=> 0,
				'group_by_instance'		=> 0,
				'time_group_id'			=> 2,
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'					=> 6,
				'title'					=> 'DAY SESSIONS',
				'stack_id'				=> 1,
				'legend'				=> 1,
				'summary_table'			=> 0,
				'group_by_instance'		=> 0,
				'time_group_id'			=> 2,
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'					=> 7,
				'title'					=> 'DAY EM UTIL',
				'stack_id'				=> 1,
				'legend'				=> 1,
				'summary_table'			=> 0,
				'group_by_instance'		=> 0,
				'time_group_id'			=> 2,
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'					=> 8,
				'title'					=> 'DAY HEAP UTIL',
				'stack_id'				=> 1,
				'legend'				=> 1,
				'summary_table'			=> 0,
				'group_by_instance'		=> 0,
				'time_group_id'			=> 2,
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'					=> 9,
				'title'					=> 'DAY PAGE UTIL',
				'stack_id'				=> 1,
				'legend'				=> 1,
				'summary_table'			=> 0,
				'group_by_instance'		=> 0,
				'time_group_id'			=> 2,
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'					=> 10,
				'title'					=> 'DAY ROLL UTIL',
				'stack_id'				=> 1,
				'legend'				=> 1,
				'summary_table'			=> 0,
				'group_by_instance'		=> 0,
				'time_group_id'			=> 2,
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'					=> 11,
				'title'					=> 'DAY ENQ QUEUE',
				'stack_id'				=> 1,
				'legend'				=> 1,
				'summary_table'			=> 0,
				'group_by_instance'		=> 0,
				'time_group_id'			=> 2,
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'					=> 12,
				'title'					=> 'DAY CPU UTIL',
				'stack_id'				=> 1,
				'legend'				=> 1,
				'summary_table'			=> 0,
				'group_by_instance'		=> 0,
				'time_group_id'			=> 2,
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'					=> 13,
				'title'					=> 'WPs Activos por hora',
				'stack_id'				=> 1,
				'legend'				=> 1,
				'summary_table'			=> 0,
				'group_by_instance'		=> 0,
				'time_group_id'			=> 2,
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'					=> 14,
				'title'					=> 'HOUR DIA ACT WPs',
				'stack_id'				=> 1,
				'legend'				=> 1,
				'summary_table'			=> 0,
				'group_by_instance'		=> 0,
				'time_group_id'			=> 2,
			) 
		);
		$wpdb->insert(
			$this->tbl_name,
			array(
				'id'					=> 15,
				'title'					=> 'Sesiones Activas por hora',
				'stack_id'				=> 1,
				'legend'				=> 1,
				'summary_table'			=> 0,
				'group_by_instance'		=> 0,
				'time_group_id'			=> 2,
			) 
		);
	}
}

//END OF CLASS	
}

global $CHART;
$CHART =new AA_CHART_CLASS();
?>