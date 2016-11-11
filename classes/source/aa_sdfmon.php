<?php
defined('ABSPATH') or die("No script kiddies please!");

class SDFMON_CLASS extends AA_CLASS{

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
								act_wps smallint(5) unsigned not null,
								dia_wps smallint(5) unsigned not null,
								rfc_wps smallint(5) unsigned not null,
								cpu_usr smallint(5) unsigned not null,
								cpu_sys smallint(5) unsigned not null,
								cpu_idle smallint(5) unsigned not null,
								cpu_ava smallint(5) unsigned null,
								page_in smallint(5) unsigned not null,
								page_out smallint(5) unsigned not null,
								free_mem int(10) unsigned not null,
								em_alloc int(10) unsigned not null,
								em_attach int(10) unsigned not null,
								em_global int(10) unsigned not null,
								heap int(10) unsigned not null,
								priv_mode smallint(5) unsigned not null,
								page_mem int(10) unsigned not null,
								roll_mem int(10) unsigned null,
								queue_dia smallint(5) unsigned not null,
								queue_upd smallint(5) unsigned not null,
								queue_enq smallint(5) unsigned not null,
								logins smallint(5) unsigned not null,
								sessions smallint(5) unsigned not null,
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
    global $SYSTEM;
    $response = $SYSTEM->get_single($id);
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
	$response=array();
	$c=0;

	$date=$_POST['year']."/".$_POST['month']."/".$_POST['day'];
	$system_id=intval($_POST['system_id']);
	if($system_id != 0){
		$filename=$_FILES['file']['name'];
		$fp = fopen($_FILES['file']['tmp_name'], 'rb');
		while(  ($line=fgets($fp))  !==  false){
			if($line[0] == "|"){
				$line=str_replace(' ','',$line);
				$info=explode("|",$line);
				if( count($info) > 20){
					unset($postvs);	
					$postvs=array(
						'id'				=>'',
						'system_id'			=>	$system_id,
						'date'				=>	$date,
						'time'				=>	$info[1],
						'servername'		=>	$info[2],
						'act_wps'			=>	$info[3],
						'dia_wps'			=>	$info[4],
						'rfc_wps'			=>	$info[5],
						'cpu_usr'			=>	$info[6],
						'cpu_sys'			=>	$info[7],
						'cpu_idle'			=>	$info[8],
						'cpu_ava'			=>	$info[9],
						'page_in'			=>	$info[10],
						'page_out'			=>	$info[11],
						'free_mem'			=>	$info[12],
						'em_alloc'			=>	$info[13],
						'em_attach'			=>	$info[14],
						'em_global'			=>	$info[15],
						'heap'				=>	$info[16],
						'priv_mode'			=>	$info[17],
						'page_mem'			=>	$info[18],
						'roll_mem'			=>	$info[19],
						'queue_dia'			=>	$info[20],
						'queue_upd'			=>	$info[21],
						'queue_enq'			=>	$info[22],
						'logins'			=>	$info[23],
						'sessions'			=>	$info[24],
					);
					if(is_numeric($postvs['sessions'])){
						$postvs['filename']=$filename;
						$response['message2']=$postvs;
						$response['message']=$this->update_class_row('add',$postvs);
						$c++;
					}
				}
			}
		}
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
//END OF CLASS	
}

global $SDFMON;
$SDFMON =new SDFMON_CLASS();



//add_action( 'admin_notices', array( $SDFMON, 'db_install_error')  );
?>