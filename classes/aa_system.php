<?php
defined('ABSPATH') or die("No script kiddies please!");

class AA_SYSTEM_CLASS extends AA_CLASS{

/**
* Esta función es llamada apenas se crea la clase.
* Es utilizada para instanciar las diferentes clases con su información vital.
*
*/
public function __construct(){
	global $wpdb;
	global $aa_vars;
	//como se definió en aa_vars
	$this->class_name	= 'system';
	//Nombre singular para títulos, mensajes a usuario, etc.
	$this->name_single	= 'Sistema';
	//Nombre plural para títulos, mensajes a usuario, etc.
	$this->name_plural	= 'Sistemas';
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
								sid varchar(30) not null,
								short_name varchar(30) null,
								creator_id bigint(20) not null,
								creation_datetime datetime not null default current_timestamp,
								owner_id bigint(20) not null,
								collab_opt_id tinyint(1) unsigned not null,
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
		'sid' => array(
			'type'			=>'text',
			'required'		=>true,
			'maxchar'		=>3,
			'desc'			=>'SID',
			'form-help'		=>'El identificador del sistema tambi&eacute;n es utilizado en los gr&aacute;ficos y validado contra archivos de configuraci&oacute;n.',
			'in_wp_table'	=>true,
			'wp_table_lead'	=>true,
//			'form_size'		=>'form-group-lg',
		),
		'short_name' => array(
			'type'			=>'text',
			'required'		=>false,
			'maxchar'		=>30,
			'desc'			=>'Nombre Corto',
			'form-help'		=>'El nombre corto permite identificar el sistema con una personalizaci&oacute;n diferente al SID. <br/>Por ejemplo, para sistemas CLON.<br/>Tamaño m&aacute;ximo: 30 caracteres.',
			'in_wp_table'	=>true,
		),
		'creator_id' => array(
			'type'			=>'current_user_id',
			'required'		=>false,
			'maxchar'		=>null,
			'desc'			=>'Usuario creador',
			'form-help'		=>'',
			'in_form'		=>false,
			'in_wp_table'	=>true,
			'in_wp_table'	=>true,
			'sp_wp_table'	=>true,
		),
		'owner_id' => array(
			'type'			=>'current_user_id',
			'required'		=>false,
			'maxchar'		=>null,
			'desc'			=>'Usuario due&ntilde;o',
			'form-help'		=>'',
			'in_form'		=>false,
			'in_wp_table'	=>true,
			'sp_wp_table'	=>true,
		),
		'creation_datetime' => array(
			'type'			=>'timestamp',
			'required'		=>false,
			'maxchar'		=>60,
			'desc'			=>'Registro de creación',
			'form-help'		=>null,
			'in_form'		=>false,
			'in_wp_table'	=>false,
		),
		'collab_opt_id' => array(
			'type'			=>'select',
			'options'		=> array(),
			'source'		=>'',
			'required'		=>true,
			'desc'			=>'Opci&oacute;n de colaboraci&oacute;n',
			'form-help'		=>'Esta opci&oacute;n permite que diferentes usuarios (<mark>nadie</mark>, <mark>algunos</mark> o <mark>todos</mark>) puedan incluir informaci&oacute;n t&eacute;cnica dentro del sistema.',
			'in_form'		=>true,
			'sp_form'		=>true,
			'in_wp_table'	=>true,
			'sp_wp_table'	=>true,
		),
	);
	register_activation_hook(WP_PLUGIN_DIR."/abap_analyzer/"."index.php", array( $this, 'db_install') );
	add_action('admin_menu', 						array( $this , "register_submenu_page"		));
	add_action( 'wp_ajax_search_system_users', 		array( $this , 'search_system_users'		));
	add_action( 'wp_ajax_fe_system_list',			array( $this , 'fe_system_list'				));
	add_action( 'wp_ajax_fe_system_info',			array( $this , 'fe_system_info'				));
	add_action( 'wp_ajax_fe_quick_system_info',		array( $this , 'fe_quick_system_info'		));
	add_action( 'wp_ajax_fe_system_show_form',		array( $this , 'fe_system_show_form'		));
	add_action( 'wp_ajax_fe_create_system',			array( $this , 'fe_create_system'			));
}
protected function sp_wp_table_creator_id($user_id){
	$user_info = get_userdata($user_id);
      return  $user_info->user_login.'<br/><small>('.$user_info->user_email.')</small>';
}
protected function sp_wp_table_owner_id($user_id){
	$user_info = get_userdata($user_id);
      return  $user_info->user_login.'<br/><small>('.$user_info->user_email.')</small>';
}
protected function sp_wp_table_collab_opt_id($opt,$id){
	$response="Error";
	if($opt == NULL || $opt == 1){
		//none
		$response ='<div class="text-center"><i class="fa fa-user-times fa-fw" aria-hidden="true"></i></div>';
	}elseif($opt == 2){
		global $SYSTEM_COLLAB;
		$collab=$SYSTEM_COLLAB->get_users($id);
		//some
		$response ='<div class="text-center"><i class="fa fa-user-plus fa-fw" aria-hidden="true"></i>';
		$QS = http_build_query(array_merge($_GET, array("action"=>$this->class_name.'_collab',"item"=>$id)));
		$URL=htmlspecialchars("$_SERVER[PHP_SELF]?$QS");
		$response.='<a href="'.$URL.'" class="">Modificar</a>';
		$response.='';
		$response.='<br/><small>('.sizeof($collab).' colaboradores)</small></div>';
	}elseif($opt == 3){
		//all
		$response ='<div class="text-center"><i class="fa fa-users fa-fw fa-2x" aria-hidden="true"></i></div>';
	}
	return $response;
}
protected function system_collab(){
	global $SYSTEM_COLLAB;
	$id=$_GET['item'];
	return $SYSTEM_COLLAB->special_form($id);
}
protected function sp_form_collab_opt_id(){
	global $COLLAB_OPT;
	$response = array();
	foreach($COLLAB_OPT->get_all() as $key => $value){
		$response[$value['id']] = $value['short_name'];
	}
	return $response;
}
public function fe_system_list(){
	$response=array();
	$response['data']=array();
	$i=0;
	foreach( $this->get_all() as $key => $value){
//		$response['data']['elem_html_'.$key]='<li class="animated flipInX col-xs-12 col-sm-6 col-md-4 col-lg-4">';
		$response['data']['elem_html_'.$key]='<li class="col-xs-12 col-sm-6 col-md-4 col-lg-4">';
			$response['data']['elem_html_'.$key].='<div class="panel panel-default">';
				$response['data']['elem_html_'.$key].='<div class="panel-heading"><h4 class="panel-title">'.$value['sid'].'</h4></div>';
				$response['data']['elem_html_'.$key].='<div class="panel-body">';
					$response['data']['elem_html_'.$key].='<p><i class="fa fa-info fa-fw" aria-hidden="true"></i> '.$value['short_name'].'</p>';
					$response['data']['elem_html_'.$key].='<p><i class="fa fa-user-circle-o fa-fw" aria-hidden="true"></i> ';
					$response['data']['elem_html_'.$key].=get_userdata($value['owner_id'])->user_login;
					global $SYSTEM_COLLAB;
					$users=$SYSTEM_COLLAB->get_users($value['id']);
					switch($value['collab_opt_id']){
						case 2:
							if( get_current_user_id() == $value['owner_id']){
								$response['data']['elem_html_'.$key].=' <a href="#system-collab/'.$value['id'].'">(+'.count($users).' colaboradores)</a>';
							}else{
								$response['data']['elem_html_'.$key].=' (+'.count($users).' colaboradores)';								
							}
							break;
						case 3:
							$response['data']['elem_html_'.$key].=' <small>(+Todo Novis)</small>';
							break;
						default:
					}
					$response['data']['elem_html_'.$key].='</p>';
					$response['data']['elem_html_'.$key].='<p><i class="fa fa-calendar fa-fw" aria-hidden="true"></i> '.$value['creation_datetime'].'</p>';
					$response['data']['elem_html_'.$key].='<div class="btn-group btn-group-justified" role="group" aria-label="...">';
					array_push($users, $value['owner_id']);
					if( in_array(get_current_user_id(), $users)	){
						$response['data']['elem_html_'.$key].='<a data-function="system-edit" data-system-id="'.$value['id'].'" class="btn btn-warning "><i class="fa fa-pencil-square-o fa-fw" aria-hidden="true"></i> Modificar</a>';
					}
					$response['data']['elem_html_'.$key].='<a href="#system-info/'.$value['id'].'" class="btn btn-default"><i class="fa fa-binoculars fa-fw" aria-hidden="true"></i> Visualizar</a>';
				$response['data']['elem_html_'.$key].='</div>';
			$response['data']['elem_html_'.$key].='</div>';
		$response['data']['elem_html_'.$key].='</li>';
		$i++;
	}
	$response['elementCount']=$i;
	$response['status']='ok';
	echo json_encode($response);
	die();	
}
public function fe_system_info(){
	$response=array();
	$system=$this->get_single($_POST['system_id']);
	$date = date_create_from_format('Y-m-d G:i:s',$system['creation_datetime'])->format('d/m/Y');
	$response['systemInfo']='<header><h1>'.$system['sid'].'</h1><P class="lead">'.$system['short_name'].'</p></header>';
	$response['systemInfo'].='<p class="text-justify">El sistema '.$system['sid'].' <small><mark>'.$system['short_name'].'</mark></small> fue creado por '.get_userdata($system['owner_id'])->user_login.' ('.get_userdata($system['owner_id'])->user_email.') el '.$date.'.</p>';
	$response['systemInfo'].='<div class="system-info col-sm-12 col-md-4">';
//	self::write_log($date);
	$response['systemInfo'].='<h2>Colaboradores</h2>';
	if($system['collab_opt_id'] == NULL || $system['collab_opt_id'] == 1){
		$response['systemInfo'].='<p>';
		$response['systemInfo'].='Este sistema no permite colaboradores.';
		$response['systemInfo'].='</p>';
	}elseif($system['collab_opt_id'] == 2){
		$response['systemInfo'].='<p>';
		global $SYSTEM_COLLAB;
		$collab=$SYSTEM_COLLAB->get_users($system['id']);
		$response['systemInfo'].='Este sistema tiene '.sizeof($collab).' colaboradores.';
		if(count($collab)>0){
			$response['systemInfo'].='<ul>';
//			$response['systemInfo'].='<li><kbd>'.get_userdata($system['owner_id'])->user_email.'</kbd></li>';
			foreach($collab as $user_id){
				$response['systemInfo'].='<li>'.get_userdata($user_id)->user_email.'</li>';
			}
			$response['systemInfo'].='</ul>';
		}
		$response['systemInfo'].='</p>';
		
	}else{
		$response['systemInfo'].='<p>';
		$response['systemInfo'].='Este sistema permite que todos los usuarios agreguen informaci&oacute;n.';
		$response['systemInfo'].='</p>';
	}
	$response['systemInfo'].="</div>";
	$response['systemInfo'].='<div class="system-data-suppliers col-sm-12 col-md-4">';
	$response['systemInfo'].='<h2>Data Suppliers</h2>';
	$response['systemInfo'].='<ul class="list-group">';
	$response['systemInfo'].='<li class="list-group-item">';
	$response['systemInfo'].='<h4 class="list-group-item-heading"><i class="fa fa-tachometer" aria-hidden="true"></i> Snapshot Monitoring <small>/SDF/MON</small></h4>';
	$response['systemInfo'].='<p class="list-group-item-text">La informaci&oacute;n del Snapshot Monitoring debe ser cargada de manera manual.</p>';
	$response['systemInfo'].='<p class="list-group-item-text"><a href="#sdfmon-setup/'.$system['id'].'" class="btn btn-default btn-block">Agregar registros</a></p>';
	$response['systemInfo'].='</li>';
	$response['systemInfo'].='</ul>';
	$response['systemInfo'].="</div>";

	$response['systemInfo'].='<div class="system-reports col-sm-12 col-md-4">';
	$response['systemInfo'].='<h2>Reportes <small>(<a href="#" id="new-report-button" data-system-id="'.$system['id'].'">Crear nuevo</a>)</small></h2>';
//	$response['systemInfo'].='<p class="list-group-item-text"><a href="#sdfmon-setup/'.$system['id'].'" class="btn btn-default btn-block">Crear nuevo reporte</a></p>';
	$response['systemInfo'].='<ul class="list-group">';
	global $REPORT;
	$report_list=$REPORT->get_reports_by_system($system['id']);
	foreach($report_list as $report_id){
		$report=$REPORT->get_single($report_id);
		$response['systemInfo'].='<li class="list-group-item" href="'."#report-preview/".$report_id.'">';
		$response['systemInfo'].='<h4 class="list-group-item-heading"><i class="fa fa-file-text-o" aria-hidden="true"></i> '.$report['short_name'].'</h4>';
		$response['systemInfo'].='<br/>';
		global $REPORT_TYPE;
		$report_type=$REPORT_TYPE->get_single($report['report_type_id']);
		$response['systemInfo'].='<p class="list-group-item-text"><i class="fa fa-file-text" aria-hidden="true"></i> Template: <em>'.$report_type['short_name'].'</em></p>';
		$response['systemInfo'].='<br/>';
		$rep_start_date = date_create_from_format('Y-m-d',$report['start_date'])->format('d/m/Y');
		$rep_end_date = date_create_from_format('Y-m-d',$report['end_date'])->format('d/m/Y');
		$response['systemInfo'].='<p class="list-group-item-text"><i class="fa fa-calendar-minus-o" aria-hidden="true"></i> Fechas: '.$rep_start_date.' - '.$rep_end_date.'</p>';
		$response['systemInfo'].='<br/>';
		$response['systemInfo'].='<p class="list-group-item-text"><i class="fa fa-user-circle" aria-hidden="true"></i> Creador: '.get_userdata($report['creator_id'])->user_login.'</p>';
		$response['systemInfo'].='<br/>';
		$response['systemInfo'].='<div class="btn-group btn-group-justified" role="group" aria-label="...">';
		if( get_current_user_id() == $report['creator_id'] ){
			$response['systemInfo'].='<a data-function="report-edit" data-report-id="'.$report_id.'" class="btn btn-warning "><i class="fa fa-pencil-square-o fa-fw" aria-hidden="true"></i> Modificar</a>';
		}
		$response['systemInfo'].='<a href="#report-preview/'.$report_id.'" class="btn btn-default"><i class="fa fa-binoculars fa-fw" aria-hidden="true"></i> Visualizar</a>';
		$response['systemInfo'].='</div>';
		$response['systemInfo'].='</li>';
	
	}

	$response['systemInfo'].='</ul>';
	$response['systemInfo'].="</div>";



	$response['system']=array();
	$response['system']['id']=$system['id'];
	$response['system']['sid']=$system['sid'];
	$response['system']['shortName']=$system['short_name'];
	$response['system']['collab']='<div class="collab"><h2>Colaboradores</h2>';
	if($system['collab_opt_id'] == NULL || $system['collab_opt_id'] == 1){
		$response['system']['collab'].='<p>';
//		$response['system']['collab'].='<i class="fa fa-user-times fa-fw" aria-hidden="true"></i>';
		$response['system']['collab'].='Este sistema no tiene colaboradores.';
		$response['system']['collab'].='</p>';
	}elseif($system['collab_opt_id'] == 2){
		$response['system']['collab'].='<p>';
//		$response['system']['collab'].='<i class="fa fa-user-times fa-fw" aria-hidden="true"></i>';
		global $SYSTEM_COLLAB;
		$collab=$SYSTEM_COLLAB->get_users($system['id']);
		$response['system']['collab'].='Este sistema tiene '.sizeof($collab).' colaboradores.';
		$response['system']['collab'].='</p>';
		
	}else{
		$response['system']['collab'].='<p>';
//		$response['system']['collab']='<i class="fa fa-users fa-fw fa-2x" aria-hidden="true"></i>';
		$response['system']['collab'].='Este sistema permite que todos los usuarios agreguen informaci&oacute;n.';
		$response['system']['collab'].='</p>';
		
	}
	$response['system']['collab'].='</div>';
	$response['dataSuppliers']=array();
	$response['dataSuppliers']['sdfmon']=array();
	$response['dataSuppliers']['sdfmon']['title']="Snapshot Monitoring";
	$response['dataSuppliers']['sdfmon']['firstDate']="23/08/2016";
	$response['dataSuppliers']['sdfmon']['lastDate']="12/11/2016";
	$response['dataSuppliers']['sdfmon']['editLink']="#sdfmon-setup/".$system['id'];
	$response['dataSuppliers']['sdfmon']['editText']="Modificar";
	$response['status']='ok';
	$response['reports']=array();
	global $REPORT;
	$report_list=$REPORT->get_reports_by_system($system['id']);
	foreach($report_list as $report_id){
		$report=$REPORT->get_single($report_id);
		$response['reports']['rep_'.$report_id]=array();
		$response['reports']['rep_'.$report_id]['id']=$report_id;
		$response['reports']['rep_'.$report_id]['shortName']=$report['short_name'];
		$response['reports']['rep_'.$report_id]['editLink']="#edit-report/".$report_id;
		$response['reports']['rep_'.$report_id]['editText']="Modificar";
		$response['reports']['rep_'.$report_id]['viewLink']="#report-preview/".$report_id;
		$response['reports']['rep_'.$report_id]['viewText']="Visualizar";
	}
	echo json_encode($response);
	die();	
	
}
public function fe_quick_system_info(){
	$response=array();
	$system_id=$_POST['system_id'];
	if( null == $system_id){
		$response['error']=true;
		$response['message']="No hay identificador de Systema";
	}else{
		$response['system']=self::get_single($system_id);
		array_push($response['system']['user'], get_user_by('id',$system_id['owner_id']));
		$response['status']='ok';
		$response['header']='<header class="col-xs-12"><h1>'.$response['system']['sid'].'</h1><p class="lead">'.$response['system']['short_name'].'</p></header>';
	}
	echo json_encode($response);
	die();		
}
public function fe_system_show_form(
					$type='add',			//add,update
					$item=null				//id a editar
){
	global $wpdb;
	$form_type=isset($_POST['form_type']) && $_POST['form_type']!=NULL?$_POST['form_type']:$type;
	$item=isset($_POST['item']) && $_POST['item']!=NULL?$_POST['item']:$item;
//	self::write_log($form_type);
	switch($form_type){
		case 'edit':
			$title='Editar ';
			$subtitle=' <small>(id: '.$item.')</small>';
			$sql="SELECT * FROM ".$this->tbl_name." WHERE id=".$item;
			$element=self::get_single( $item );
			foreach( $this->db_fields as $key => $field){
				if($this->db_fields[$key]['type']!='display'){
					$this->db_fields[$key]['value']=$element[$key];					
				}
			}
			if( $this->db_fields['owner_id']['value'] != get_current_user_id()){
				$response=array();
				$response['message']="Solo el dueño del sistema puede modificar el sistema";
				$response['error']=true;
				echo json_encode($response);
				die();
				
			}
			break;
			//Seleccionar valores
		case 'add':
			$title='Crear nuevo ';
			$subtitle='';
			break;
	}
	$output='';
//	$output.='<div class="">';
//		$output.='<div class="">';
//			$output.='<h3>'.$title.$this->name_single.$subtitle.'</h3>';
//		$output.='</div>';
		$output.='<form
					class="form-horizontal"
					action="#"
					method="post"
					id=""
					>';
	$output.='<input type="hidden" name="'.$this->plugin_post.'[action]" value="'.$form_type.'" />';
	$output.='<input type="hidden" name="'.$this->plugin_post.'[force]" value="0" />';
	$output.=wp_nonce_field( $form_type, $this->plugin_post."[actioncode]",true,false);
	if(isset($item)){
		$output.='<input type="hidden" name="'.$this->plugin_post.'[id]" value="'.$item.'" />';
	}
	foreach ( $this->db_fields as $key => $field ){
		$desc=isset($field['desc'])?$field['desc']:'';
		$form_size=(isset($field['form_size']))?$field['form_size']:'';
		$value=(isset($field['value']))?$field['value']:'';
		$value=(isset($field['value']))?'value="'.$field['value'].'"':'';
		$min=(isset($field['min']))?$field['min']:'';
		$max=(isset($field['max']))?$field['max']:'';
		$id=$this->plugin_post.'['.$key.']';
		$required=($field['required']==TRUE)?'data-validation="true"':'';
		$placeholder=(isset($field['placeholder']))?'placeholder="'.$field['placeholder'].'"':'placeholder="'.$field['desc'].'"';
		if( isset($field['in_form']) && $field['in_form'] == false){
//			$output.='<input type="hidden" id="'.$id.'" name="'.$id.'" value="'.$id.'" />';
		}else{
			$output.='<div class="form-group '.$form_size.'">';
				$output.='<label for="'.$id.'" class="col-sm-2 control-label">'.$desc.'</label>';
				$output.='<div class="col-sm-10">';
				switch($field['type']){
					case 'date':
						$output.='<input
										type="date"
										class="form-control"
										id="'.$id.'"
										name="'.$id.'"
										'.$placeholder.'
										'.$required.'
										'.$value.'
										maxlength="'.$field['maxchar'].'"
										/>';
						break;
					case 'text':
						$output.='<input
										type="text"
										class="form-control"
										id="'.$id.'"
										name="'.$id.'"
										'.$placeholder.'
										'.$required.'
										'.$value.'
										maxlength="'.$field['maxchar'].'"
										/>';
						break;
					case 'textarea':
						$output.='<textarea
										rows="4"
										class="form-control"
										id="'.$id.'"
										name="'.$id.'"
										'.$required.'
										>'.( isset($field['value']) ? $field['value'] : '').'</textarea>';
						break;
					case 'bool':
						$output.='<input
										type="checkbox"
										class="form-control aa-admin-check"
										id="'.$id.'"
										name="'.$id.'"
										'.(isset($field['value']) && ($field['value'] != false)?'checked':'').'
										/>';
							$output.='<label for="'.$id.'">'.$desc.'</label>';
						break;
					case 'number':
						$output.='<input
										type="number"
										class="form-control"
										id="'.$id.'"
										name="'.$id.'"
										placeholder="'.$placeholder.'"
										'.$required.'
										'.$value.'
										min="'.$field['min'].'"
										max="'.$field['max'].'"
										/>';
						break;
					case 'nat_number':
						$output.='<input
										type="number"
										class="form-control"
										id="'.$id.'"
										name="'.$id.'"
										placeholder="'.$placeholder.'"
										'.$required.'
										'.$value.'
										min="'.$field['1'].'"
										max="'.$field['max'].'"
										/>';
						break;
					case 'percentage':
						$output.='<div class="input-group">';
						$output.='<input
										type="number"
										class="form-control"
										id="'.$id.'"
										name="'.$id.'"
										placeholder="'.$placeholder.'"
										'.$required.'
										'.$value.'
										min="'.$field['min'].'"
										max="'.$field['max'].'"
										/>';
						$output.='<span class="input-group-addon" id="basic-addon2">%</span>';
						$output.='</div>';
						break;
						case 'select':
							if(method_exists($this, 'sp_form_'.$key)){
								$options=call_user_func_array(array($this, 'sp_form_'.$key),array());
								if(count($options) == 0){
									$field['options']=array(0 => "No hay informaci&oacute;n");
								}else{
									$field['options']=$options;
								}
							}else{
								$field['options']=array(0 => "No hay informaci&oacute;n");
							}
							$output.='<select
										class="form-control"
										id="'.$id.'"
										name="'.$id.'"
										'.$required.'
										">';
							$output.='<option value="0" disabled>Seleccionar</option>';
							foreach($field['options'] as $sel_key => $sel_opt){
								$output.='<option value="'.$sel_key.'" ';
								
								$output.=isset($field['value']) ? ($sel_key == $field['value'] ? " selected " : '') : '';
								$output.='>'.$sel_opt.'</option>';
							}
							$output.='</select>';
						break;

				}
					$output.='<p class="help-block">'.$field['form-help'].'</p>';
				$output.='</div>';
			$output.='</div>';
			
		}
	}
//	$output.='<div class="form-group '.$form_size.'">';
//		$output.='<div class="col-sm-2 control-label"></div>';
//			$output.='<div class="col-sm-10">';
//				$output.='<a href="#system-list" class="btn btn-default">Cancelar</a>';
//				$msg=($form_type=="add")?"Agregar":"Editar";
//				$output.='<button type="submit" class="btn btn-primary">'.$msg.'</button>';
//			$output.='</div>';
//		$output.='</div>';
//	$output.='</div>';
	$output.='</form>';
//	$output.='</div>';
	if(isset($_POST['action'])){
		$response=array();
		$response['title']="Crear nuevo Sistema";
		$response['element']=$element;
		$response['form']=$output;
		$response['status']='ok';
		echo json_encode($response);
		die();
	}
	return $output;
}
public function fe_create_system(){
	$response=array();
	$post = isset( $_POST[$this->plugin_post] ) &&  $_POST[$this->plugin_post]!=null ? $_POST[$this->plugin_post] : $_POST;
//	$response['data'] = $_POST;
	if( $post != '' ){
		if( isset( $post["action"] ) && isset( $post["actioncode"] ) ){
			if( wp_verify_nonce( $post["actioncode"], $post["action"] ) ){
				switch ( $post["action"] ){
					case 'edit':
						$query=self::update_class_row('edit',$post);
						break;
					default:
						$query=self::update_class_row('add',$post);
						break;
				}
				if($query['status'] == 'ok'){
					$response['status'] = 'ok';
					$response['message']=$query['message'];
				}else{
					$response['error']='true';
					$response['message']=$query['message'];
				}
			}else{
				$response['error']=true;
				$response['message'] = '<div class="alert alert-danger" role="alert">Error de validación de seguridad (wp_verify_nonce).</div>';
			}
		}else{
			$response['error']=true;
			$response['message'] = '<div class="alert alert-danger" role="alert">Error de validación de variables post (action & actioncode).</div>';			
		}
	}else{
			$response['error']=true;
			$response['message'] = 'No llegó el post';			
		
	}
	echo json_encode($response);
	die();
}
	
//END OF CLASS	
}

global $SYSTEM;
$SYSTEM =new AA_SYSTEM_CLASS();



//add_action( 'admin_notices', array( $SYSTEM, 'db_install_error')  );
?>