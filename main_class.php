<?php
defined('ABSPATH') or die("No script kiddies please!");

abstract class ABAP_CLASS{
//---------------------------------------------------------------------------------------------------------------------------------------------------------
	/**
	* Create the Class table if required
	*
	* @since 1.0
	* @author Cristian Marin
	*/
	function db_install(){
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $this->crt_tbl_sql );
		//wp_die();									//uncomment only for error & debug purposes
		update_option( $this->tbl_name."_db_version" , $this->db_version );
		
	}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
	/**
	* Registers the Class menu in the WordPress system
	*
	* @since 1.0
	* @author Cristian Marin
	*/
	function abap_register_class_submenu_page() {
		global $abap_vars;
		$parent_slug	=$this->parent_slug;
		$page_title		='Administraci&oacute;n de '.$this->name_plural;
		$menu_title		=$this->icon.' '.$this->name_plural;
		$capability		=$this->capability;
		$menu_slug		=$this->menu_slug;
		$function		=array( $this , "class_menu" );

		add_submenu_page(
			$parent_slug,
			$page_title,
			$menu_title,
			$capability,
			$menu_slug,
			$function);
	}

//---------------------------------------------------------------------------------------------------------------------------------------------------------
	/**
	* Output the basic Class administation menu
	*
	* @since 1.0
	* @author Cristian Marin
	*/
	function class_menu(){
		echo '<div class="wrap">';
		echo '<h2>';
			echo $this->icon;
			echo ' Adminsitraci&oacute;n de '.$this->name_plural;
		echo '</h2>';
		$action = ( isset( $_GET["action"] ) ) ? $_GET["action"] : "";
		$item = ( isset( $_GET["item"] ) ) ? $_GET["item"] : "";
		$actioncode = ( isset( $_GET["actioncode"] ) ) ? $_GET["actioncode"] : "";

		
		self::check_post_vars();
		if( $action == "add" ){
			$this->new_form();
		}
		elseif( $action == "edit" && wp_create_nonce( $item.$action ) == $actioncode ){
			$this->edit_form( $item );
		}
		else{
			$this->class_menu_main();
		}
		echo "</div>";
	}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
	/**
	* Check the content of the GET and POST variables and subsequently choses an action to perform
	*
	* @since 1.0
	* @author Cristian Marin
	*/
	function check_post_vars(){
		global $abap_vars;

		if( isset( $_REQUEST[$this->plugin_post] ) ){
			$post = $_REQUEST[$this->plugin_post];
			if( isset( $post["action"] ) && isset( $post["actioncode"] ) ){
				if( wp_verify_nonce( $post["actioncode"], $post["action"] ) ){
					switch ( $post["action"] ):
						case "add":
							self::add_new( $post );
							break;
						case "bulkdelete":
							self::bulk_delete( $post );
							break;
						case "update":
							self::update( $post );
							break;
					endswitch;
				}
			}
		}
	}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
	/**
	* Add new Class registry into the database
	*
	* @since 1.0
	* @author Cristian Marin
	*/
	function add_new( $postvs ){
		return abap_add_new_class_row($this->tbl_name, $postvs, $this->db_fields, $this->name_single );
	}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
	/**
	* Update a single Class Registry in the database
	*
	* @since 1.0
	* @author Cristian Marin
	*/
	function update( $postvs ){
		abap_edit_class_row($this->tbl_name, $postvs, $this->db_fields, $this->name_single );
	}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
	/**
	* Deletes multiple Class registries from the database
	*
	* @since 1.0
	* @author Cristian Marin
	*/
	function bulk_delete( $postvs ){
		abap_bulk_delete( $this->tbl_name,$postvs,$this->name_plural);
	}


//---------------------------------------------------------------------------------------------------------------------------------------------------------
	/**
	* Read a single Class registry from the database
	*
	* @since 1.0
	* @author Cristian Marin
	*/
	function get_all(){
		global $wpdb;
		global $abap_vars;
		$output = $wpdb->get_results( "SELECT * FROM ".$this->tbl_name,ARRAY_A);
		if($abap_vars['DEBUG']):
			$wpdb->show_errors();
		endif;
		return $output;
	}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
	/**
	* Read a single Class registry from the database
	*
	* @since 1.0
	* @author Cristian Marin
	*/
	function get_single( $id = 0 ){
		global $wpdb;
		global $abap_vars;
		$output = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$this->tbl_name." WHERE `id` = %d", $id ),ARRAY_A );
		if($abap_vars['DEBUG']):
			$wpdb->show_errors();
		endif;
		return $output;
	}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
	/**
	* Read all Class registry from the database
	*
	* @since 1.0
	* @author Cristian Marin
	*/
	function get_sql($sql){
		global $wpdb;
		global $abap_vars;
		//Protects from SQL injection and names with apostrophes
		//$sql = $mysqli->real_escape_string($sql);
		$output=$wpdb->get_results( $sql, ARRAY_A );
		if($abap_vars['DEBUG']):
			$wpdb->show_errors();
		endif;
		return $output;
	}
//---------------------------------------------------------------------------------------------------------------------------------------------------------
	
	//END OF CLASS	
}
?>