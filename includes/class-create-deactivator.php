<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://19h47.fr
 * @since      1.0.0
 *
 * @package    Create
 * @subpackage Create/includes
 */


/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Create
 * @subpackage Create/includes
 * @author     Jérémy Levron <jeremylevron@19h47.fr>
 */
class Status_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		update_option( 'plugin_status', 'inactive' );
	}
}
