<?php
/**
* To change this license header, choose License Headers in Project Properties,
* To change this template file, choose Tools | Templates,
* and open the template in the editor.
*
* @link       https://www.orionorigin.com/
* @since      1.0.0
*
* @package    Vpc_Cta
* @subpackage Vpc_Cta/public
*/

/**
* Description of class-mb-confog
*
* @author HL
*/
class Vpc_Upload_Component {

	/**
	* The ID of the current component upload.
	*
	* @access   public
	* @var string $id The ID of the current component upload.
	*/
	public $id;

	/**
	* The datas of the current component upload.
	*
	* @access   public
	* @var array $settings The datas of the current component upload.
	*/
	public $settings;

	/**
	* Initialize the class and set its properties.
	*
	* @method __construct
	* @param  string $config_id The ID of the component upload.
	*/
	public function __construct( $config_id ) {
		if ( $config_id ) {
			$this->id       = $config_id;
			$this->settings = get_post_meta( $config_id, 'vpc-upload-component', true );
		}
	}

	/**
	* Register the config custom post type.
	*/
	public function register_cpt_config() {
		$labels = array(
			'name'               => _x( 'Upload Component', 'vpc-upload' ),
			'singular_name'      => _x( 'Upload Component', 'vpc-upload' ),
			'add_new'            => _x( 'New Upload component', 'vpc-upload' ),
			'add_new_item'       => _x( 'New Upload component', 'vpc-upload' ),
			'edit_item'          => _x( 'Edit Upload component', 'vpc-upload' ),
			'new_item'           => _x( 'New Upload component', 'vpc-upload' ),
			'view_item'          => _x( 'View Upload component', 'vpc-upload' ),
			'not_found'          => _x( 'No Upload component found', 'vpc-upload' ),
			'not_found_in_trash' => _x( 'No Upload component in the trash', 'vpc-upload' ),
			'menu_name'          => _x( 'Product Builder', 'vpc-upload' ),
			'all_items'          => _x( 'Upload Components', 'vpc-upload' ),
		);

		$args = array(
			'labels'              => $labels,
			'hierarchical'        => false,
			'description'         => 'Upload Components',
			'supports'            => array( 'title' ),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_nav_menus'   => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'query_var'           => false,
			'can_export'          => true,
		);
		register_post_type( 'vpc-upload-component', $args );
	}

	/**
	* Adds the metabox for the config CPT.
	*/
	public function get_config_metabox() {
		$screens = array( 'vpc-upload-component' );
		foreach ( $screens as $screen ) {
			add_meta_box(
				'vpc-upload-component-settings-box',
				esc_html__( 'Upload component settings', 'vpc-upload' ),
				array( $this, 'get_upload_component_metabox' ),
				$screen
			);
		}
	}

	/**
	* Upload Component CPT metabox callback.
	*/
	public function get_upload_component_metabox() {
		wp_enqueue_media();
		?>
		<div class='block-form'>
			<?php
			$begin = array(
				'type' => 'sectionbegin',
				'id'   => 'vpc-upload-component-container',
			);

			$top  = array(
				'title'             => esc_html__( 'Top (unit: %)', 'vpc-upload' ),
				'id'                => 'vpc-upload-component-top',
				'name'              => 'vpc-upload-component[top]',
				'type'              => 'number',
				'default'           => '',
				'desc'              => esc_html__( 'Upload component\'s top position.', 'vpc-upload' ),
				'custom_attributes' => array( 'step' => 'any' ),
			);
			$left = array(
				'title'             => esc_html__( 'Left (unit: %)', 'vpc-upload' ),
				'id'                => 'vpc-upload-component-left',
				'name'              => 'vpc-upload-component[left]',
				'type'              => 'number',
				'default'           => '',
				'desc'              => esc_html__( 'Upload component\'s left position.', 'vpc-upload' ),
				'custom_attributes' => array( 'step' => 'any' ),
			);

			$box_width = array(
				'title'             => esc_html__( 'Image box width (unit: %)', 'vpc-upload' ),
				'id'                => 'vpc-upload-component-box-width',
				'name'              => 'vpc-upload-component[width]',
				'type'              => 'number',
				'default'           => '',
				'desc'              => esc_html__( 'Upload component\'s box width.', 'vpc-upload' ),
				'custom_attributes' => array( 'step' => 'any' ),
			);

			$box_height = array(
				'title'             => esc_html__( 'Image box height (unit: %)', 'vpc-upload' ),
				'id'                => 'vpc-upload-component-box-height',
				'name'              => 'vpc-upload-component[height]',
				'type'              => 'number',
				'default'           => '',
				'desc'              => esc_html__( 'Upload component\'s box height.', 'vpc-upload' ),
				'custom_attributes' => array( 'step' => 'any' ),
			);

			$rotation = array(
				'title'             => esc_html__( 'Angle (unit: °)', 'vpc-upload' ),
				'id'                => 'vpc-upload-component-rotation',
				'name'              => 'vpc-upload-component[angle]',
				'type'              => 'number',
				'default'           => '',
				'desc'              => esc_html__( 'Upload component\'s angle of rotation.', 'vpc-upload' ),
				'custom_attributes' => array( 'step' => 'any' ),
			);

			$price = array(
				'title'             => esc_html__( 'Price', 'vpc-upload' ),
				'id'                => 'vpc-upload-component-price',
				'name'              => 'vpc-upload-component[price]',
				'type'              => 'number',
				'default'           => '',
				'desc'              => esc_html__( 'Upload component\'s price.', 'vpc-upload' ),
				'custom_attributes' => array( 'step' => 'any' ),
			);

			$lock_rotation = array(
				'title'     => esc_html__( 'Lock rotation', 'vpc-upload' ),
				'name'      => 'vpc-upload-component[lock-rotation]',
				'default'   => 'no',
				'desc'      => esc_html__( 'Would you like to lock the rotation of the image?', 'vpc-upload' ),
				'type'      => 'radio',
				'options'   => array(
					'no'  => esc_html__( 'No', 'vpc-upload' ),
					'yes' => esc_html__( 'Yes', 'vpc-upload' ),
				),
				'row_class' => 'vpc-upload-component-lock-rotation-type',
			);

			$lock_scaling_x = array(
				'title'     => esc_html__( 'Lock scaling from right or left', 'vpc-upload' ),
				'name'      => 'vpc-upload-component[lock-scaling-x]',
				'default'   => 'no',
				'desc'      => esc_html__( 'Would you like to lock the image scaling from right or left?', 'vpc-upload' ),
				'type'      => 'radio',
				'options'   => array(
					'no'  => esc_html__( 'No', 'vpc-upload' ),
					'yes' => esc_html__( 'Yes', 'vpc-upload' ),
				),
				'row_class' => 'vpc-upload-component-lock-scaling-x-type',
			);

			$lock_scaling_y = array(
				'title'     => esc_html__( 'Lock scaling from top or bottom', 'vpc-upload' ),
				'name'      => 'vpc-upload-component[lock-scaling-y]',
				'default'   => 'no',
				'desc'      => esc_html__( 'Would you like to lock the image scaling from top or bottom?', 'vpc-upload' ),
				'type'      => 'radio',
				'options'   => array(
					'no'  => esc_html__( 'No', 'vpc-upload' ),
					'yes' => esc_html__( 'Yes', 'vpc-upload' ),
				),
				'row_class' => 'vpc-upload-component-lock-scaling-y-type',
			);

			$lock_movement_x = array(
				'title'     => esc_html__( 'Lock the horizontal movement', 'vpc-upload' ),
				'name'      => 'vpc-upload-component[lock-movement-x]',
				'default'   => 'no',
				'desc'      => esc_html__( 'Would you like to lock the horizontal movement of the image?', 'vpc-upload' ),
				'type'      => 'radio',
				'options'   => array(
					'no'  => esc_html__( 'No', 'vpc-upload' ),
					'yes' => esc_html__( 'Yes', 'vpc-upload' ),
				),
				'row_class' => 'vpc-upload-component-lock-movement-x-type',
			);

			$lock_movement_y = array(
				'title'     => esc_html__( 'Lock the vertical movement', 'vpc-upload' ),
				'name'      => 'vpc-upload-component[lock-movement-y]',
				'default'   => 'no',
				'desc'      => esc_html__( 'Would you like to lock the vertical movement of the image?', 'vpc-upload' ),
				'type'      => 'radio',
				'options'   => array(
					'no'  => esc_html__( 'No', 'vpc-upload' ),
					'yes' => esc_html__( 'Yes', 'vpc-upload' ),
				),
				'row_class' => 'vpc-upload-component-lock-movement-y-type',
			);

			$end      = array( 'type' => 'sectionend' );
			$settings = apply_filters(
				'vpc_upload_component_settings',
				array(
					$begin,
					$top,
					$left,
					$rotation,
					$box_width,
					$box_height,
					$price,
					$lock_rotation,
					$lock_scaling_x,
					$lock_scaling_y,
					$lock_movement_x,
					$lock_movement_y,
					$end,
				)
			);
			// Escaping output.
			$allowed_tags = vpc_upload_get_allowed_tags();
			$raw_output   = o_admin_fields( $settings );
			echo wp_kses( $raw_output, $allowed_tags );
			?>
		</div>
		<?php
	}

	/**
	* Function to get all upload components for select2_array technology.
	*
	* @return array
	*/
	public function get_all_upload_components_for_select2_array() {
		$args                         = array(
			'post_type'   => 'vpc-upload-component',
			'post_status' => 'publish',
			'nopaging'    => true,
		);
		$upload_components_post_types = get_posts( $args );
		$none_data                    = array(
			'id'   => '',
			'text' => 'None',
		);
		$upload_components            = array();
		array_push( $upload_components, $none_data );
		foreach ( $upload_components_post_types as $upload_component ) {
			$upload_component_data = array(
				'id'   => $upload_component->ID,
				'text' => $upload_component->post_title,
			);
			array_push( $upload_components, $upload_component_data );
		}
		return $upload_components;
	}

	/**
	* Function to get js variables.
	*/
	public function get_js_variables() {
		$upload_components = $this->get_all_upload_components_for_select2_array();
		?>
		<script>
		var vpc_upload_components = <?php echo wp_json_encode( $upload_components ); ?>;
		</script>
		<?php
	}

	/**
	* Function to save a product's upload component.
	*
	* @param  string $root_id The product upload component ID.
	*/
	public function save_product_upload_component( $root_id ) {
		$meta_key  = 'vpc-upload-component';
		$post_data = wp_unslash( $_POST );
		if ( isset( $post_data[ $meta_key ] ) && ! empty( $post_data[ $meta_key ] ) && ! wp_verify_nonce( $post_data[ $meta_key ] ) ) {
			update_post_meta( $root_id, $meta_key, $post_data[ $meta_key ] );
		}
	}
}
