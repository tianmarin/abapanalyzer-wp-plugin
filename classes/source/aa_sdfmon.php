<?php
defined('ABSPATH') or die("No script kiddies please!");

class AA_SDFMON_CLASS extends AA_CLASS{

/**
* Esta función es llamada apenas se crea la clase.
* Es utilizada para instanciar las diferentes clases con su información vital.
*
*/
public function __construct(){
	global $wpdb;
	global $aa_vars;
	//como se definió en aa_vars
	$this->class_name	= 'sdfmon';
	//Nombre singular para títulos, mensajes a usuario, etc.
	$this->name_single	= 'Snapshot Monitoring';
	//Nombre plural para títulos, mensajes a usuario, etc.
	$this->name_plural	= 'Snapshots Monitoring';
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
								id int unsigned not null auto_increment,
								system_id tinyint(1) unsigned not null,
								filename varchar(50) not null,
								date date not null,
								time time not null,
								servername varchar(50) not null,
								act_wps smallint(5) unsigned null,
								dia_wps smallint(5) unsigned null,
								rfc_wps smallint(5) unsigned null,
								cpu_usr float unsigned null,
								cpu_sys float unsigned null,
								cpu_idle float unsigned null,
								cpu_ava float unsigned null,
								page_in smallint(5) unsigned null,
								page_out smallint(5) unsigned null,
								free_mem int(10) unsigned null,
								em_alloc int(10) unsigned null,
								em_attach int(10) unsigned null,
								em_global int(10) unsigned null,
								heap int(10) unsigned null,
								priv_mode smallint(5) unsigned null,
								page_mem int(10) unsigned null,
								roll_mem int(10) unsigned null,
								queue_dia smallint(5) unsigned null,
								queue_upd smallint(5) unsigned null,
								queue_enq smallint(5) unsigned null,
								logins smallint(5) unsigned null,
								sessions smallint(5) unsigned null,
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
		required		:	Flag de obligatoriedad del dato (not null)
							true|false
		maxchar			:	Máximo número de caracters (valido solo para inputs tipo texto)
							- número
							- null (es indefinido)
		field_desc		:	Descripción del campo utilizado en formualrios y mensajes al usuario
		*/
		'id' => array(
			'type'			=>'id',
			'required'		=>false,
			'maxchar'		=>null,
			'desc'			=>'id',
			'form-help'		=>'',
			'in_form'		=>false,
			'in_wp_table'	=>true,
		),
		'system_id' => array(
			'type'			=>'select',
			'options'		=> array(),
			'required'		=>false,
			'desc'			=>'Sistema',
			'in_wp_table'	=>true,
			'in_form'		=>false,
			'sp_wp_table'	=>true,
			'wp_table_lead'	=>true,
		),
		'filename' => array(
			'type'			=>'time',
			'required'		=>false,
			'in_form'		=>false,
			'in_wp_table'	=>false,
		),
		'date' => array(
			'type'			=>'date',
			'required'		=>false,
			'in_form'		=>false,
			'in_wp_table'	=>false,
		),
		'time' => array(
			'type'			=>'time',
			'required'		=>false,
			'in_form'		=>false,
			'in_wp_table'	=>false,
		),
		'servername' => array(
			'type'			=>'text',
			'required'		=>false,
			'in_form'		=>false,
			'in_wp_table'	=>false,
		),
		'act_wps' => array(
			'type'			=>'nat_number',
			'required'		=>false,
			'in_form'		=>false,
			'in_wp_table'	=>false,
		),
		'dia_wps' => array(
			'type'			=>'nat_number',
			'required'		=>false,
			'in_form'		=>false,
			'in_wp_table'	=>false,
		),
		'rfc_wps' => array(
			'type'			=>'nat_number',
			'required'		=>false,
			'in_form'		=>false,
			'in_wp_table'	=>false,
		),
		'cpu_usr' => array(
			'type'			=>'nat_number',
			'required'		=>false,
			'in_form'		=>false,
			'in_wp_table'	=>false,
		),
		'cpu_sys' => array(
			'type'			=>'nat_number',
			'required'		=>false,
			'in_form'		=>false,
			'in_wp_table'	=>false,
		),
		'cpu_idle' => array(
			'type'			=>'nat_number',
			'required'		=>false,
			'in_form'		=>false,
			'in_wp_table'	=>false,
		),
		'cpu_ava' => array(
			'type'			=>'nat_number',
			'required'		=>false,
			'in_form'		=>false,
			'in_wp_table'	=>false,
		),
		'page_in' => array(
			'type'			=>'nat_number',
			'required'		=>false,
			'in_form'		=>false,
			'in_wp_table'	=>false,
		),
		'page_out' => array(
			'type'			=>'nat_number',
			'required'		=>false,
			'in_form'		=>false,
			'in_wp_table'	=>false,
		),
		'free_mem' => array(
			'type'			=>'nat_number',
			'required'		=>false,
			'in_form'		=>false,
			'in_wp_table'	=>false,
		),
		'em_alloc' => array(
			'type'			=>'nat_number',
			'required'		=>false,
			'in_form'		=>false,
			'in_wp_table'	=>false,
		),
		'em_attach' => array(
			'type'			=>'nat_number',
			'required'		=>false,
			'in_form'		=>false,
			'in_wp_table'	=>false,
		),
		'em_global' => array(
			'type'			=>'nat_number',
			'required'		=>false,
			'in_form'		=>false,
			'in_wp_table'	=>false,
		),
		'heap' => array(
			'type'			=>'nat_number',
			'required'		=>false,
			'in_form'		=>false,
			'in_wp_table'	=>false,
		),
		'priv_mode' => array(
			'type'			=>'nat_number',
			'required'		=>false,
			'in_form'		=>false,
			'in_wp_table'	=>false,
		),
		'page_mem' => array(
			'type'			=>'nat_number',
			'required'		=>false,
			'in_form'		=>false,
			'in_wp_table'	=>false,
		),
		'roll_mem' => array(
			'type'			=>'nat_number',
			'required'		=>false,
			'in_form'		=>false,
			'in_wp_table'	=>false,
		),
		'queue_dia' => array(
			'type'			=>'nat_number',
			'required'		=>false,
			'in_form'		=>false,
			'in_wp_table'	=>false,
		),
		'queue_upd' => array(
			'type'			=>'nat_number',
			'required'		=>false,
			'in_form'		=>false,
			'in_wp_table'	=>false,
		),
		'queue_enq' => array(
			'type'			=>'nat_number',
			'required'		=>false,
			'in_form'		=>false,
			'in_wp_table'	=>false,
		),
		'logins' => array(
			'type'			=>'nat_number',
			'required'		=>false,
			'in_form'		=>false,
			'in_wp_table'	=>false,
		),
		'sessions' => array(
			'type'			=>'nat_number',
			'required'		=>false,
			'in_form'		=>false,
			'in_wp_table'	=>false,
		),
	);
	register_activation_hook(WP_PLUGIN_DIR."/abap_analyzer/"."index.php", array( $this, 'db_install') );
	add_action('admin_menu', array( $this , "register_submenu_page" ) );
	add_action( 'wp_ajax_search_report_users',		array( $this , 'search_report_users'	));
	add_action( 'wp_ajax_fe_sdfmon_get_dates',		array( $this , 'fe_sdfmon_get_dates'	));
	add_action( 'wp_ajax_fe_sdfmon_file_upload',	array( $this , 'fe_sdfmon_file_upload'	));
}
protected function sp_wp_table_system_id($id){
    global $AA_SYSTEM;
    $response = $AA_SYSTEM->get_single($id);
    return $response['sid'].' ('.$response['short_name'].')';
}
public function fe_sdfmon_get_dates(){
	$response=array();
	if(isset($_POST['system_id']) && $_POST['system_id']!=''){
		if(true){
			$dates=array();
			$sql="SELECT
					date
				FROM  ".$this->tbl_name ."
				WHERE system_id=".$_POST['system_id']."
				GROUP BY date
				ORDER BY date ASC";
			$sdfmon= $this->get_sql($sql);
			foreach($sdfmon as $sdfentry){
				array_push($dates,$sdfentry['date']);
			}
			$response['status'] = 'ok';
			$response['dates'] = $dates;
		}else{
			$response['status'] = 'error';
			$response['message'] = "Usuario sin permisos";
			$response['userMessage'] ="No tienes permisos.<br/>";
			$response['userMessage'].="quieres pedir pemiroso.";
		}
	}else{
		$response['status'] = 'error';
		$response['message'] = "No hay id de sistema";
		$response['userMessage'] ="No hemos obtenido el identificador del sistema.<br/>";
		$response['userMessage'].="Si has seguido el link de otro lado, puede que esté malo.";
	}
	echo json_encode($response);
	die();	
}
public function fe_sdfmon_file_upload(){
	$response=array();
	$c=0;
	set_time_limit(600);
	$time=$servername=$act_wps=$dia_wps=$rfc_wps=$cpu_usr=$cpu_sys=$cpu_idle=$cpu_ava=$page_in=$page_out=$free_mem=$em_alloc=$em_attach=$em_global=$heap=$priv_mode=$page_mem=$roll_mem=$queue_dia=$queue_upd=$queue_enq=$logins=$sessions=NULL;
	$date=$_POST['year']."/".$_POST['month']."/".$_POST['day'];
	$system_id=intval($_POST['system_id']);
	if($system_id != 0){
		$filename=$_FILES['file']['name'];
		$fp = fopen($_FILES['file']['tmp_name'], 'rb');
		while(  ($line=fgets($fp))  !==  false){
			if($line[0] == "|"){
				$line=str_replace(' ','',$line);
				$info=explode("|",$line);
				if( !isset($definition) ){
//					self::write_log($line);
					if( count($info) > 10){
						if(!is_numeric($info[3])){
							for($i=0;$i<count($info);$i++){
								$valid_percentage=90;
								//Time
								$text=array("Time","Fecha");
								if(self::check_similarity($text,$info[$i]) >= $valid_percentage){
									$time=$i;
									$definition=TRUE;
								}
								//Instance
								$text="ASInstance";
								if(self::check_similarity($text,$info[$i]) >= $valid_percentage){
									$servername=$i;
									$definition=TRUE;
								}
								//act_wps
								$text="Act.WPs";
								if(self::check_similarity($text,$info[$i]) >= $valid_percentage){
									$act_wps=$i;
									$definition=TRUE;
								}
								//dia_wps
								$text="Dia.WPs";
								if(self::check_similarity($text,$info[$i]) >= $valid_percentage){
									$dia_wps=$i;
									$definition=TRUE;
								}
								//rfc_wps
								$text="RFCWPs";
								if(self::check_similarity($text,$info[$i]) >= $valid_percentage){
									$rfc_wps=$i;
									$definition=TRUE;
								}
								//cpu_usr
								$text="CPUUsr";
								if(self::check_similarity($text,$info[$i]) >= $valid_percentage){
									$cpu_usr=$i;
									$definition=TRUE;
								}
								//cpu_sys
								$text="CPUSys";
								if(self::check_similarity($text,$info[$i]) >= $valid_percentage){
									$cpu_sys=$i;
									$definition=TRUE;
								}
								//cpu_idle
								$text="CPUIdle";
								if(self::check_similarity($text,$info[$i]) >= $valid_percentage){
									$cpu_idle=$i;
									$definition=TRUE;
								}
								//cpu_ava
								$text="Ava.";
								if(self::check_similarity($text,$info[$i]) >= $valid_percentage){
									$cpu_ava=$i;
									$definition=TRUE;
								}
								//page_in
								$text="Pagingin";
								if(self::check_similarity($text,$info[$i]) >= $valid_percentage){
									$page_in=$i;
									$definition=TRUE;
								}
								//page_out
								$text="Pagingout";
								if(self::check_similarity($text,$info[$i]) >= $valid_percentage){
									$page_out=$i;
									$definition=TRUE;
								}
								//free_mem
								$text="FreeMem.";
								if(self::check_similarity($text,$info[$i]) >= $valid_percentage){
									$free_mem=$i;
									$definition=TRUE;
								}
								//em_alloc
								$text="EMalloc.";
								if(self::check_similarity($text,$info[$i]) >= $valid_percentage){
									$em_alloc=$i;
									$definition=TRUE;
								}
								//em_attach
								$text="EMattach.";
								if(self::check_similarity($text,$info[$i]) >= $valid_percentage){
									$em_attach=$i;
									$definition=TRUE;
								}
								//em_global
								$text="Emglobal";
								if(self::check_similarity($text,$info[$i]) >= $valid_percentage){
									$em_global=$i;
									$definition=TRUE;
								}
								//heap
								$text="HeapMemor";
								if(self::check_similarity($text,$info[$i]) >= $valid_percentage){
									$heap=$i;
									$definition=TRUE;
								}
								//priv_mode
								$text="Pri.";
								if(self::check_similarity($text,$info[$i]) >= $valid_percentage){
									$priv_mode=$i;
									$definition=TRUE;
								}
								//page_mem
								$text="PagingMem";
								if(self::check_similarity($text,$info[$i]) >= $valid_percentage){
									$page_mem=$i;
									$definition=TRUE;
								}
								//roll_mem
								$text="RollMem";
								if(self::check_similarity($text,$info[$i]) >= $valid_percentage){
									$roll_mem=$i;
									$definition=TRUE;
								}
								//queue_dia
								$text="Dia.";
								if(self::check_similarity($text,$info[$i]) >= $valid_percentage){
									$queue_dia=$i;
									$definition=TRUE;
								}
								//queue_upd
								$text="Upd.";
								if(self::check_similarity($text,$info[$i]) >= $valid_percentage){
									$queue_upd=$i;
									$definition=TRUE;
								}
								//queue_enq
								$text="Enq.";
								if(self::check_similarity($text,$info[$i]) >= $valid_percentage){
									$queue_enq=$i;
									$definition=TRUE;
								}
								//logins
								$text="Logins";
								if(self::check_similarity($text,$info[$i]) >= $valid_percentage){
									$logins=$i;
									$definition=TRUE;
								}
								//sessions
								$text="Sessions";
								if(self::check_similarity($text,$info[$i]) >= $valid_percentage){
									$sessions=$i;
									$definition=TRUE;
								}
					}
						}else{
							//aun no hay definición
						}
					}else{
						//menos de 10 lineas
					}
				}else{
					unset($postvs);	
					$postvs=array(
						'id'				=>'',
						'system_id'			=>	$system_id,
						'date'				=>	$date,
						'time'				=>	( $time == NULL )			? NULL : preg_replace( '/[^0-9:]/', '', $info[$time] ),
						'servername'		=>	( $servername == NULL )		? NULL : $info[$servername],
						'act_wps'			=>	( $act_wps == NULL )		? NULL : $info[$act_wps],
						'dia_wps'			=>	( $dia_wps == NULL )		? NULL : $info[$dia_wps],
						'rfc_wps'			=>	( $rfc_wps == NULL )		? NULL : $info[$rfc_wps],
						'cpu_usr'			=>	( $cpu_usr == NULL )		? NULL : $info[$cpu_usr],
						'cpu_sys'			=>	( $cpu_sys == NULL )		? NULL : $info[$cpu_sys],
						'cpu_idle'			=>	( $cpu_idle == NULL )		? NULL : $info[$cpu_idle],
						'cpu_ava'			=>	( $cpu_ava == NULL )		? NULL : $info[$cpu_ava],
						'page_in'			=>	( $page_in == NULL )		? NULL : $info[$page_in],
						'page_out'			=>	( $page_out == NULL )		? NULL : $info[$page_out],
						'free_mem'			=>	( $free_mem == NULL )		? NULL : $info[$free_mem],
						'em_alloc'			=>	( $em_alloc == NULL )		? NULL : $info[$em_alloc],
						'em_attach'			=>	( $em_attach == NULL )		? NULL : $info[$em_attach],
						'em_global'			=>	( $em_global == NULL )		? NULL : $info[$em_global],
						'heap'				=>	( $heap == NULL )			? NULL : $info[$heap],
						'priv_mode'			=>	( $priv_mode == NULL )		? NULL : $info[$priv_mode],
						'page_mem'			=>	( $page_mem == NULL )		? NULL : $info[$page_mem],
						'roll_mem'			=>	( $roll_mem == NULL )		? NULL : $info[$roll_mem],
						'queue_dia'			=>	( $queue_dia == NULL )		? NULL : $info[$queue_dia],
						'queue_upd'			=>	( $queue_upd == NULL )		? NULL : $info[$queue_upd],
						'queue_enq'			=>	( $queue_enq == NULL )		? NULL : $info[$queue_enq],
						'logins'			=>	( $logins == NULL )			? NULL : $info[$logins],
						'sessions'			=>	( $sessions == NULL )		? NULL : $info[$sessions],
					);
					if($postvs['time'] != NULL && $postvs['servername'] != NULL ){
						$postvs['filename']=$filename;
						$response['message']=$this->update_class_row('add',$postvs);
						$c++;
					}else{
						
					}
				}
			}else{
				//omitir linea
			}
		}//end While
		$response['status'] = 'ok';
		$response['filename'] = $filename;
		$response['date'] = $date;
		$response['counter'] = $c;
	}else{
		$response['error'] = 'No hay análisis';
	}
	echo json_encode($response);
	die();	
}
protected function check_similarity($first,$second){
	switch(gettype($first)){
		case 'array':
			$max_per_round=0;
			foreach($first as $first_string){
				similar_text($first_string, $second,$simil);
				if($simil >= $max_per_round){
					$max_per_round=$simil;
				}
			return $max_per_round;
			}
			break;
		default:
			similar_text($first, $second,$simil);
//			self::write_log($first.' - '.$second.' = '.$simil);
	}
	return $simil;
}
//END OF CLASS	
}

global $AA_SDFMON;
$AA_SDFMON =new AA_SDFMON_CLASS();

?>