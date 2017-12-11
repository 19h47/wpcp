<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.19h47.fr
 * @since      1.0.0
 *
 * @package    wpcp
 * @subpackage wpcp/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    wpcp
 * @subpackage wpcp/public
 * @author     Levron Jérémy <levronjeremy@19h47.fr>
 */
class WPCP_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;


	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}


	/**class="Jazz">🎷
	 * create post
	 *
	 */
	public function create_post( $contact_form ) {
		$submission = WPCF7_Submission::get_instance();

		if ( ! $submission ) {
			return false;
		}

        // Get data from $submission object
        $posted_data = $submission->get_posted_data();

        // Create post object
        $your_post = array(
        	// Event title
        	'post_title'    => $posted_data[event_title],

    	  	'post_content'  => $posted_data[your_content],
    	  	'post_status'   => 'draft',
    	  	'meta_input'	=> array(
    	  		'status'			=> $posted_data[your_status],
    	  		'name'				=> $posted_data[your_name],
    	  		'first_name'		=> $posted_data[your_first_name],

    	  		// Event
    	  		'event_children'	=> $posted_data[event_children],
    	  		'event_price' 		=> $posted_data[event_price],
    	  	),
        );

        return wp_insert_post( $your_post );
	}
}
