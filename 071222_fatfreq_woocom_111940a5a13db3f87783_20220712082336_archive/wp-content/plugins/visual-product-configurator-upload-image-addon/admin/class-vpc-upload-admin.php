<?php
/**
* The admin-specific functionality of the plugin.
*
* @link       www.orionorigin.com
* @since      1.0.0
*
* @package    Vpc_Upload
* @subpackage Vpc_Upload/admin
*/

/**
* The admin-specific functionality of the plugin.
*
* Defines the plugin name, version, and two examples hooks for how to
* enqueue the admin-specific stylesheet and JavaScript.
*
* @package    Vpc_Upload
* @subpackage Vpc_Upload/admin
* @author     ORION <support@orionorigin.com>
*/
class Vpc_Upload_Admin {

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
	* @param      string $plugin_name       The name of this plugin.
	* @param      string $version    The version of this plugin.
	*/
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	* Register the stylesheets for the admin area.
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
		if ( is_vpc_admin_screen() ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/vpc-upload-admin.css', array(), VPC_UPLOAD_VERSION, 'all' );
		}

	}

	/**
	* Runs the new version check and upgrade process
	* @return \Vpc_Upload_Updater
	*/
	function get_updater() {
		if(class_exists( 'vpc' )){
			//die(var_dump("dddd"));
			do_action( 'vpc_before_init_updater' );
			require_once( VPC_UPLOAD_DIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'updaters' . DIRECTORY_SEPARATOR . 'class-vpc-upload-updater.php' );
			$updater = new Vpc_Upload_Updater();
			$updater->init();
			require_once( VPC_UPLOAD_DIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'updaters' . DIRECTORY_SEPARATOR . 'class-vpc-upload-updating-manager.php' );
			$updater->setUpdateManager( new Vpc_Upload_Updating_Manager( VPC_UPLOAD_VERSION, $updater->versionUrl(), VPC_UPLOAD_MAIN_FILE ) );
			do_action( 'vpc_after_init_updater' );
			return $updater;
		}
	}

	/**
	* Register the JavaScript for the admin area.
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
		if ( is_vpc_admin_screen() ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/vpc-upload-admin.js', array( 'jquery' ), VPC_UPLOAD_VERSION, false );
			wp_localize_script( $this->plugin_name, 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		}
	}

	function add_global_settings($global_settings){
		$begin = array(
			'type' => 'sectionbegin',
			'id' => 'vpc-upload-container',
			'table' => 'options',
		);

		$end = array('type' => 'sectionend');

		$license_key = array(
			'title' => __( 'Upload image add on license key', 'vpc' ),
			'name' => 'vpc-options[purchase-code-upload-image-add-on]',
			'type' => 'text',
			'desc' => __( 'This is the licence key you received after your purchase.<a href=“https://configuratorsuiteforwp.com/guide/how-to-add-my-license/” target=“_blank”>Learn more</a>. <a href=“https://configuratorsuiteforwp.com/my-account/orders/“ target=“blank”>Where can I find my license key</a>?', 'vpc' ),
				'default' => '',
			);

			array_push($global_settings,$begin);
			array_push($global_settings,$license_key);
			array_push($global_settings,$end);
			return $global_settings;
		}

		/**
		* Function to add new submenu to "Product Builder" menu.
		*/
		public function add_new_submenu() {
			$parent_slug = 'edit.php?post_type=vpc-config';
			add_submenu_page( $parent_slug, esc_html__( 'Upload components', 'vpc-upload' ), esc_html__( 'Upload components', 'vpc-upload' ), 'manage_product_terms', 'edit.php?post_type=vpc-upload-component', false );
		}

		/**
		* Function to add new behavior to the behavior list.
		*
		* @param  array $behaviours List of all the behaviors.
		* @return array $behaviours
		*/
		public function add_upload_behaviour( $behaviours ) {
			$behaviours['upload'] = esc_html__( 'Upload field', 'vpc-upload' );
			return $behaviours;
		}

		/**
		* Add new upload fields to the option fields.
		*
		* @param  array  $options_fields List of the options fields.
		* @param  string $config_id      The ID of a configuration.
		* @return array  $options_fields
		*/
		public function add_vpc_upload_options( $options_fields, $config_id ) {
			$basic_title                = esc_html__( 'Upload component', 'vpc-upload' );
			$options_fields['fields'][] = $this->set_option_upload_field( $basic_title, 'upload-component', 'text', 'vpc-upload-component-selector', 'vpc-upload-component-column' );
			if ( class_exists( 'Vpc_Mva' ) ) {
				$config    = get_post_meta( $config_id, 'vpc-config', true );
				$views_arr = vpc_mva_get_config_views( $config );
				if ( is_array( $views_arr ) ) {
					foreach ( $views_arr as $key => $view ) {
						$title                      = trim( $view ) . ' ' . esc_html__( 'upload component', 'vpc-upload' );
						$name                       = 'view_' . $key . '_upload_component';
						$options_fields['fields'][] = $this->set_option_upload_field( $title, $name, 'text', 'vpc-views-upload-component-selector', 'vpc-views-upload-component-column' );
					}
				}
			}
			return $options_fields;
		}

		/**
		* Function to set an option field.
		*
		* @param string $title Title of the input field.
		* @param string $name Name of the input field.
		* @param string $type Type of the input field.
		* @param string $class Class of the input field.
		* @param string $col_class Class of the input field's column.
		* @return array It contains all the parameters needed to create an input field.
		*/
		public function set_option_upload_field( $title, $name, $type, $class, $col_class ) {
			return array(
				'title'     => $title,
				'name'      => $name,
				'type'      => $type,
				'class'     => $class,
				'col_class' => $col_class,
			);
		}



		/**
		* Function to add a notice to inform the user about the changes that has been made in this version of the plugin and to allow the user to make a migration of olds data to macth the new structure.
		*/
		public function run_db_updates_requirements() {
			// Checks db structure for v3.0.
			$vpc_upload_db_version = get_option( 'vpc-upload-db-version' );
			if ( empty( $vpc_upload_db_version ) ) {
				$vpc_upload_db_version = 0;
			}
			if ( $vpc_upload_db_version < 2 ) {
				$new_upload_db_version = 2;
				?>
				<div class='updated' id='vpc-upload-updater-container'>
					<h3><?php echo esc_html_e( 'Custom upload image add-on database update required.', 'vpc-upload' ); ?></h3>
					<div>
						<ul>
							<li>
								<h3><?php esc_html_e( 'Custom upload image add-on', 'vpc-upload' ); ?></h3>
								<ul>
									<li><?php esc_html_e( "From now on, in order to set the main option's upload image's parameters ( top, left, angle, etc.. ), you'll first have to create an upload component which will be linked to an image upload's option of your choice.", 'vpc-upload' ); ?></li>
									<li><?php esc_html_e( 'If you click on the Run Updater button below, the plugin will create an upload component for all custom upload option and linked them together based on your data.', 'vpc-upload' ); ?></li>
								</ul>
							</li>
						</ul>
						<input type="button" value="<?php echo esc_html_e( 'Run the updater', 'vpc-upload' ); ?>" id="vpc-upload-run-updater" class="button button-primary"/>
						<div class="upload_loading" style="display:none;"></div>
					</div>
				</div>
				<script>
				jQuery( '#vpc-upload-run-updater' ).click( 'click', function () {
					if ( confirm( 'It is strongly recommended that you make a backup your database before proceeding. Are you sure you wish to run the updater now' ) ) {
						jQuery( '.upload_loading' ).show();
						jQuery.post(
							ajax_object.ajax_url,
							{
								action: 'run_upload_updater',
								new_upload_db_version: <?php echo esc_html( $new_upload_db_version ); ?>
							},
							function ( data ) {
								jQuery( '.upload_loading' ).hide();
								jQuery( '#vpc-upload-updater-container' ).html( data );
								jQuery( '#vpc-upload-updater-container' ).addClass( 'done' );
								location.reload();
							}
						);
					}
				} );
				</script>
				<?php
			}
		}

		/**
		* Function to start the migration of the database if the need exists.
		*/
		public function run_updater() {
			ob_start();
			$upload_db_version = ( ! wp_verify_nonce( $upload_db_version ) ) ? get_proper_value( $_POST, 'new_upload_db_version', '' ) : 0;
			if ( 2 === (int) $upload_db_version ) {
				// We migrate the custom upload data.
				$this->migrate_custom_upload_data();
				esc_html_e( 'Done!', 'vpc-upload' );
				update_option( 'vpc-upload-db-version', 2 );
			} else {
				esc_html_e( 'There is no migration callback available for your actual DB version.', 'vpc-upload' );
			}
			$output = ob_get_contents();
			ob_end_clean();
			echo esc_html( $output );
			die();
		}

		/**
		* Function to migrate the custom upload data.
		*
		* @global type $wpdb
		*/
		public function migrate_custom_upload_data() {
			global $wpdb, $db;
			$sql                  = 'select distinct post_id from ' . $wpdb->postmeta . ' where meta_value like "%\"behaviour\";s:6:\"upload\"%"';
			$sql3                 = 'select distinct post_id from ' . $wpdb->postmeta . ' where meta_value like "%\"behaviour\";s:15:\"multiple_upload\"%"';
			$db                   = $wpdb;
			$configs_to_migrate_1 = $db->get_col( $sql );
			$configs_to_migrate_3 = $db->get_col( $sql3 );
			$configs_to_migrate   = array_merge( $configs_to_migrate_1, $configs_to_migrate_3 );
			foreach ( $configs_to_migrate as $key => $config_id ) {
				$config       = get_post_meta( $config_id, 'vpc-config', true );
				$config_title = get_the_title( $config_id );
				if ( ( isset( $config['multi-views'] ) && 'No' === $config['multi-views'] ) || ! isset( $config['multi-views'] ) ) {
					$this->get_new_config_data_for_single_upload( $config_id, $config, $config_title );
				} elseif ( isset( $config['multi-views'] ) && 'Yes' === $config['multi-views'] ) {
					$this->get_new_config_data_for_multiple_upload( $config_id, $config, $config_title );
				}
			}
		}

		/**
		* Function to get the new configuration data.
		*
		* @param   array  $config_id      The configuration's ID.
		* @param   array  $config         The configuration's data.
		* @param   string $config_title   The configuration's title.
		*/
		public function get_new_config_data_for_single_upload( $config_id, $config, $config_title ) {
			if ( isset( $config['components'] ) && ! empty( $config['components'] ) ) {
				$components = $config['components'];
				foreach ( $config['components'] as $i => $component ) {
					$config = $this->get_new_component_data_for_single_upload( $config, $config_title, $component, $i );
				}
			}
			update_post_meta( $config_id, 'vpc-config', $config );
		}


		/**
		* Function to get the component's new data.
		*
		* @param   array  $config         The configuration's data.
		* @param   string $config_title   The configuration's title.
		* @param   array  $component      A component from the configuration.
		* @param   int    $i              Index of the component in the configuration's components list.
		* @return  array
		*/
		public function get_new_component_data_for_single_upload( $config, $config_title, $component, $i ) {
			if ( isset( $component['behaviour'] ) && 'upload' === $component['behaviour'] ) {
				if ( isset( $component['options'] ) && ! empty( $component['options'] ) ) {
					foreach ( $component['options'] as $j => $option ) {
						$title  = $config_title . ' - ' . $component['cname'] . ' - ' . $option['name'];
						$metas  = $this->get_old_custom_upload_option_data( $option['top'], $option['left'], $option['width'], $option['price'] );
						$config = $this->set_new_custom_upload_component_for_single_upload( $config, $option, $title, $i, $j, 'upload-component' );
					}
				}
			}
			return $config;
		}

		/**
		* Function to set a new component upload.
		*
		* @param   array  $config             The configuration's data.
		* @param   array  $metas              New component upload data.
		* @param   string $title              The post title.
		* @param   int    $i                  Index of the component in the configuration's components list.
		* @param   int    $j                  Index of the option in a component's options list.
		* @param   string $upload_field_name  A custom upload field option's name.
		* @return  array
		*/
		public function set_new_custom_upload_component_for_single_upload( $config, $metas, $title, $i, $j, $upload_field_name ) {
			$upload_component_data = array(
				'post_title'  => $title,
				'post_status' => 'publish',
				'post_type'   => 'vpc-upload-component',
			);
			$upload_component_id   = wp_insert_post( $upload_component_data );
			update_post_meta( $upload_component_id, 'vpc-upload-component', $metas );

			$config['components'][ $i ]['behaviour']                           = 'upload';
			$config['components'][ $i ]['options'][ $j ][ $upload_field_name ] = $upload_component_id;
			return $config;
		}

		/**
		* Function to get the upload option's old data.
		*
		* @param   string $top          Upload option's top.
		* @param   string $left         Upload option's left.
		* @param   string $width        Upload option's bloc width.
		* @param   string $price        Upload option's price.
		* @return  array                Upload option's old datas.
		*/
		public function get_old_custom_upload_option_data( $top, $left, $width, $price ) {
			return array(
				'top'   => ( isset( $top ) && '' !== $top ) ? $top : '',
				'left'  => ( isset( $left ) && '' !== $left ) ? $left : '',
				'angle' => '',
				'width' => ( isset( $width ) && '' !== $width ) ? $width : '',
				'price' => $price,
			);
		}

		/**
		* Function to get a new configuration data for a multiple upload behavior.
		*
		* @param   array  $config_id      The configuration's ID.
		* @param   array  $config         The configuration's data.
		* @param   string $config_title   The configuration's title.
		*/
		public function get_new_config_data_for_multiple_upload( $config_id, $config, $config_title ) {
			$config_views = array();
			if ( isset( $config['views'] ) && '' !== $config['views'] ) {
				$config_views = array_unique( explode( PHP_EOL, $config['views'] ) );
			} else {
				$config_views = array_unique( $this->get_all_config_views( $config ) );
			}

			$all_new_datas = array();
			$components    = $config['components'];
			if ( isset( $components ) && ! empty( $components ) ) {
				foreach ( $config['components'] as $i => $component ) {
					if ( isset( $component['behaviour'] ) && 'multiple_upload' === $component['behaviour'] && isset( $component['options'] ) && ! empty( $component['options'] ) ) {
						foreach ( $component['options'] as $j => $option ) {
							$all_views_datas = $this->get_all_views_datas( $config, $option );
							$views           = $all_views_datas['views'];
							$option_views    = $all_views_datas['option_views'];
							$option_views    = array_unique( $option_views );
							$all_new_datas   = $this->get_all_new_datas( $all_new_datas, $views, $option_views, $config_title, $component, $option, $i, $j );
						}
					}
				}
			}
			$this->set_new_config_data( $config_id, $config, $all_new_datas, $config_views );
		}


		/**
		* Function to set a new configuration's datas.
		*
		* @param  string $config_id     A configuration's ID.
		* @param  array  $config        A configuration's datas.
		* @param  array  $all_new_datas All new datas to add to the configuration.
		* @param  array  $config_views  The configuration's views.
		*/
		public function set_new_config_data( $config_id, $config, $all_new_datas, $config_views ) {
			if ( isset( $all_new_datas ) && ! empty( $all_new_datas ) ) {
				foreach ( $all_new_datas as $datas ) {
					if ( isset( $datas['behaviour'] ) && 'multiple_upload' === $datas['behaviour'] ) {
						if ( isset( $datas['title'] ) && '' !== $datas['title'] && isset( $datas['metas'] ) && ! empty( $datas['metas'] ) ) {
							$upload_component_data = array(
								'post_title'  => $datas['title'],
								'post_status' => 'publish',
								'post_type'   => 'vpc-upload-component',
							);

							$upload_component_id = wp_insert_post( $upload_component_data );
							update_post_meta( $upload_component_id, 'vpc-upload-component', $datas['metas'] );
						}
						$config['components'][ $datas['component'] ]['behaviour'] = 'upload';
						if ( isset( $datas['upload_field_view_name'] ) && '' !== $datas['upload_field_view_name'] ) {
							$config['components'][ $datas['component'] ]['options'][ $datas['option'] ][ $datas['upload_field_view_name'] ] = $upload_component_id;
						}
					}
				}
			}
			$config['views'] = implode( PHP_EOL, $config_views );
			update_post_meta( $config_id, 'vpc-config', $config );
		}

		/**
		* Function to get all the views datas.
		*
		* @param  array $config A configuration's datas.
		* @param  array $option An option's datas.
		* @return array         All views datas.
		*/
		public function get_all_views_datas( $config, $option ) {
			$old_views = array_unique( get_option( 'vpc-mva-views' ) );
			if ( isset( $config['views'] ) && '' !== $config['views'] ) {
				$option_views = array_unique( explode( PHP_EOL, $config['views'] ) );
			} else {
				$to_show_views = $this->get_views_to_show( $option );
				$option_views  = array();
				if ( isset( $to_show_views ) && ! empty( $to_show_views ) ) {
					foreach ( $to_show_views as $value ) {
						if ( isset( $old_views ) && ! empty( $old_views ) && isset( $old_views[ $value ] ) ) {
							$option_views[] = $old_views[ $value ];
						}
					}
				}
			}
			return array(
				'views'        => $old_views,
				'option_views' => $option_views,
			);
		}

		/**
		* Function to get all the new datas.
		*
		* @param  array  $all_new_datas All new datas.
		* @param  array  $views         All the views.
		* @param  array  $option_views  The views used in an option.
		* @param  string $config_title  A configuration's title.
		* @param  array  $component     A component's datas.
		* @param  array  $option        An option's datas.
		* @param  int    $i             A component's index in a configuration datas.
		* @param  int    $j             An option's index in a component's datas.
		* @return array                 All new datas.
		*/
		public function get_all_new_datas( $all_new_datas, $views, $option_views, $config_title, $component, $option, $i, $j ) {
			if ( isset( $views ) && ! empty( $views ) && ! empty( $option_views ) ) {
				foreach ( $views as $view_key => $view_value ) {
					if ( in_array( $view_value, $option_views, true ) ) {
						$title                  = $config_title . ' - ' . $component['cname'] . ' - ' . $option['name'] . ' - ' . sanitize_title( $view_value );
						$upload_field_view_name = 'view_' . $view_key . '_upload_component';
						$top                    = $this->get_multi_views_upload_field_data( $option, 'multi_views_image_top', 'top', $view_key );
						$left                   = $this->get_multi_views_upload_field_data( $option, 'multi_views_image_left', 'left', $view_key );
						$width                  = $this->get_multi_views_upload_field_data( $option, 'multi_views_image_width', 'width', $view_key );
						$price                  = $this->get_multi_views_upload_field_data( $option, 'multi_views_image_prices', 'price', $view_key );

						if ( '' === $top && '' === $left && '' === $width && '' === $price ) {
							$all_new_datas[] = array(
								'component' => $i,
								'option'    => $j,
								'behaviour' => $component['behaviour'],
							);
						} else {
							$metas           = array(
								'top'   => ( isset( $top ) && '' !== $top ) ? $top : '',
								'left'  => ( isset( $left ) && '' !== $left ) ? $left : '',
								'angle' => '',
								'width' => ( isset( $width ) && '' !== $width ) ? $width : '',
								'price' => $price,
							);
							$all_new_datas[] = array(
								'component'              => $i,
								'option'                 => $j,
								'upload_field_view_name' => $upload_field_view_name,
								'title'                  => $title,
								'metas'                  => $metas,
								'behaviour'              => $component['behaviour'],
							);
						}
					}
				}
			} else {
				$all_new_datas[] = array(
					'component' => $i,
					'option'    => $j,
					'behaviour' => $component['behaviour'],
				);
			}
			return $all_new_datas;
		}


		/**
		* Function to get the configuration's views.
		*
		* @param  array $config A configuration's datas.
		* @return array         The configuration's views.
		*/
		public function get_all_config_views( $config ) {
			$config_views = array();
			$views        = get_option( 'vpc-mva-views' );
			if ( isset( $config ) && isset( $config['components'] ) && ! empty( $config['components'] ) ) {
				foreach ( $config['components'] as $component_key => $component ) {
					if ( isset( $component['options'] ) && ! empty( $component['options'] ) ) {
						foreach ( $component['options'] as $option_key => $option ) {
							$to_show_views = $this->get_views_to_show( $option );
							if ( isset( $to_show_views ) && ! empty( $to_show_views ) ) {
								foreach ( $to_show_views as $value ) {
									if ( isset( $views ) && ! empty( $views ) && isset( $views[ $value ] ) ) {
										$config_views[] = $views[ $value ];
									}
								}
							}
						}
					}
				}
			}

			return $config_views;
		}

		/**
		* Function to get the right views to show for an option.
		*
		* @param  array $option An option from a component's config.
		* @return array         The list of the views to show.
		*/
		public function get_views_to_show( $option ) {
			$to_show_views = array();
			if ( isset( $option ) && ! empty( $option ) ) {
				foreach ( $option as $opt_key => $opt_value ) {
					if ( strstr( $opt_key, 'multi_views_image_' ) ) {
						foreach ( $opt_value as $key => $value ) {
							if ( ! in_array( $value['text_view_field'], $to_show_views, true ) ) {
								array_push( $to_show_views, $value['text_view_field'] );
							}
						}
					} elseif ( strstr( $opt_key, 'view_options' ) ) {
						foreach ( $opt_value as $key => $value ) {
							if ( ! in_array( $value['view'], $to_show_views, true ) ) {
								array_push( $to_show_views, $value['view'] );
							}
						}
					}
				}
			}
			return $to_show_views;
		}

		/**
		* Function to retrieve an old multiviews upload field value.
		*
		* @param array  $option    A component's option data.
		* @param string $name      An option's field's name.
		* @param string $value     Reseached data name.
		* @param string $view_key  The concern view's key.
		* @return array            A view's custom upload field data.
		*/
		public function get_multi_views_upload_field_data( $option, $name, $value, $view_key ) {
			$upload_field_data             = '';
			$multi_views_upload_field_data = get_proper_value( $option, $name, array() );
			if ( is_array( $multi_views_upload_field_data ) ) {
				foreach ( $multi_views_upload_field_data as $field_data_value ) {
					if ( isset( $field_data_value['text_view_field'] ) && $field_data_value['text_view_field'] === (string) $view_key ) {
						$upload_field_data = $field_data_value[ $value ];
					}
				}
			}
			return $upload_field_data;
		}


	}
