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
        	'post_type'		=> 'event',
        	'post_status'	=> 'draft',

        	'post_title'    => $posted_data[event_title],
    	  	'post_status'   => 'draft',
        );

        $cities = get_terms(
        	array(
		    	'taxonomy' 		=> 'city',
		    	'hide_empty' 	=> false,
			)
        );

        foreach ( $cities as $city ) {
        	if ( $city->name === $posted_data[place_participating_city] ) {
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

        if ( $posted_data[place_address] ) {
    		array_push( $address, $posted_data[place_address] );
        }

        if ( $posted_data[place_city] ) {
    		array_push( $address, $posted_data[place_city] );
        }

        if ( $posted_data[place_address_supplement] ) {
    		array_push( $address, $posted_data[place_address_supplement] );
        }

        if ( $posted_data[place_postal_code] ) {
    		array_push( $address, $posted_data[place_postal_code] );
        }

        update_post_meta( $post_id, '_wpgm_details', array( 'address' => join( ', ', $address ) ) );

        // wp_die( var_dump( get_post_meta( $post_id ) ) );


        /////////////////////
        // Qui êtes-vous ? //
        /////////////////////

        // Particular
        if ( $posted_data[particular] ) {
        	update_field( 'field_5a6a583c34e75', $posted_data[particular], $post_id );
        }

        // Association name
        if ( $posted_data[association_name] ) {
        	 update_field( 'field_5a6a574df7240', $posted_data[association_name], $post_id );
        }

        // Association email
        if ( $posted_data[association_email] ) {
        	update_field( 'field_5a6a566a52e71', $posted_data[association_email], $post_id );
        }

        // Association phone
        if ( $posted_data[association_phone] ) {
        	 update_field( 'field_5a6a5738f723f', $posted_data[association_phone], $post_id );
        }


        /////////////////////////////
        // Informations de contact //
        /////////////////////////////

        // your_civility
        if ( $posted_data[your_civility] ) {
        	update_field( 'field_5a6ba39fd700e', $posted_data[your_civility], $post_id );
        }

        // your_name
        if ( $posted_data[your_name] ) {
        	update_field( 'field_5a6ba492f464a', $posted_data[your_name], $post_id );
        }

        // your_first_name
        if ( $posted_data[your_first_name] ) {
        	update_field( 'field_5a6ba488f4649', $posted_data[your_first_name], $post_id );
        }

        // your_email
        if ( $posted_data[your_email] ) {
        	update_field( 'field_5a6ba50af464b', $posted_data[your_email], $post_id );
        }

        // your_phone
        if ( $posted_data[your_phone] ) {
        	update_field( 'field_5a6ba538f464c', $posted_data[your_phone], $post_id );
        }


        /////////////////////////
        // Lieu de l'évènement //
        /////////////////////////

        // place_type
        if ( $posted_data[place_type] ) {
        	update_field( 'field_5a6b9efc1ca76', $posted_data[place_type], $post_id );
        }

        // place_name
        if ( $posted_data[place_name] ) {
        	update_field( 'field_5a5fc10ae7b4d', $posted_data[place_name], $post_id );
        }

        // place_participating_city
        if ( $posted_data[place_participating_city] ) {
        	wp_set_object_terms(
        		$post_id,
        		$posted_data[place_participating_city],
        		'city',
        		false
        	);
        }

        // place_address
        if ( $posted_data[place_address] ) {
        	update_field( 'field_5a6ba027c718b', $posted_data[place_address], $post_id );
        }

        // place_city
        if ( $posted_data[place_city] ) {
        	update_field( 'field_5a6ba049c718c', $posted_data[place_city], $post_id );
        }

        // place_address_supplement
        if ( $posted_data[place_address_supplement] ) {
        	update_field( 'field_5a6ba072c718d', $posted_data[place_address_supplement], $post_id );
        }

        // place_postal_code
        if ( $posted_data[place_postal_code] ) {
        	update_field( 'field_5a6ba0a3c718e', $posted_data[place_postal_code], $post_id );
        }


        ////////////////////////////////
        // Paramétrage de l’événement //
        ////////////////////////////////

       	// event_category
       	if ( $posted_data[event_category] ) {
       		wp_set_object_terms(
       			$post_id,
       			$posted_data[event_category],
       			'event_category',
       			false
       		);
       	}

       	// Saturday
       	$saturdays = explode( ',', $posted_data[saturday_repeater] );
       	$saturdays = array_splice( $saturdays , 1, count( $saturdays ) );
       	$saturdays_value = [];

       	for ( $i = 0; $i <= ( count( $saturdays ) / 3 ) - 1; $i++ ) {
    		$index = "-{$i}";

       		if ( $index === '-0' ) {
       			$index = '';
       		}

       		$saturday_value = array(
       			'hour'		=> $posted_data['event_date_saturday_hour' . $index],
       			'duration'	=> $posted_data['event_date_saturday_duration' . $index],
       			'end'		=> $posted_data['event_date_saturday_end' . $index],
       		);

       		array_push( $saturdays_value, $saturday_value );
       	}
       	update_field( 'field_5a6c8c24e73e4', $saturdays_value, $post_id );


       	// Sunday
   	   	$sundays = explode( ',', $posted_data[sunday_repeater] );
   	   	$sundays = array_splice( $sundays , 1, count( $sundays ) );
   	   	$sundays_value = [];

   	   	for ( $i = 0; $i <= ( count( $sundays ) / 3 ) - 1; $i++ ) {
   			$index = "-{$i}";

   	   		if ( $index === '-0' ) {
   	   			$index = '';
   	   		}

   	   		$sunday_value = array(
   	   			'hour'		=> $posted_data['event_date_sunday_hour' . $index],
   	   			'duration'	=> $posted_data['event_date_sunday_duration' . $index],
   	   			'end'		=> $posted_data['event_date_sunday_end' . $index],
   	   		);

   	   		array_push( $sundays_value, $sunday_value );
   	   	}
   	   	update_field( 'field_5a6c8cd3e6712', $sundays_value, $post_id );

   	   	// event_prices
		if ( $posted_data[event_prices] ) {
			update_field( 'field_5a6c8d41e6717', $posted_data[event_prices], $post_id );
		}

		// event_places_number
		if ( $posted_data[event_places_number] ) {
			update_field( 'field_5a6c8d12e6716', $posted_data[event_places_number], $post_id );
			update_field( 'field_5a70ec191d80f', $posted_data[event_places_number], $post_id );
		}

		// event_minimum_ages
		if ( $posted_data[event_minimum_ages] ) {
			update_field( 'field_5a6c8d5de6718', $posted_data[event_minimum_ages], $post_id );
		}

		// event_description
		if ( $posted_data[event_description] ) {
			update_field( 'field_5a633a9d94992', $posted_data[event_description], $post_id );
		}

		////////////////////
		// Ajout de média //
		////////////////////

		require_once( ABSPATH . 'wp-admin/includes/image.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );

		// image
        $event_attachments = array( 'event-image-1', 'event-image-2', 'event-image-3' );
        $event_attachments_id = [];

    	var_dump($_FILES);
        foreach( $event_attachments as $event_attachment ) {
            $event_attachment_id = media_handle_upload( $event_attachment, 0 );
        	// var_dump( $event_attachment_id );

            if ( $event_attachment_id ) {
            	array_push( $event_attachments_id, $event_attachment_id );
            }
        }
		update_field( 'field_5a6dd43daa1dc', $event_attachments_id, $post_id );


		// partners
	   	$partners = explode( ',', $posted_data[partners_repeater] );
	   	$partners = array_splice( $partners , 1, count( $partners ) );
	   	$partners_attachment_id = [];

	   	for ( $i = 0; $i <= count( $partners ); $i++ ) {
	   		$partner_attachment_id = media_handle_upload( $partners[$i], 0 );

	   		if ( $partner_attachment_id ) {
	   			array_push( $partners_attachment_id, $partner_attachment_id );
	   		}
	   	}
	   	update_field( 'field_5a6dcdd1766c1', $partners_attachment_id, $post_id );


		// youtube
		if ( $posted_data[youtube] ) {
			update_field( 'field_5a6dcd81a44b5', $posted_data[youtube], $post_id );
		}

		wp_die(var_dump($event_attachments_id));

        return $posted_data;
	}
}
