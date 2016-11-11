<?php
defined('ABSPATH') or die("No script kiddies please!");

//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**
* Creates the Security filter
*
* @author Cristian Marin
*/
function aa_security($capability="read_posts"){
	if ( !is_super_admin() ) :
		if(current_user_can($capability) == false):?>
			<div class="error settings-error" id="setting-error-settings_updated">
				<p><strong>No tienes autorización para ver esta página.</strong></p>
			</div>
			<?php
			wp_die();
		endif;
	endif;
	return TRUE;
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**
* Creates the Good MSG
*
* @author Cristian Marin
*/
function aa_good_msg($text){
	echo '<div id="message" class="updated">';
	echo '	<p><strong>'.$text.'</strong></p>';
	echo '</div>';
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**
* Creates the Bad MSG
*
* @author Cristian Marin
*/
function aa_bad_msg($text){
	echo '<div class="error settings-error" id="setting-error-settings_updated">';
	echo '	<p><strong>'.$text.'</strong></p>';
	echo '</div>';

}

//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**
* Creates the main content of the administation page
*
* @author Cristian Marin
*/
function aa_class_menu_main(
								$title="Listado",
								$menu_slug=null,
								$add_new="Agregar nuevo",
								$plugin_post=null,
								$titles=null,
								$contents=null,
								$filters=null){
	global $wpdb;
	global $aa_vars;
	global $ITA_CUSTOMER;
	?>	
	<h2>
		<?php echo $title;?>
		<a class="add-new-h2" href="?page=<?php echo $menu_slug;?>&action=add">
			<?php echo $add_new;?>
		</a>
	</h2>
	<form
		action="?page=<?php echo $menu_slug;?>"
		method="post"
		id="deleteForm"
		onsubmit="return confirm('\t¡Espera!\n¡Si eliminas estos items no los podrás recuperar!\n¿Deseas continuar?');"></form>
	<input form="deleteForm" type='hidden' name='<?php echo $plugin_post;?>[action]' value='bulkdelete' />
	<input form="deleteForm" type='hidden' name='<?php echo $plugin_post;?>[item]' value='' />
		<?php
			echo str_replace(
				'<input ',
				'<input form="deleteForm" ',
				wp_nonce_field( "bulkdelete", $plugin_post."[actioncode]",false,false)
			);
		?>
	<table class="wp-list-table widefat fixed posts">
		<thead>
			<tr>
				<th class="manage-column column-cb check-column" scope="col">
					<input form="deleteForm" type="checkbox">
				</th>
				<?php
				if($filters!=null){
					?>
					<form
						action="?page=<?php echo $menu_slug;?>"
						method="get"
						id="filterForm"></form>
					<input form="filterForm" type='hidden' name='page' value='<?php echo $menu_slug;?>' />
					<?php
					foreach ($filters as $key => $filter):
						echo "<th>";
						switch($filter[2]):
							case 'text':
								echo $filter[1];
								break;
							case 'dropdown':
								echo '<select
										form="filterForm"
										name="'.$filter[0].'"
										id="'.$filter[0].'"
										class="formfilter"
										';
								echo '>';
								echo '<option value="0">Filtra por '.$filter[1].'</option>';
										foreach($filter[3] as $option):
											echo '<option value="'.$option[0].'" ';
											if(isset($_GET[$filter[0]]))	echo ($option[0]==$_GET[$filter[0]])?'selected':'';
											echo '>'.$option[1].'</option>';
										endforeach;
								echo '</select>';
								echo '<label for="'.$filter[0].'"><i class="fa fa-sort"></i></label>';
								break;
							default:
								echo "ERROR";
						endswitch;
					endforeach;
					wp_register_script('aa_formfilter',plugins_url( 'js/formfilter.js' , __FILE__),array( 'jquery' ));
					wp_enqueue_script( 'aa_formfilter');
				}else{
					foreach ($titles as $key => $title):
						echo ($key==0)?'':'<th>'.$title.'</th>';
					endforeach;
				}
				?>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th class="manage-column column-cb check-column" scope="col">
					<input form="deleteForm" type="checkbox">
				</th>
				<?php
					foreach ($titles as $key => $title):
						echo ($key==0)?'':'<th>'.$title.'</th>';
					endforeach;
				?>
			</tr>
		</tfoot>
		<tbody>
		<?php 
		if(sizeof($contents) == 0):?>
			<tr class="no-items">
				<td colspan="<?php echo count($titles)-1;?>"><?php _e('No se ha encontrado información','aa');?></td>
			</tr>
		<?php 
		else:
			foreach($contents as $index => $content): ?>
				<tr <?php if($index%2)	{echo "class='alternate'";}?>>
					<?php
					foreach ($titles as $key => $title):
						if($key==0):
							echo '<th class="check-column" scope="row">';
							echo '<input form="deleteForm" type="checkbox" name="'.$plugin_post.'[delete]['.$content[$key].']" />';
							echo '</th>';
						elseif($key==1):
							$id=$content[0];
							echo "<td>".$content[$key];
								echo '<div class="row-actions">';
									echo '<span class="edit">';
										echo '<a href="?page='.$menu_slug.'&action=edit&item='.$id.'&actioncode='.wp_create_nonce($id."edit").'">';
											echo 'Edaar';
										echo '</a>';
									echo '</span>';
								echo '</div>';
							echo '</td>';
						else:
							echo '<td>'.$content[$key].'</td>';
						endif;
					endforeach;
					?>
				</tr>
			<?php
			endforeach;
		endif;
		?>
		</tbody>
	</table>
	<p><input form="deleteForm" type="submit" class="button-secondary" value="Eliminar items seleccionados" /></p>
<?php
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**
* Form to add/edit a single Class registry into the system
*
* @since 1.0
* @author Cristian Marin
*/
function aa_class_form(
								$type=null,				//add,update
								$title="Titulo",
								$menu_slug=null,
								$plugin_post=null,
								$fields=null,
								$id=null){
		global $wpdb;
		switch($type){
			case "add":		$submit="Crear item";	break;
			case "update":	$submit="Edaar item";	break;
			default:
				aa_bad_msg("Error interno al identificar el Tipo de Formulario");
				wp_die();
		}
		echo '<h3>'.$title.'</h3>';
		echo '<div id="error-message-wrapper" class="error settings-error" style="padding:0;"></div>';
		echo '<form action="?page='.$menu_slug.'" method="post" id="'.$menu_slug.'">';
			echo '<input type="hidden" name="'.$plugin_post.'[action]" value="'.$type.'" />';
			if($id!=null):
				echo '<input type="hidden" name="'.$plugin_post.'[id]" value="'.$id.'" />';
			endif;
			foreach ($fields as $field) {
				if($field['field_type']=="hidden"):
					echo "<input type='hidden' name='".$plugin_post."[".$field['field_name']."]' value='".$field['field_value']."' />";
				endif;
			}
			wp_nonce_field( $type, $plugin_post."[actioncode]");
			echo '<table class="form-table"><tbody>';
			foreach($fields as $key => $field):
				$required	=($field['required']==TRUE)?'required':'';
				if(isset($field['size'])):
					switch($field['size']):
						case "XS":	$size="15%";	break;
						case "S":	$size="30%";	break;
						case "M":	$size="50%";	break;
						case "L":	$size="75%";	break;
						default:	$size="100%";
					endswitch;
				endif;
				if($field['field_type']!="hidden"):
					echo '<tr>';
					echo '<th><label for="'.$plugin_post.'['.$field['field_name'].']">'.$field['field_desc'].'</label></th>';
					switch($field['field_type']):
						case 'number':
						case 'nat_number':
							echo '<td>
									<input
										type="text"
										data-validation="'. $required .'"
										maxlength="'.$field['maxchar'].'"
										name="'.$plugin_post.'['.$field['field_name'].']"
										id="'.$plugin_post.'['.$field['field_name'].']"
										class="widefat"
										style="width: '.$size.'"
										value="'.$field['field_value'].'"
										placeholder="'.strip_tags($field['field_desc']).'" />';
							echo '</td>';
							break;
						case 'text':
							echo '<td>
									<input
										type="text"
										data-validation="'. $required .'"
										maxlength="'.$field['maxchar'].'"
										name="'.$plugin_post.'['.$field['field_name'].']"
										id="'.$plugin_post.'['.$field['field_name'].']"
										class="widefat"
										style="width: '.$size.'"
										value="'.$field['field_value'].'"
										placeholder="'.strip_tags($field['field_desc']).'" />';
							echo '</td>';
							break;
						case 'textarea':
							echo '<td>
									<textarea
										data-validation="'. $required .'"
										name="'.$plugin_post.'['.$field['field_name'].']"
										id="'.$plugin_post.'['.$field['field_name'].']"
										class="widefat"
										placeholder="'.strip_tags($field['field_desc']).'">'.$field['field_value'].'</textarea>
								</td>';
							break;
						case 'dropdown':
							echo '<td>
									<select
										name="'.$plugin_post.'['.$field['field_name'].']"
										id="'.$plugin_post.'['.$field['field_name'].']"';
										if($required):
											echo 'data-validation="number"';
											echo 'data-validation-allowing="range[1;1000]"';
										endif;
										echo '>';
								echo '<option value="0">Selecciona '.$field['field_desc'].'</option>';
								if($field['field_value']!=0)
									foreach($field['field_value'] as $option):
										echo '<option value="'.$option[0].'" ';
										if(isset($field['selected']))
											echo ($option[0]==$field['selected'])?'selected':'';
										echo '>'.$option[1].'</option>';
									endforeach;	
							echo '</select></td>';
							break;
						case 'bool':
							echo '<td>';
								$checked = $field['field_value']==0?'':'checked';
								echo '<input type="checkbox" 
										name="'.$plugin_post.'['.$field['field_name'].']"
										id="'.$plugin_post.'['.$field['field_name'].']"
										value="'.$plugin_post.'['.$field['field_name'].']" 
										'.$checked.'
									 />';
							echo '</td>';
							break;
						case 'title':
							echo '<td>'.$field['field_desc'].'</td>';
							break;
						default:
							echo "<td>ERROR</td>";
					endswitch;
				echo '</tr>';
			endif;
			endforeach;
			echo '<tr>';
				echo '<td colspan="2">';
					echo '<input type="submit" class="button-primary" value="'.$submit.'" />';
					echo '<a href="?page='.$menu_slug.'" class="button-secondary">Volver</a>';
				echo '</td>';
			echo '</tr>';
		echo '</tbody></table></form>';
}

//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**
* Check the correct values of the input variables according to Class definition by $db_fields
*
* @since 1.0
* @author Cristian Marin
*/
function check_form_values( $postvs=null, $db_fields=null){
	foreach($db_fields as $key => $db_field):
		if($db_field['required']==true):
			switch($db_field['field_type']):
				case 'id':
					break;
				case 'number':
					if (!is_numeric($postvs[$key])):
						return false;
					endif;
					break;
				case 'nat_number':
					if (!is_numeric($postvs[$key]) || $postvs[$key]<1):
						return false;
					endif;
					break;
				case 'text':
					if (!is_string($postvs[$key]) || $postvs[$key]==''):
						return false;
					endif;
					break;
				default:
			endswitch;
		endif;
	endforeach;
	return true;
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**
* Add new Class registry into the database
*
* @since 1.0
* @author Cristian Marin
*/
function aa_add_new_class_row( $tbl_name=null, $postvs=null, $db_fields=null, $name="item",$silent="false"){
	global $wpdb;
	if(check_form_values($postvs, $db_fields)){
		$insertArray = array();
		foreach($db_fields as $key => $db_field):
			
			switch($db_field['field_type']){
				case 'id':
					break;
				case 'timestamp':
					$insertArray[$key]=current_time( 'mysql');
					break;
				case 'bool':
					if(isset($postvs[$key])):
						$insertArray[$key]=TRUE;
					else:
						$insertArray[$key]=FALSE;
					endif;
					break;
				default:
					$insertArray[$key] = strip_tags(stripslashes( $postvs[$key] ));
			}
		endforeach;
		if ( $wpdb->insert( $tbl_name, $insertArray ) ){
			$silent?:aa_good_msg("El nuevo ".$name." ha sido guardado.");
			return $wpdb->insert_id;
		}else{
			aa_bad_msg("Hubo un error al agregar el nuevo ".$name."; intenta nuevamente. :)");
		}
	
	}else{
		aa_bad_msg("No has ingresado los datos correctos para un nuevo ".$name."; intenta nuevamente. :)");
	}
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**
* Edit a single Class registry into the database
*
* @since 1.0
* @author Cristian Marin
*/
function aa_edit_class_row( $tbl_name=null, $postvs=null, $db_fields=null, $name="item" ){
	global $wpdb;
	
	$editArray		= array();
	$editArray_eval	= array();
	$whereArray		= array();
	if(check_form_values($postvs, $db_fields)){
		$whereArray=array("id" => intval($postvs["id"]));
		foreach($db_fields as $key => $db_field){
			switch($db_field['field_type']){
				case 'id':
					break;
				case 'bool':
					if(isset($postvs[$key])):
						$editArray[stripslashes($key)]=TRUE;
					else:
						$editArray[stripslashes($key)]=FALSE;
					endif;
					//array_push($editArray_eval,$value);
					break;
				default:
					$editArray[stripslashes($key)]=strip_tags(stripslashes($postvs[$key]));
/*					$value=null;
					switch($db_field['field_type']){
						case("nat_number"):	$value="%d";	break;
						case("number"):		$value="%d";	break;
						case("text"):		$value="%s";	break;
						case("bool"):		$value="%b";	break;
						default:			$value="%s";
					}
					array_push($editArray_eval,$value);
*/			}
		}
		$result = $wpdb->update($tbl_name,$editArray,$whereArray);
		if( $result === false ){
			aa_bad_msg("Hubo un error al edaar el ".$name."; intenta nuevamente. :)");
		}elseif ( $result == 0){
			aa_good_msg("Los valores son iguales. ".$name." no modificado.");
		}else{
			aa_good_msg($name." edaado exitosamente.");
		}
	}else{
		var_dump($postvs);
		aa_bad_msg("No has ingresado los datos correctos para edaar el ".$name."; intenta nuevamente. :)");
	}
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**
* Deletes multiple Class registries from the database
*
* @since 1.0
* @author Cristian Marin
*/
function aa_bulk_delete( $tbl_name,$postvs,$name ){
	global $wpdb;
	if( isset( $postvs["delete"] ) ):
		$count = sizeof( $postvs["delete"] );
		$items = implode( ",", array_keys( $postvs["delete"] ) );
		if( preg_match( "/^([0-9, ])*$/", $items ) ):
			$sql = "DELETE FROM ".$tbl_name." WHERE `id` IN (".esc_sql($items).")";
			$wpdb->get_results($sql);
			aa_good_msg($count.' '.$name.' han sido eliminados.');
		else:
			aa_bad_msg("No se pudo borrar los ".$name.", por favor pruebe nuevamente.");
		endif;
	else:
		aa_bad_msg("¡¿Estás mal de la cabeza?! Nada seleccionado!. Vuelve y selecciona elementos antes de hacer click nuevamente!");
	endif;
}



//---------------------------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------------------------

function aa_get_all($tables=null){
		global $wpdb;
/*	foreach($tables as $key => $table):
		$tables[$key]['tcode']='t'.str_pad($key, 3, "0", STR_PAD_LEFT); #000
	endforeach;
*/
	$SQLL=aa_get_all_select($tables,sizeof($tables)-1);
	$SQLL.=" FROM ".aa_get_all_from($tables,sizeof($tables)-1);
	if(($SQL_WHERE=aa_get_all_where($tables,sizeof($tables)-1))!=null){
		$SQLL.=" WHERE ".implode(" AND ",$SQL_WHERE);
	}
	$SQLL.=" ORDER BY ".aa_get_all_order($tables,sizeof($tables)-1);
	if(($SQL_GROUP=aa_get_all_group($tables,sizeof($tables)-1))!=null){
		echo " GROUP BY ".implode(" AND ",$SQL_GROUP);
	}
	
//	echo $SQLL;
	return $wpdb->get_results( $SQLL ,ARRAY_A );
}
/**
* Add select clauses
*
* @since 1.0
* @author Cristian Marin
*/
function aa_get_all_select($tables=null,$num=0){
	$sel_str=array();
	foreach($tables[$num]['fields'] as $key => $field){
		$sel_str[$key] =$tables[$num]['tcode'].".".$field[0]." as ".$field[1];
	}
	$output=implode(',',$sel_str);
	if($num<=0){
		return "SELECT " .$output;
	}else{
		return aa_get_all_select($tables,$num-1).",".$output;
	}
}
/**
* Add from clauses
*
* @since 1.0
* @author Cristian Marin
*/
function aa_get_all_from($tables=null,$num=0){
	$sel_str=array();
	foreach($tables[$num]['fields'] as $key => $field){
		$sel_str[$key] =$tables[$num]['tcode'].".".$field[0]." as ".($field[1]!=null)?$field[1]:$field[0];
	}
	$output=implode(',',$sel_str);
	if($num<=0){
		return $tables[$num]['tname']." ".$tables[$num]['tcode'];
	}else{
		$output ="(";
			$output.=aa_get_all_from($tables,$num-1);
				$output.=" LEFT JOIN ".$tables[$num]['tname']." ".$tables[$num]['tcode'];
					$output.=" ON ";
						$output.=$tables[$num]['tjoin']['tcode'].".".$tables[$num]['tjoin']['tfield'];
						$output.="=";
						$output.=$tables[$num]['tcode'].".".$tables[$num]['tjoin']['ofield'];
						
		$output.=")";
		return $output;
	}
}
/**
* Add where clauses
*
* @since 1.0
* @author Cristian Marin
*/
function aa_get_all_where($tables=null,$num=0){
	$whe_str=array();
	foreach($tables[$num]['fields'] as $key => $field){	
		if($field[3]!=null && $field[3]!='' && $field[3]!=0){
			switch($field[2]){
				case 'like':
					array_push($whe_str,$tables[$num]['tcode'].".".$field[0]." LIKE '".$field[3]."'");
					break;
				case '=':
					array_push($whe_str,$tables[$num]['tcode'].".".$field[0]." = '".$field[3]."'");
					break;
			}
		}
	}
	if($num<=0){
		return $whe_str;
	}else{
		if(is_array($next=aa_get_all_where($tables,$num-1))):
			foreach($next as $entradas):
				array_push($whe_str,$entradas);
			endforeach;
		endif;
		return $whe_str;		
	}
}

/**
* Add order by clauses
*
* @since 1.0
* @author Cristian Marin
*/
function aa_get_all_order($tables=null,$num=0){
	$sel_str=array();
	foreach($tables[$num]['fields'] as $key => $field){
		$sel_str[$key] =($field[1]!=null)?$field[1]:$field[0];
	}
	$output=implode(',',$sel_str);
	if($num<=0){
		return $output;
	}else{
		return $output.",".aa_get_all_order($tables,$num-1);
	}
}
/**
* Add group by clauses
*
* @since 1.0
* @author Cristian Marin
*/
function aa_get_all_group($tables=null,$num=0){
	$grp_str=array();
	foreach($tables[$num]['fields'] as $key => $field){	
		if($field[4]!=null && $field[4]!='' && $field[4]!=0){
			array_push($whe_grp,$tables[$num]['tcode'].".".$field[0]);
		}
	}
	if($num<=0){
		return $whe_grp;
	}else{
		if(is_array($next=aa_get_all_order($tables,$num-1))):
			foreach($next as $entradas):
				array_push($whe_grp,$entradas);
			endforeach;
		endif;
		return $whe_grp;
	}
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**
* Add order by clauses
*
* @since 1.0
* @author Cristian Marin
*/
function aa_post_to_get($variable=null){
	if(isset($_POST[$plugin_post][$variable])){
		$_GET[$variable]=$_POST[$plugin_post][$variable];
	}
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
/*
* Change CSS for LESS into wp_enqueue_style
* http://stackoverflow.com/questions/8082236/wp-enqueue-style-and-rel-other-than-stylesheet
*/
function aa_plugin_style_loader_tag_function($tag){
  //do stuff here to find and replace the rel attribute    
  return preg_replace("/='stylesheet' id='less-css'/", "='stylesheet/less' id='less-css'", $tag);
}
add_filter('style_loader_tag', 'aa_plugin_style_loader_tag_function');
//---------------------------------------------------------------------------------------------------------------------------------------------------------
/*
* Log own debug statements
* http://www.smashingmagazine.com/2011/03/08/ten-things-every-wordpress-plugin-developer-should-know/
*/

function log_me($message) {
    if (WP_DEBUG === true) {
        if (is_array($message) || is_object($message)) {
            error_log(print_r($message, true));
        } else {
            error_log($message);
        }
    }
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------


?>