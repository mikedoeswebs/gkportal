<?php
/*
Plugin Name: Fellowship Bottom Admin Bar
Plugin URI: http://fellowship.agency/
Description: Reposition the admin bar to the bottom of each page.
Version: 1.0.0
Author: Ian Tearle
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class fellowhsipMoveAdminBar {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'move_admin_bar' ) );
	}

	function move_admin_bar() {

		if(!is_admin_bar_showing()) {
			return;
		}

	    wp_enqueue_style( 'pbc-move-admin-bar' , plugin_dir_url( __FILE__ ) . 'css/fellowship-bottom-admin-bar.css' , array() , 1.00 , false );

	}
}

new fellowhsipMoveAdminBar();
