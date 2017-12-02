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
	 * A factory object or handle the create of mapping posts.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      Cf7_2_Post_system    $post_mapping_factory   mapping factory object.
	 */
	private $post_mapping_factory;


	/**
	 * A CF7 list table object.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      Cf7_WP_Post_Table    $cf7_list_table   cf7 admin list table object.
	 */
	private $cf7_list_table;


	/**
	* The screen ID of our custom admin page.
	* @since 3.0.0
	* @access public
	* constant string   the id of the screen, which is dependent on the CF7 main menu.
	*/
	const MAP_SCREEN_ID = 'contact_page_map_cf7_2_post';


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
		// $this->cf7_list_table = Cf7_WP_Post_Table::set_table();
		// $this->load_dependencies();
	}


	/**
	* Deactivate this plugin if CF7 plugin is deactivated
	* Hooks on action 'admin_init'
	* @since 1.1.0
	*/
	public function check_plugin_dependency() {

		//if either the polylang for the cf7 plugin is not active anymore, deactive this extension
		if( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice__success' ) );

			return;
		}

		add_action( 'admin_notices', array( $this, 'admin_notice__error' ) );
	}

	function admin_notice__success() {

		$class = 'notice notice-success is-dismissible';
		$message = __( '<strong>WordPress Create Post</strong> has been installed with success' );

		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
	}

	function admin_notice__error() {

		$class = 'notice notice-error';
		$message = __( '<strong>WordPress Create Post</strong> requires <strong>Contact Form 7</strong> plugin.');

		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
	}



	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Cf7_2_Post_Factory. manages the cf7 mapping to custom post.
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		// for the Cf7_2_Post_Factory class
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cf7-2-post-factory.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cf7-2-post-system-factory.php';

		//contact post table list
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'assets/cf7-admin-table/admin/cf7-post-admin-table.php';
	}


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles($hook) {
		if(self::MAP_SCREEN_ID == $hook){
			wp_enqueue_style('jquery-nice-select-css', plugin_dir_url( __DIR__ ) . 'assets/jquery-nice-select/css/nice-select.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cf7-2-post-mapping.css', array('dashicons'), $this->version, 'all' );
		}else{
			$screen = get_current_screen();
			if(WPCF7_ContactForm::post_type != $screen->post_type){
				return;
			}
			switch($screen->base){
				case 'edit':
					wp_enqueue_style( 'cf7-2-post-quick-edit-css', plugin_dir_url( __FILE__ ) . 'css/cf7-table.css', array(), $this->version, 'all' );
					break;
			}
		}
	}


	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts($hook) {
		if(self::MAP_SCREEN_ID == $hook){
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cf7-2-post-admin.js', array( 'jquery', 'postbox'), $this->version, true );
			wp_enqueue_script('jquery-clibboard', plugin_dir_url( __DIR__ ) . 'assets/clipboard/clipboard.min.js', array('jquery'),$this->version,true);
			wp_localize_script( $this->plugin_name, 'cf7_2_post_ajaxData', array('url' => admin_url( 'admin-ajax.php' )));
			wp_enqueue_script('jquery-nice-select', plugin_dir_url( __DIR__ ) . 'assets/jquery-nice-select/js/jquery.nice-select.min.js', array( 'jquery' ), $this->version, true );
		}else{
			$screen = get_current_screen();
			if(WPCF7_ContactForm::post_type != $screen->post_type){
				return;
			}
			switch($screen->base){
				case 'edit':
					wp_enqueue_script( 'cf72post-quick-edit-js', plugin_dir_url( __FILE__ ) . 'js/cf7-2-post-quick-edit.js', array( 'jquery' ), $this->version, true );
					break;
			}
		}
	}


	/**
	* Modify the regsitered cf7 post type
	* THis function enables public capability and amind UI visibility for the cf7 post type. Hooked late on `init`
	* @since 1.0.0
	*
	*/
	public function modify_cf7_post_type(){
		if(class_exists('WPCF7_ContactForm') &&  post_type_exists( WPCF7_ContactForm::post_type ) ) {
				global $wp_post_types;
				$wp_post_types[WPCF7_ContactForm::post_type]->supports[] = 'custom-fields';
		}
	}


	/**
	* Adds a new sub-menu
	* Add a new sub-menu to the Contact main menu to edit mapping post
	*
	*/
	public function add_cf7_sub_menu(){
		/**
		*change capability for plugin, defaut to admin users.
		*@since 3.0.0
		*/
		$capability = apply_filters('cf7_2_post_mapping_capability', 'manage_options');
		if('manage_options'!== $capability){ //validate capability.
				$roles = wp_roles();
				$is_valid=false;
				foreach($roles as $role){
						if(in_array($capability, $role['capabilities'])){
								$is_valid=true;
								break;
						}
				}
				if(!$is_valid) $cabability = 'manage_options';
		}
		// add_submenu_page( string $parent_slug, string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '' )
		$hook2 = add_submenu_page(
			'wpcf7', //parent slug
			'CF7 Form to post', //page title
			'Map CF7 to Post', //menu title
			$capability, //capability
			'map_cf7_2_post', //menu_slug -> scteen_id , change this and chage self::MAP_SCREEN_ID
			array($this,'display_mapping_page')); //fn
	}


	/**
	* Function to load custom admin page metabox.
	* Hooked on 'load-{self::MAP_SCREEN_ID}'
	*@since 3.0.0
	*/
	public function load_admin_page(){
		/* Trigger the add_meta_boxes hooks to allow meta boxes to be added */
		do_action('add_meta_boxes_'.self::MAP_SCREEN_ID, null);
		do_action('add_meta_boxes', self::MAP_SCREEN_ID, null);

		/* Add screen option: user can choose between 1 or 2 columns (default 2) */
		add_screen_option('layout_columns', array('max' => 2, 'default' => 2) );
	}


	/**
	* Function to add a few metabox
	* Hooked to 'add_meta_box_{self::MAP_SCREEN_ID}'
	*@since 3.0.0
	*/
	public function add_metabox(){
		//submit
		add_meta_box(
				'submit', //Meta box ID
				__('Save form to post','cf7-2-post'), //Meta box Title
				array($this,'show_submit_metabox'), //Callback defining the plugin's innards
				self::MAP_SCREEN_ID, // Screen to which to add the meta box
				'side' // Context
		);
		//helper
		add_meta_box(
				'helper', //Meta box ID
				__('Actions &amp; Filers','cf7-2-post'), //Meta box Title
				array($this,'show_helper_metabox'), //Callback defining the plugin's innards
				self::MAP_SCREEN_ID, // Screen to which to add the meta box
				'side' // Context
		);
		//field
		add_meta_box(
				'field', //Meta box ID
				__('Custom Meta Fields','cf7-2-post'), //Meta box Title
				array($this,'show_field_metabox'), //Callback defining the plugin's innards
				self::MAP_SCREEN_ID, // Screen to which to add the meta box
				'normal' // Context
		);
		//taxonomy
		add_meta_box(
				'taxonomy', //Meta box ID
				__('Taxonomies','cf7-2-post'), //Meta box Title
				array($this,'show_taxonomy_metabox'), //Callback defining the plugin's innards
				self::MAP_SCREEN_ID, // Screen to which to add the meta box
				'normal' // Context
		);
	}


	/**
	* Display submit metabox
	* Callback fn above.
	*@since 3.0.0
	*/
	public function show_submit_metabox(){
		include_once plugin_dir_path(__FILE__) . 'partials/cf7-2-post-submit-metabox.php';
	}


	/**
	* Display helper metabox
	* Callback fn above.
	*@since 3.0.0
	*/
	public function show_helper_metabox(){
		include_once plugin_dir_path(__FILE__) . 'partials/cf7-2-post-helper-metabox.php';
	}


	/**
	* Display metafield metabox
	* Callback fn above.
	*@since 3.0.0
	*/
	public function show_field_metabox(){
		include_once plugin_dir_path(__FILE__) . 'partials/cf7-2-post-field-metabox.php';
	}


	/**
	* Display taxonomy metabox
	* Callback fn above.
	*@since 3.0.0
	*/
	public function show_taxonomy_metabox(){
		include_once plugin_dir_path(__FILE__) . 'partials/cf7-2-post-taxonomy-metabox.php';
	}


	/**
	* Modify cf7 post type list table columns
	* Hooked on 'modify_{$post_type}_posts_columns', to remove the default columns
	* @since 1.0.0
	* @param      Array    $columns       IDs of existing columns.
	* @return     Array    $columns       IDs of table columns.
	*/
	public function modify_cf7_list_columns($columns){
		$capability = apply_filters('cf7_2_post_mapping_capability', 'manage_options');
		if(current_user_can($capability)){
			//$columns['mapped_post']= __( 'Post Type', 'cf7-2-post');
			$columns['map_cf7_2_post']= __( 'Form to post', 'cf7-2-post');
		}
		return $columns;
	}


	/**
	* Populate custom columns in cf7 list table
	* @since 1.0.0
	* @param      String    $column       ID of current column to populate.
	* @param      int    $post_id    CF7 post ID for the row to fill.
	*/
	public function populate_custom_column( $column, $post_id ) {
		$capability = apply_filters('cf7_2_post_mapping_capability', 'manage_options');
		if(!current_user_can($capability)){
			return;
		}
		switch ( $column ) {
			case 'map_cf7_2_post' :
					$post_type =  get_post_meta( $post_id , '_cf7_2_post-type' , true );
					//$form = WPCF7_ContactForm::get_instance($post_id);
					if ($post_type){
						$status = get_post_meta( $post_id , '_cf7_2_post-map' , true );
						$url = admin_url( 'admin.php?page=map_cf7_2_post&id=' . $post_id . '&action=edit' );
						echo '<a class="cf7-2-post-map-link" href="'.$url.'">'.('draft'==$status ? 'Draft:':'Mapped:').$post_type.'</a>';
						echo '<input type="hidden" class="cf7-2-post-status" value="'.$status.'"/>';
					}else{
						$url = admin_url( 'admin.php?page=map_cf7_2_post&id=' . $post_id . '&action=new' );
						echo '<a class="cf7-2-post-map-link" href="'.$url.'">Create new</a>';
					}
					break;
		}
	}


	/**
	 * Function to populate the quick edit form
	 * Hooked on 'quick_edit_custom_box' action
	 *
	 * @since 1.0.0
	 * @param      string    $column_name     column name to add edit field.
	 * @param      string    $post_type     post type being displayed.
	 * @return     string    echos the html fields.
	**/
	public function quick_edit_box( $column_name, $post_type ) {
		if("wpcf7_contact_form" != $post_type){
			return;
		}
		$capability = apply_filters('cf7_2_post_mapping_capability', 'manage_options');
		if(!current_user_can($capability)){
			return;
		}
		static $printNonce = TRUE;
		if ( $printNonce ) {
				$printNonce = FALSE;
				wp_nonce_field('cf7_2_post_quick_edit' , 'cf7_2_post_quick_edit' );
		}
		switch ( $column_name ) {
			case 'map_cf7_2_post':
				include_once( plugin_dir_path( __FILE__ ) . 'partials/cf7-2-post-quick-edit.php' );
				break;
		}
	}


	/**
	 * Saves Quick-edits changes
	 * Hooked to save_post_wpcf7_contact_form
	 * @since 2.0.0
	 * @param      string    $post_id     post ID.
	**/
	public function save_quick_edit($post_id){
		//debug_msg($_POST);
		if(!isset($_POST['cf7_2_post_quick_edit'])){
			return;
		}
		if(!wp_verify_nonce($_POST['cf7_2_post_quick_edit'],'cf7_2_post_quick_edit')){
			return;
		}
		if(isset($_POST['cf7_2_post_status'])){
			switch($_POST['cf7_2_post_status']){
				case 'draft':
				case 'publish':
					update_post_meta($post_id, '_cf7_2_post-map', $_POST['cf7_2_post_status']);
					break;
				case 'delete':
					//reset mapping
					$mappings = get_post_meta($post_id);
					foreach($mappings as $key=>$value){
						switch(true){
							case (0 === strpos($key,'_cf7_2_post-')):
							case (0 === strpos($key,'cf7_2_post_map-')):
							case (0 === strpos($key,'cf7_2_post_map_meta-')):
								delete_post_meta($post_id, $key);
								//debug_msg('deleting: '.$key);
								break;
						}
					}
					break;
			}
		}
	}


	/**
	* Display the custom admin page for creating post
	* This is a call back function based on the admin menu hook
	* @since 3.0.0
	*/
	public function display_mapping_page(){
		if( isset($_GET['id']) ){
			$cf7_post_id = $_GET['id'];
			if( isset($this->post_mapping_factory) && $cf7_post_id == $this->post_mapping_factory->get_cf7_post_id() ){
				$factory_mapping = $this->post_mapping_factory;
			}else{
				$factory_mapping = Cf7_2_Post_System::get_factory($cf7_post_id);
				$this->post_mapping_factory = $factory_mapping;
			}
			include( plugin_dir_path( __FILE__ ) . 'partials/cf7-2-post-edit-mapping.php');
		}else{
			$adminUrl = admin_url('edit.php?post_type=wpcf7_contact_form');
			echo '<div><h2>Ooops! Have you taken a wrong turn?</h2>';
			echo '<p>This page is for mapping a CF7 form to a custom post,';
			echo ' please access it from the from <a href="'.$adminUrl.'">table list page</a>.</p></div>';
		}
	}


	/**
	*Save draft with Ajax data submission from admin form.
	* @since 1.0.0
	*/
	public function ajax_save_post_mapping(){
		//debug_msg($_POST, "save post ");
		if( !isset($_POST['cf7_2_post_nonce']) || !wp_verify_nonce( $_POST['cf7_2_post_nonce'],'cf7_2_post_mapping') ){
			wp_send_json_error("Security failed, try to reload the page");
			die();
		}
		if( isset( $_POST['cf7_post_id'] ) ){

			$cf7_post_id = $_POST['cf7_post_id'];
			$this->post_mapping_factory = Cf7_2_Post_System::get_factory($cf7_post_id);
			if($this->post_mapping_factory->is_system_published()){
				$json_data = array('msg'=>'Nothing to update');
				wp_send_json_error($json_data);
				die();
			}
			$create_or_update = false;
			$json_data=array('msg'=>'Unknown action', 'post'=>'unknown');;
			switch(true){
				case isset($_POST['save_draft']):
					$create_or_update = false;
					$result = $this->post_mapping_factory->save($_POST, $create_or_update);
					$json_data = array('msg'=>'Saved draft', 'post'=>'saved');
					break;
				case isset($_POST['update_post']):
					$create_or_update = true;
					$result = $this->post_mapping_factory->update($_POST);
					$json_data = array('msg'=>'Updated post', 'post'=>'created');
					break;
				case isset($_POST['save_post']):
					$create_or_update = true;
					$result = $this->post_mapping_factory->save($_POST, $create_or_update);
					$json_data = array('msg'=>'Created post', 'post'=>'created');
					break;
			}

			if($result){
				//wp_send_json_success( $data );
				wp_send_json_success( $json_data );
			}else{
				$json_data = array('msg'=>'Something is wrong, try to reload the page');
				wp_send_json_error($json_data);
			}
		}else{
			$json_data = array('msg'=>'No CF7 post ID, try to reload the page');
			wp_send_json_error($json_data);
		}
		die();
	}


	/**
	*Disables browser page caching for forms which are mapped to a post.
	* Hooked on 'wp_head' in fn load_cf7_script.
	*@since 3.0.0
	*/
	public function disable_browser_page_cache(){
		$screen = get_current_screen();
		if(self::MAP_SCREEN_ID !== $screen->id) return;
			?>
		<meta http-equiv="cache-control" content="max-age=0" />
		<meta http-equiv="cache-control" content="no-cache" />
		<meta http-equiv="expires" content="0" />
		<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
		<meta http-equiv="pragma" content="no-cache" />
			<?php
	}


	/**
	 * Ajax Load system post options
	 * Hooked on 'wp_ajax'
	 * @since 2.0.0
	**/
	public function ajax_get_meta_options(){
		if( !isset($_POST['cf7_2_post_nonce']) || !wp_verify_nonce( $_POST['cf7_2_post_nonce'],'cf7_2_post_mapping') ){
			wp_send_json_error("Security failed, try to reload the page");
		}
		if( isset($_POST['post_type'])){
			$json_data = array(
				'options' => Cf7_2_Post_System::get_system_post_metas($_POST['post_type'])
			);
			wp_send_json_success( $json_data );
		}else{
			wp_send_json_error(array('msg'=>'no post_type defined'));
		}
		die();
	}


	/**
	* Loads the custom posts created into the dashboard.
	* @since 1.0.0
	*/
	public function register_dynamic_posts(){
		Cf7_2_Post_Factory::register_cf7_post_maps();
	}


	/**
	 * Delete existing fields for a given cf7 form, as well as all post data
	 * This funciton is hooked on 'wpcf7_post_delete', a filter created by hooking on the cf7 plugin 'wp_redirect' hook in the 'cf7-post-admin-table.php' file
	 * @since 1.0.0
	 * @param      int    $cf7_post_id    The ID of the cf7 form to be deleted .
	**/
	public function delete_cf7_post($cf7_post_id){
		if(Cf7_2_Post_Factory::is_mapped($cf7_post_id)){
			$delete_all_posts = false;
			//TODO load settings to allow users to delete all submitted form post data when deleting a mapping
			$factory = Cf7_2_Post_Factory::get_factory($cf7_post_id);
			$factory->delete_mapping($delete_all_posts);

		}
	}


	/**
	 * Adds a 'save' button shortcode to cf7 forms
	 *
	 * @since 2.0.0
	**/
	public function cf7_shortcode_tags(){
		if ( class_exists( 'WPCF7_TagGenerator' ) ) {
			$tag_generator = WPCF7_TagGenerator::get_instance();
			$tag_generator->add(
				'save', //tag id
				__( 'save', 'cf7_2_post' ), //tag button label
				array($this,'save_tag_generator'), //callback
				array( 'nameless' => 1 ) //option name less = true, ie no name for this tag
			);
		}
	}


	/**
	 * Save button tag screen display.
	 *
	 * This function is called by cf7 plugin, and is registered with a hooked function above
	 *
	 * @since 1.0.0
	 * @param WPCF7_ContactForm $contact_form the cf7 form object
	 * @param array $args arguments for this form.
	 */
	public function save_tag_generator( $contact_form, $args = '' ) {
		$args = wp_parse_args( $args, array() );
		include( plugin_dir_path( __FILE__ ) . '/partials/cf7-tag-display.php');
	}
	public function inject_footer_script(){
		include( plugin_dir_path( __FILE__ ) . '/partials/cf72post-footer-script.php');
	}
}