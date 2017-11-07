<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      {{plugin.version}}
 * @package    {{plugin.package}}
 * @subpackage {{plugin.package}}/classes
 */
class {{plugin.package}}_Admin {


	
	public $labels;
	
	public $args;
	

	

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    {{plugin.version}}
	 */
	function __construct() {
		add_action( 'init', array( $this, 'super_post_type' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );

		add_action( 'add_meta_boxes', array($this,'add_meta_boxes'));

		add_action( 'save_post', array($this,'save_meta_boxes'), 1, 2 );

		$this->labels = array(
			'name'               => __( 'Plural Name', '{{plugin.slug}}' ),
			'singular_name'      => __( 'Singular Name', '{{plugin.slug}}' ),
			'add_new'            => _x( 'Add New Singular Name', '{{plugin.slug}}', '{{plugin.slug}}' ),
			'add_new_item'       => __( 'Add New Singular Name', '{{plugin.slug}}' ),
			'edit_item'          => __( 'Edit Singular Name', '{{plugin.slug}}' ),
			'new_item'           => __( 'New Singular Name', '{{plugin.slug}}' ),
			'view_item'          => __( 'View Singular Name', '{{plugin.slug}}' ),
			'search_items'       => __( 'Search Plural Name', '{{plugin.slug}}' ),
			'not_found'          => __( 'No Plural Name found', '{{plugin.slug}}' ),
			'not_found_in_trash' => __( 'No Plural Name found in Trash', '{{plugin.slug}}' ),
			'parent_item_colon'  => __( 'Parent Singular Name:', '{{plugin.slug}}' ),
			'menu_name'          => __( 'Plural Name', '{{plugin.slug}}' )
			);
		$this->args = array(
			'labels'              => $this->labels,
			'hierarchical'        => false,
			'description'         => 'description',
			'taxonomies'          => array(),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => null,
			'menu_icon'           => null,
			'show_in_nav_menus'   => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'has_archive'         => true,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => true,
			'capability_type'     => 'post',
			'supports'            => array(
				'title',
				'editor',
				'author',
				'thumbnail',
				'excerpt',
				'custom-fields',
				'trackbacks',
				'comments',
				'revisions',
				'page-attributes',
				'post-formats',
				),
			);
	}

	public function super_post_type() {
		/**
		 * Registers a new post type
		 * @uses $wp_post_types Inserts new post type object into the list
		 *
		 * @param string  Post type key, must not exceed 20 characters
		 * @param array|string  See optional args description above.
		 * @return object|WP_Error the registered post type object, or an error object
		 */
		
		$labels = array(
			'name'               => __( 'Post Types', '{{plugin.slug}}' ),
			'singular_name'      => __( 'Post Type', '{{plugin.slug}}' ),
			'add_new'            => _x( 'Add New Post Type', '{{plugin.slug}}', '{{plugin.slug}}' ),
			'add_new_item'       => __( 'Add New Post Type', '{{plugin.slug}}' ),
			'edit_item'          => __( 'Edit Post Type', '{{plugin.slug}}' ),
			'new_item'           => __( 'New Post Type', '{{plugin.slug}}' ),
			'view_item'          => __( 'View Post Type', '{{plugin.slug}}' ),
			'search_items'       => __( 'Search Post Types', '{{plugin.slug}}' ),
			'not_found'          => __( 'No Post Types found', '{{plugin.slug}}' ),
			'not_found_in_trash' => __( 'No Post Types found in Trash', '{{plugin.slug}}' ),
			'parent_item_colon'  => __( 'Parent Post Type:', '{{plugin.slug}}' ),
			'menu_name'          => __( 'Post Types', '{{plugin.slug}}' ),
			);
		
		$args = array(
			'labels'              => $labels,
			'hierarchical'        => true,
			'description'         => 'description',
			'taxonomies'          => array(),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => null,
			'menu_icon'           => null,
			'show_in_nav_menus'   => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'has_archive'         => true,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => true,
			'capability_type'     => 'post',
			'supports'            => array(
				'title',
				),
			);
		
		register_post_type( 'post-types', $args );


	}

	public function register_post_types () {

		$args = array(
			'post_type'   => 'post-types',
			'posts_per_page'         => -1,
			);
		
		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$labels = get_post_meta( get_the_id(), 'post_type_meta', true );
				$slug = $labels['slug'];
				unset($labels['slug']);
				$args = $this->args;
				$args['labels'] = $labels;
				register_post_type( $slug, $args );

			}
			wp_reset_postdata();
			wp_reset_query();
		}
	}

	public function add_meta_boxes() {
		add_meta_box(
			'slug',
			'Slug',
			array($this, 'meta_box_content'),
			'post-types',
			'normal',
			'default',
			array('key' => 'slug','value' => 'Slug')
			);

		foreach ($this->labels as $key => $value) {
			add_meta_box(
				$key,
				$value,
				array($this, 'meta_box_content'),
				'post-types',
				'normal',
				'default',
				array('key' => $key,'value' => $value)
				);
		}
	}

	public function meta_box_content($post,$callbackArgs) {
		global $post;
		$callbackArgs = $callbackArgs['args'];
		$key = $callbackArgs['key'];
		wp_nonce_field( basename( __FILE__ ), 'post_type_meta_nonce' );

		$value = get_post_meta( $post->ID, 'post_type_meta', true );
		if (isset($value[$key])) {
			$value = $value[$key];
		}
		else {
			$value = '';
		}
		echo '<input type="text" name="post_type_meta['.$key.']" value="' . esc_textarea( $value )  . '" class="widefat">';

	}

	public function save_meta_boxes( $post_id, $post ) {
	// Return if the user doesn't have edit permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}
	// Verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times.
		if ( ! isset( $_POST['post_type_meta'] ) || ! wp_verify_nonce( $_POST['post_type_meta_nonce'], basename(__FILE__) ) ) {
			return $post_id;
		}
		$meta_value = array_map( 'esc_attr', $_POST['post_type_meta'] );
		// Don't store custom data twice
		if ( 'revision' === $post->post_type ) {
			return;
		}
		if ( get_post_meta( $post_id, 'post_type_meta', false ) ) {
			// If the custom field already has a value, update it.
			update_post_meta( $post_id, 'post_type_meta', $meta_value );
		} else {
			// If the custom field doesn't have a value, add it.
			add_post_meta( $post_id, 'post_type_meta', $meta_value);
		}
		if ( ! $meta_value ) {
			// Delete the meta key if there's no value
			delete_post_meta( $post_id, 'post_type_meta' );
		}
	}


}
