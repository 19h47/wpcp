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
 * @author     Jérémy Levron <jeremylevron@19h47.fr>
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


	/**
	 * Filter WPCF7 validate file
	 *
	 * @param 	$result
	 * @param 	$tag
	 * @return 	$result
	 * @author  	Jérémy Levron <jeremylevron@19h47.fr>
	 * @see  	https://gist.github.com/thetrickster/35b4d402b0feeae7074d
	 */
	function filter_wpcf7_validate_file( $result, $tag ) {
	  	$name = $tag['name'];

	    	$inputs_file = ['event-image-1', 'event-image-2', 'event-image-3'];

    		if ( ! in_array( $name, $inputs_file, true ) ) {
    			return $result;
    		}

    		if ( empty( $_FILES[$tag->name]['tmp_name'] ) ) {
    			return $result;
    		}

	    	$sizes = getimagesize( $_FILES[$tag->name]['tmp_name'] );

    		// Width
	    	if ( $sizes[0] < 500 ) {
    			$result['valid'] = false;
          		$result['reason'] = array( $name => wpcf7_get_message( 'invalid_file_width' ) );
	    	}

    		// Height
		if ( $sizes[1] < 350 ) {
			$result['valid'] = false;
	      		$result['reason'] = array( $name => wpcf7_get_message( 'invalid_file_height' ) );
		}

		return $result;
	}


	/**
	 * Filter WPCF7 custom validation messages
	 *
	 * @param  arr $messages
	 * @return arr $message
	 * @author  Jérémy Levron <jeremylevron@19h47.fr>
	 * @see  	https://gist.github.com/thetrickster/35b4d402b0feeae7074d
	 */
	function filter_wpcf7_custom_validation_messages( $messages ) {
		return array_merge(
			$messages,
			array(
				'invalid_file_width' => array(
				  'description' 	=> __( 'The user uploaded a too small picture, the width must be at least 500px wide.', 'contact-form-7' ),
				  'default' 		=> __( 'The image must be 500px wide minimum', 'contact-form-7' )
				),
				'invalid_file_height' => array(
					'description' 	=> __( 'The user uploaded a too small picture, the height must be at least 350px high.', 'contact-form-7' ),
					'default' 		=> __( 'The image must be 350px high minimum', 'contact-form-7' )
				)
			)
		);
	}


	/**
	 * create post
	 *
	 */
	public function create_post( $contact_form ) {

		$submission = WPCF7_Submission::get_instance();

		if ( ! $submission ) {
			return false;
		}

		if ( $contact_form->id() !== 334 ) {
			return $components;
		}

		// Get data from $submission object
		$posted_data = $submission->get_posted_data();

		// var_dump($posted_data);

		// Create post object
		$your_post = array(
			// Event title
			'post_type'     => 'event',
			'post_status'   => 'draft',
			'post_title'    => $posted_data['event_title'],
			'post_status'   => 'draft',
		);

		$cities = get_terms(
			array(
				'taxonomy'      => 'city',
				'hide_empty'    => false,
			)
		);

		foreach ( $cities as $city ) {
			if ( $city->name === $posted_data['place_participating_city'] ) {
				$your_post['post_author'] = get_field( 'user', 'city_' . $city->term_id )['ID'];

				break;
			}
		}


		$post_id = wp_insert_post( $your_post );

		if ( ! $post_id ) {
			return false;
		}

		// WPGM
		$address = [];

		if ( $posted_data['place_address'] ) {
			array_push( $address, $posted_data['place_address'] );
		}

		if ( $posted_data['place_city'] ) {
			array_push( $address, $posted_data['place_city'] );
		}

		if ( $posted_data['place_address_supplement'] ) {
			array_push( $address, $posted_data['place_address_supplement'] );
		}

		if ( $posted_data['place_postal_code'] ) {
			array_push( $address, $posted_data['place_postal_code'] );
		}

		update_post_meta( $post_id, '_wpgm_details', array( 'address' => join( ', ', $address ) ) );

		
		/////////////////////
		// Qui êtes-vous ? //
		/////////////////////

		// Particular
		if ( $posted_data['particular'] ) {
			update_field( 'field_5a6a583c34e75', $posted_data['particular'], $post_id );
		}

		// Association name
		if ( $posted_data['association_name'] ) {
			 update_field( 'field_5a6a574df7240', $posted_data['association_name'], $post_id );
		}

		// Association email
		if ( $posted_data['association_email'] ) {
			update_field( 'field_5a6a566a52e71', $posted_data['association_email'], $post_id );
		}

		// Association phone
		if ( $posted_data['association_phone'] ) {
			 update_field( 'field_5a6a5738f723f', $posted_data['association_phone'], $post_id );
		}

		// Place name
		if ( $posted_data['place_name'] ) {
			update_field( 'field_5a75ed0e3532a', $posted_data['place_name'], $post_id );
		}

		// Place email
		if ( $posted_data['place_email'] ) {
			update_field( 'field_5a75ed9c3532b', $posted_data['place_email'], $post_id );
		}

		// Place phone
		if ( $posted_data['place_phone'] ) {
			update_field( 'field_5a75edbe3532c', $posted_data['place_phone'], $post_id );
		}


		/////////////////////////////
		// Informations de contact //
		/////////////////////////////

		// your_civility
		if ( $posted_data['your_civility'] ) {
			update_field( 'field_5a6ba39fd700e', $posted_data['your_civility'], $post_id );
		}

		// your_name
		if ( $posted_data['your_name'] ) {
			update_field( 'field_5a6ba492f464a', $posted_data['your_name'], $post_id );
		}

		// your_first_name
		if ( $posted_data['your_first_name'] ) {
			update_field( 'field_5a6ba488f4649', $posted_data['your_first_name'], $post_id );
		}

		// your_email
		if ( $posted_data['your_email'] ) {
			update_field( 'field_5a6ba50af464b', $posted_data['your_email'], $post_id );
		}

		// your_phone
		if ( $posted_data['your_phone'] ) {
			update_field( 'field_5a6ba538f464c', $posted_data['your_phone'], $post_id );
		}


		/////////////////////////
		// Lieu de l'évènement //
		/////////////////////////

		// place_type
		if ( $posted_data['place_type'] ) {
			update_field( 'field_5a6b9efc1ca76', $posted_data['place_type'], $post_id );
		}

		// place_public_name
		if ( $posted_data['place_public_name'] ) {
			update_field( 'field_5a5fc10ae7b4d', $posted_data['place_public_name'], $post_id );
		}

		// place_participating_city
		if ( $posted_data['place_participating_city'] ) {
			wp_set_object_terms(
				$post_id,
				$posted_data['place_participating_city'],
				'city',
				false
			);
		}

		// place_address
		if ( $posted_data['place_address'] ) {
			update_field( 'field_5a6ba027c718b', $posted_data['place_address'], $post_id );
		}

		// place_city
		if ( $posted_data['place_city'] ) {
			update_field( 'field_5a6ba049c718c', $posted_data['place_city'], $post_id );
		}

		// place_address_supplement
		if ( $posted_data['place_address_supplement'] ) {
			update_field( 'field_5a6ba072c718d', $posted_data['place_address_supplement'], $post_id );
		}

		// place_postal_code
		if ( $posted_data['place_postal_code'] ) {
			update_field( 'field_5a6ba0a3c718e', $posted_data['place_postal_code'], $post_id );
		}


		////////////////////////////////
		// Paramétrage de l’événement //
		////////////////////////////////

		// event_category
		if ( $posted_data['event_category'] ) {
			wp_set_object_terms(
				$post_id,
				$posted_data['event_category'],
				'event_category',
				false
			);
		}


		// Friday
		$fridays = explode( ',', $posted_data['friday_repeater'] );
		$fridays = array_splice( $fridays , 1, count( $fridays ) );
		$fridays_value = [];

		if ( ! empty( $posted_data['event_date_friday_hour'] ) && ! empty( $posted_data['event_date_friday_duration'] ) && ! empty( $posted_data['event_date_friday_end'] ) ) {

			for ( $i = 0; $i <= ( count( $fridays ) / 3 ) - 1; $i++ ) {
				$index = "-{$i}";

				if ( $index === '-0' ) {
					$index = '';
				}

				$friday_value = array(
					'hour'      => $posted_data['event_date_friday_hour' . $index],
					'duration'  => $posted_data['event_date_friday_duration' . $index],
					'end'       => $posted_data['event_date_friday_end' . $index],
				);

				if ( $posted_data['event_places_number'] ) {
					$friday_value['event_places_number'] = $posted_data['event_places_number'];
				}

				array_push( $fridays_value, $friday_value );
			}
			update_field( 'field_5aaa221e302f8', $fridays_value, $post_id );
		}


		// Saturday
		$saturdays = explode( ',', $posted_data['saturday_repeater'] );
		$saturdays = array_splice( $saturdays , 1, count( $saturdays ) );
		$saturdays_value = [];

		if ( ! empty( $posted_data['event_date_saturday_hour'] ) && ! empty( $posted_data['event_date_saturday_duration'] ) && ! empty( $posted_data['event_date_saturday_end'] ) ) {

			for ( $i = 0; $i <= ( count( $saturdays ) / 3 ) - 1; $i++ ) {
				$index = "-{$i}";

				if ( $index === '-0' ) {
					$index = '';
				}

				$saturday_value = array(
					'hour'      => $posted_data['event_date_saturday_hour' . $index],
					'duration'  => $posted_data['event_date_saturday_duration' . $index],
					'end'       => $posted_data['event_date_saturday_end' . $index],
				);

				if ( $posted_data['event_places_number'] ) {
					$saturday_value['event_places_number'] = $posted_data['event_places_number'];
				}

				array_push( $saturdays_value, $saturday_value );
			}
			update_field( 'field_5a6c8c24e73e4', $saturdays_value, $post_id );
		}



		// Sunday
		$sundays = explode( ',', $posted_data['sunday_repeater'] );
		$sundays = array_splice( $sundays , 1, count( $sundays ) );
		$sundays_value = [];

		if ( ! empty( $posted_data['event_date_sunday_hour'] ) && ! empty( $posted_data['event_date_sunday_duration'] ) && ! empty( $posted_data['event_date_sunday_end'] ) ) {

			for ( $i = 0; $i <= ( count( $sundays ) / 3 ) - 1; $i++ ) {
				$index = "-{$i}";

				if ( $index === '-0' ) {
					$index = '';
				}

				$sunday_value = array(
					'hour'      => $posted_data['event_date_sunday_hour' . $index],
					'duration'  => $posted_data['event_date_sunday_duration' . $index],
					'end'       => $posted_data['event_date_sunday_end' . $index],
				);

				if ( $posted_data['event_places_number'] ) {
					$sunday_value['event_places_number'] = $posted_data['event_places_number'];
				}

				array_push( $sundays_value, $sunday_value );
			}
			update_field( 'field_5a6c8cd3e6712', $sundays_value, $post_id );
		}


		// event_prices
		if ( $posted_data['event_prices'] ) {
			update_field( 'field_5a6c8d41e6717', $posted_data['event_prices'], $post_id );
		}

		// event_places_number
		if ( $posted_data['event_places_number'] ) {
			update_field( 'field_5a70ec191d80f', $posted_data['event_places_number'], $post_id );
		}

		// event_minimum_ages
		if ( $posted_data['event_minimum_ages'] ) {
			update_field( 'field_5a6c8d5de6718', $posted_data['event_minimum_ages'], $post_id );
		}

		// event_description
		if ( $posted_data['event_description'] ) {
			update_field( 'field_5a633a9d94992', $posted_data['event_description'], $post_id );
		}

		
		////////////////////
		// Ajout de média //
		////////////////////

		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );

		// var_dump( $_FILES );

		// image
		$event_attachments_id = [];
		$event_attachments_image = array( 'event-image-1', 'event-image-2', 'event-image-3' );


		foreach ( $event_attachments_image as $event_attachment_image ) {

			if ( ! isset( $submission->uploaded_files()[$event_attachment_image] ) ) continue;

			$updloaded_files = $submission->uploaded_files()[$event_attachment_image];

			$content = file_get_contents( $updloaded_files );

			$filename = $_FILES[$event_attachment_image]['name'];

			$upload = wp_upload_bits( $filename, null, $content);

			$wp_check_filetype = wp_check_filetype( basename( $upload['file'] ), null );
			$wp_upload_dir = wp_upload_dir();

			$attachment = array(
				'guid'           => $wp_upload_dir['url'] . '/' . sanitize_title( basename( $filename ) ),
				'post_mime_type' => $wp_check_filetype['type'],
				'post_title'     => preg_replace( '/\.[^.]+$/', '', sanitize_title( basename( $filename ) ) ),
				'post_content'   => '',
				'post_status'    => 'inherit'
			);

			$attach_id = wp_insert_attachment( $attachment, $upload['file'], $post_id );

			// There was an error uploading the image.
			if ( $attach_id === 0 ) continue;

			$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
			wp_update_attachment_metadata( $attach_id, $attach_data );

			array_push( $event_attachments_id, $attach_id );
		}
		update_field( 'field_5a6dd43daa1dc', $event_attachments_id, $post_id );


		// partners
		// Get partners repeater
		$partners = explode( ',', $posted_data['partners_repeater'] );
		// Remove the first entry (Useless for media upload)
		$partners = array_splice( $partners , 1, count( $partners ) );
		$partners_attachment_id = [];

		foreach ( $partners as $partner ) {

			$partner_attachment_id = media_handle_upload( $partner, 0 );

			// There was an error uploading the image.
			if ( is_wp_error( $partner_attachment_id ) ) continue;

			array_push( $partners_attachment_id, $partner_attachment_id );
		}
		update_field( 'field_5a6dcdd1766c1', $partners_attachment_id, $post_id );

		// youtube
		if ( $posted_data['youtube'] ) {
			update_field( 'field_5a6dcd81a44b5', $posted_data['youtube'], $post_id );
		}

		return $posted_data;
	}
}
