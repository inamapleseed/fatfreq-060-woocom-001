<?php
/**
* The public-facing functionality of the plugin.
*
* @link       www.orionorigin.com
* @since      1.0.0
*
* @package    Vpc_Upload
* @subpackage Vpc_Upload/public
*/

/**
* The public-facing functionality of the plugin.
*
* Defines the plugin name, version, and two examples hooks for how to
* enqueue the public-facing stylesheet and JavaScript.
*
* @package    Vpc_Upload
* @subpackage Vpc_Upload/public
* @author     ORION <support@orionorigin.com>
*/
class Vpc_Upload_Public {

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
	* @param      string $plugin_name       The name of the plugin.
	* @param      string $version    The version of this plugin.
	*/
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	* Register the stylesheets for the public-facing side of the site.
	*
	* @since    1.0.0
	*/
	public function enqueue_styles() {

		/**
		* This function is provided for demonstration purposes only.
		*
		* An instance of this class should be passed to the run() function
		* defined in Vpc_Upload_Loader as all of the hooks are defined
		* in that particular class.
		*
		* The Vpc_Upload_Loader will then create the relationship
		* between the defined hooks and the functions defined in this
		* class.
		*/
		wp_enqueue_style( 'vpc-upload-public-css', plugin_dir_url( __FILE__ ) . 'css/vpc-upload-public.css', array(), VPC_UPLOAD_VERSION, 'all' );
	}

	/**
	* Register the JavaScript for the public-facing side of the site.
	*
	* @since    1.0.0
	*/
	public function enqueue_scripts() {

		/**
		* This function is provided for demonstration purposes only.
		*
		* An instance of this class should be passed to the run() function
		* defined in Vpc_Upload_Loader as all of the hooks are defined
		* in that particular class.
		*
		* The Vpc_Upload_Loader will then create the relationship
		* between the defined hooks and the functions defined in this
		* class.
		*/
		wp_enqueue_script( 'vpc-upload-public-js', plugin_dir_url( __FILE__ ) . 'js/vpc-upload-public.js', array( 'jquery' ), VPC_UPLOAD_VERSION, false );
	}

	/**
	* Function to set the custom upload field.
	*
	* @param array  $option         Option's datas.
	* @param string $o_image        Option's image.
	* @param string $price          Option's price.
	* @param string $option_id      Option's ID.
	* @param array  $component      Component's data.
	* @param string $skin_name      Skin's name.
	* @param array  $config_to_load Saved configuration's datas.
	* @param array  $config         Configuration's datas.
	*/
	public function set_upload_options( $option, $o_image, $price, $option_id, $component, $skin_name, $config_to_load, $config ) {
		if (!get_option('vpc-upload-image-add-on-license-key')) {
			echo "<h2 class='error_msg'>You have not activated your license yet. Please, activate it in order to use Custom upload add on.</h2>";
		} else {
			$multi_views = vpc_upload_if_multi_views_active( $config );
			vpc_upload_create_option_field( $option, $price, $config_to_load, $multi_views, $component['behaviour'], $component, $config );

		}
	}

	/**
	* Function to set up the custom upload container on the preview.
	*
	* @param  string $preview_html The preview's html code.
	* @param  string $prod_id      The product's ID.
	* @param  string $config_id    The configuration's ID.
	* @return string
	*/
	public function add_file_on_preview( $preview_html, $prod_id, $config_id ) {
		$config = $this->get_vpc_get_config_data( $prod_id );
		if ( isset( $config['multi-views'] ) && 'No' === $config['multi-views'] || ! isset( $config['multi-views'] ) ) {
			if ( class_exists( 'Vpc_Cta_Public' ) ) {
				$preview_html = '<div class="vpc-global-preview">'
				. '<div id="upload_panel" class=""></div>'
				. '<div id="text_panel" class=""></div>'
				. '<div id="vpc-preview"></div>'
				. '</div>';
			} else {
				$preview_html = '<div class="vpc-global-preview">'
				. '<div id="upload_panel" class=""></div>'
				. '<div id="vpc-preview"></div>'
				. '</div>';
			}
		}
		return $preview_html;
	}

	/**
	* Function to calculate the total price of a cart item.
	*
	* @param  string $total_price The cart item's total price.
	* @param  string $product_id  The product ID.
	* @param  array  $config      A configuration's data.
	* @param  array  $cart_item   A cart item's data.
	* @return string
	*/
	public function add_file_price( $total_price, $product_id, $config, $cart_item ) {
		$a_price = 0;
		if (isset($cart_item) && isset($cart_item['visual-product-configuration'])) {
			$recap = $cart_item['visual-product-configuration'];
			if (isset($recap['canvas_data']) && '' !== $recap['canvas_data']) {
				$canvas_data = json_decode(stripslashes($recap['canvas_data']));
				foreach ($canvas_data as $key => $canvas) {
					foreach ($canvas as $key => $datas) {
						if (isset($datas->type) && 'image' === $datas->type && isset($datas->src) && '' !== $datas->src) {
							$upload_component_data = vpc_upload_get_component_data($datas->upload_component_id);
							$a_price += (float) $upload_component_data['price'];
						}
					}
				}
			}
		}
		$a_price = apply_filters( 'vpc_upload.add_file_price', $a_price, $product_id, $config, $cart_item);
		$total_price += $a_price;
		return $total_price;
	}

	/**
	* Function to add new datas to $vpc_settings.
	*
	* @param array $datas All Visual Product Configurator datas.
	* @return array
	*/
	public function add_new_data( $datas ) {
		$datas['upload_component_data'] = array();
		$datas['upload_component_all_data'] = array();
		return $datas;
	}

	/**
	* Function to filter the recap.
	*
	* @param  array   $recap  Dirty recap's data.
	* @param  array   $config A configuration's datas.
	* @param  boolean $ico    Show an icon or not.
	* @return array           Cleaned recap's data.
	*/
	public function filter_recap( $recap, $config, $ico ) {
		$multi_views = vpc_upload_if_multi_views_active( $config );
		if ( isset( $recap['action'] ) ) {
			unset( $recap['action'] );
		}
		if ( isset( $recap['nonce'] ) ) {
			unset( $recap['nonce'] );
		}
		if ( isset( $recap[ 'canvas_data' ] ) ) {
			unset( $recap[ 'canvas_data' ] );
		}

		if ( isset( $config['components'] ) && ! empty( $config['components'] ) ) {
			foreach ( $config['components'] as $component_key => $component ) {
				if ( ! $multi_views && isset( $component['behaviour'] ) && 'upload' === $component['behaviour'] ) {
					$recap = $this->get_new_recap_for_single_view( $recap, $component['options'] );
				} elseif ( $multi_views && isset( $component['behaviour'] ) && 'upload' === $component['behaviour'] ) {
					$recap = $this->get_new_recap_for_multi_views( $recap, $component['options'], $config );
				}
			}
		}

		return $recap;
	}

	/**
	* Function to get the new recap for the single view.
	*
	* @param  array $recap   Dirty recap's data.
	* @param  array $options A list of options.
	* @return array          Cleaned recap.
	*/
	public function get_new_recap_for_single_view( $recap, $options ) {
		foreach ( $options as $key => $option ) {
			if ( isset( $recap[ $option['name'] ] ) ) {
				unset( $recap[ $option['name'] ] );
			}
		}
		return $recap;
	}

	/**
	* Function to get the new recap for multiple views.
	*
	* @param  array $recap   Dirty recap's data.
	* @param  array $options A list of options.
	* @param  array $config  A configuration's datas.
	* @return array          Cleaned recap.
	*/
	public function get_new_recap_for_multi_views( $recap, $options, $config ) {
		if ( class_exists( 'Vpc_Mva' ) ) {
			$views = vpc_mva_get_config_views( $config );
			foreach ( $options as $key => $option ) {
				if ( isset( $views ) && ! empty( $views ) ) {
					foreach ( $views as $key => $view ) {
						$field_name        = strtoupper( $view );
						$option_name_field = $option['name'] . ' ' . trim( $field_name );
						if ( isset( $recap[ $option_name_field ] ) ) {
							unset( $recap[ $option_name_field ] );
						}
					}
				}
			}
		}
		return $recap;
	}


	public function get_new_formatted_config_data($output, $recap, $config, $show_icons)
	{
		$new_output = "<div class='vpc-cart-options-container'>";
		if (isset($recap[ 'canvas_data' ])) {
			$canvases_datas = json_decode(stripslashes($recap[ 'canvas_data' ]));
			if (isset($canvases_datas) && !empty($canvases_datas)) {
				foreach ($canvases_datas as $canvas_key => $canvas) {
					$new_output = vpc_upload_get_canvas_datas_recap( $new_output, $canvas );
				}
			}
		}
		$new_output .= '</div>';
		$new_output = apply_filters( 'get_uploads_new_formatted_config_data', $new_output, $output, $recap, $config, $show_icons );
		$output .= $new_output;
		return $output;
	}



	/**
	* Function to get the configuration's datas using the product's ID.
	*
	* @param  string $prod_id A product's ID.
	* @return array           A configuration's datas.
	*/
	private function get_vpc_get_config_data( $prod_id ) {
		$ids         = get_product_root_and_variations_ids( $prod_id );
		$config_meta = get_post_meta( $ids['product-id'], 'vpc-config', true );
		$configs     = get_proper_value( $config_meta, $prod_id, array() );
		$config_id   = get_proper_value( $configs, 'config-id', false );
		$config      = get_post_meta( $config_id, 'vpc-config', true );
		return $config;
	}


	/**
	* Function to save the custom upload image on the server and get the new image src.
	*/
	public function vpc_upload_save_image() {
		if ( isset( $_POST['data_url'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['data_url'] ) ) ) ) {
			$image_data = sanitize_text_field( wp_unslash( $_POST['data_url'] ) );
		}

		if ( isset( $_POST['extension'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['extension'] ) ) ) ) {
			$extension = sanitize_text_field( wp_unslash( $_POST['extension'] ) );
		}
		$upload_dir      = wp_upload_dir();
		$generation_path = $upload_dir['basedir'] . '/VPC/UPLOADS';
		$generation_url  = $upload_dir['baseurl'] . '/VPC/UPLOADS';
		$final_file_url  = '';
		if ( wp_mkdir_p( $generation_path ) ) {
			$id              = uniqid();
			$final_file_path = $generation_path . '/upload_' . $id . '.' . $extension;
			$final_file_url  = $generation_url . '/upload_' . $id . '.' . $extension;
			$unencoded       = base64_decode( $image_data );
			$fp              = fopen( $final_file_path, 'w' );
			fwrite( $fp, $unencoded );
			fclose( $fp );
		}
		echo esc_html( $final_file_url );
		die();
	}
}
