<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.19h47.fr
 * @since      1.0.0
 *
 * @package    wpcp
 * @subpackage wpcp/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    wpcp
 * @subpackage wpcp/admin
 * @author     Levron Jérémy <levronjeremy@19h47.fr>
 */
class WPCP_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    	$plugin_name    	The ID of this plugin.
	 */
	private $plugin_name;


	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    	$version    		The current version of this plugin.
	 */
	private $version;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}


	/**
	 * Don't activate this plugin if Contact Form 7 plugin is deactivated
	 *
	 * Hooks on action 'admin_notices'
	 *
	 * @since 1.1.0
	 */
	public function check_plugin_dependency() {

		if( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice__success' ) );

			return true;
		}

		return add_action( 'admin_notices', array( $this, 'admin_notice__error' ) );
	}


	/**
	 * admin notice success
	 */
	public function admin_notice__success() {

		$class = 'notice notice-success is-dismissible';
		$message = __( '<strong>WordPress Create Post</strong> has been installed with success' );

		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
	}


	/**
	 * admin notice error
	 */
	public function admin_notice__error() {

		$class = 'notice notice-error';
		$message = __( '<strong>WordPress Create Post</strong> requires <strong>Contact Form 7</strong> plugin.');

		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
	}
}
