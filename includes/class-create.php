<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.19h47.fr
 * @since      1.0.0
 *
 * @package    wpcp
 * @subpackage wpcp/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    wpcp
 * @subpackage wpcm/includes
 * @author     Jérémy Levron levronjeremy@19h47.fr
 */

class WPCP {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      WPCP_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

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
     * @param    string    $plugin_name       The name of this plugin.
     * @param    string    $version    The version of this plugin.
     */
    public function __construct() {

        $this->plugin_name = 'wpcp';
        $this->version = '1.0.0';

        $this->load_dependencies();

        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - WPCP_Loader. Orchestrates the hooks of the plugin.
     * - WPCP_Admin. Defines all hooks for the admin area.
     * - WPCP_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of
         * the core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-create-loader.php';


        /**
         * The class responsible for defining all actions that occur in the
         * admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wpcp-admin.php';


        /**
         * The class responsible for defining all actions that occur in the
         * public-facing side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wpcp-public.php';


        $this->loader = new WPCP_Loader();
    }


    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        $plugin_admin = new WPCP_Admin( $this->get_plugin_name(), $this->get_version() );
    }


    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
        $plugin_public = new WPCP_Public(
            $this->get_plugin_name(),
            $this->get_version()
        );

        $this->loader->add_filter( 'wpcf7_validate_file', $plugin_public, 'filter_wpcf7_validate_file', 10, 2 );
        $this->loader->add_filter( 'wpcf7_validate_file*', $plugin_public, 'filter_wpcf7_validate_file', 10, 2 );
        $this->loader->add_filter( 'wpcf7_messages', $plugin_public, 'filter_wpcf7_custom_validation_messages' );
        $this->loader->add_action( 'wpcf7_before_send_mail', $plugin_public, 'create_post' );
    }


    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }


    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }


    /**
     * Retrieve the default post types
     *
     * @return    default_post_types
     * @access    public
     */
    public function get_default_post_types() {
        return $this->default_post_types;
    }


    /**
     * Return loader
     *
     * @since     1.0.0
     * @return    loader
     * @access    public
     */
    public function get_loader() {
        return $this->loader;
    }


    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }
}