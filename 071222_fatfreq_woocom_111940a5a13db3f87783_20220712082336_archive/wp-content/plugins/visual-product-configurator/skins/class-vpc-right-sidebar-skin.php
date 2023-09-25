<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class-vpc-default-skin
 *
 * @author HL
 */
class VPC_Right_Sidebar_Skin {

    public $product;
    public $product_id;
    public $settings;
    public $config;

    public function __construct( $product_id = false, $config = false ) {
	if ( $product_id ) {
	    if ( vpc_woocommerce_version_check() ) {
		$this->product = new WC_Product( $product_id );
	    } else {
		$this->product = wc_get_product( $product_id );
	    }
	    $this->product_id = $product_id;

	    $this->config = get_product_config( $product_id );
	} elseif ( $config ) {
	    $this->config = new VPC_Config( $config );
	}
    }

    public function display( $config_to_load = array() ) {

	vpc_skins_enqueue_styles_scripts( 'VPC_Right_Sidebar_Skin' );
	ob_start();

	if ( ! $this->config || empty( $this->config ) ) {
	    return __( 'No valid configuration is linked to this product. Please review.', 'vpc' );
	}

	$skin_name = get_class( $this );

	$config = $this->config->settings;

	$options_style		 = '';
	$components_aspect	 = get_proper_value( $config, 'components-aspect', 'closed' );
	if ( 'closed' == $components_aspect ) {
	    $options_style = 'display: none';
	}
	$product_id = '';
	if ( class_exists( 'Woocommerce' ) ) {
	    if ( vpc_woocommerce_version_check() ) {
		$product_id = $this->product->id;
	    } else {
		$product_id = $this->product->get_id();
	    }
	    do_action( 'vpc_before_container', $config, $product_id, $this->config->id );
	}
	$conf_desc	 = get_configurator_description( $config );
	$conf_desc	 = apply_filters( 'vpc_configurator_description', $conf_desc, $config, $product_id );
	?>
	<div id="vpc-container" class="o-wrap <?php echo esc_attr( $skin_name ); ?>" data-curr="<?php echo ( class_exists( 'Woocommerce' ) ) ? html_entity_decode( htmlentities( get_woocommerce_currency_symbol() ) ) : ''; ?>">
	    <?php
	    vpc_get_configurator_loader();
	    do_action( 'vpc_before_inside_container', $config, $product_id, $this->config->id );
	    ?>
	    <div class="o-col conf_desc"><?php echo html_entity_decode( htmlentities( $conf_desc ) ); ?></div>

	    <div class="o-col xl-2-3 lg-2-3 md-1-1 sm-1-1" id="vpc-preview-wrap">

		<div class="default-right-skin">
		    <?php if ( class_exists( 'Woocommerce' ) ) vpc_get_price_container( $this->product->get_id() ); ?>
		</div>
		<?php
		$preview_html	 = '<div id="vpc-preview"></div>';
		$preview_html	 = apply_filters( 'vpc_preview_container', $preview_html, $product_id, $this->config->id );
		echo html_entity_decode( htmlentities( $preview_html ) );
		?>

		<?php
		do_action( 'vpc_after_preview_area', $config, $product_id, $this->config->id );
		?>
	    </div>

	    <div class="o-col xl-1-3 lg-1-3 md-1-1 sm-1-1" id="vpc-components">
		<?php
		do_action( 'vpc_before_components', $config, $product_id );
		if ( isset( $config[ 'components' ] ) ) {
		    foreach ( $config[ 'components' ] as $component_index => $component ) {
			$this->get_components_block( $component, $options_style, $config, $config_to_load );
		    }
		}
		do_action( 'vpc_after_components', $config, $product_id, $config_to_load );
		?>
	    </div>
	    <div id="vpc-bottom-limit"></div>
	    <div id="vpc-form-builder-wrap">
		<?php
		if ( class_exists( 'Ofb' ) ) {
		    if ( isset( $config[ 'ofb_id' ] ) ) {
			$form_builder_id = $config[ 'ofb_id' ];
			$form		 = display_form_builder( $form_builder_id, $config_to_load );
			echo $form;
		    }
		}
		?>
	    </div>
	    <div class="vpc-action-buttons o-col xl-1-1 o-left-offset-2-3">
		<div class="o-col xl-1-1">

		    <?php echo vpc_get_action_buttons( $this->product_id ); ?>
		    <?php
		    if ( class_exists( 'Vpc_Sfla' ) ) {
			$Save_class = new Vpc_Sfla_Public( false, VPC_SFLA_VERSION );
			echo $Save_class->get_sfla_buttons( $config, $product_id );
		    }
		    if ( class_exists( 'Vpc_Ssa' ) ) {
			$Save = new Vpc_Ssa_Public( false, VPC_SSA_VERSION );
			echo $Save->get_ssa_buttons( $product_id );
		    }
		    ?>
		</div>
	    </div>
	    <div>
		<?php
		if ( class_exists( 'Vpc_Sfla' ) ) {
		    $Save_class = new Vpc_Sfla_Public( false, VPC_SFLA_VERSION );
		    echo $Save_class->get_all_configs( $config, $this->product->get_id() );
		}
		?>
	    </div>
	    <div id="debug"></div>
	    <div class="vpc-debug">
		<?php do_action( 'vpc_container_end', $config, $this->product_id ); ?>
	    </div>
	</div>

	<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
    }

    private function get_components_block( $component, $options_style, $config, $config_to_load = array() ) {
	global $vpc_settings, $WOOCS;
	$skin_name	 = get_class( $this );
	$c_icon		 = '';
	$options	 = '';
	if ( isset( $component[ 'options' ] ) ) {
	    $options = $component[ 'options' ];
	}
	if ( $options ) {
	    $options = sort_options_by_group( $options );
	}
	$component_id	 = 'component_' . sanitize_title( str_replace( ' ', '', $component[ 'cname' ] ) );
	$component_id	 = get_proper_value( $component, 'component_id', sanitize_title( $component_id ) );

	// We make sure we have an usable behaviour
	$handlable_behaviours = vpc_get_behaviours();
	// var_dump($handlable_behaviours);
	if ( ! isset( $handlable_behaviours[ $component[ 'behaviour' ] ] ) ) {
	    $component[ 'behaviour' ] = 'radio';
	}

	if ( $component[ 'cimage' ] ) {
	    $img_url = o_get_proper_image_url( $component[ 'cimage' ] );
	}
	if ( isset( $img_url ) ) {
	    $image_resize	 = aq_resize( $img_url, 60, 60, true );
	    $c_icon		 = "<img src='" . $image_resize . "' alt=''>";
	    if ( ! $c_icon ) {
		$c_icon = $img_url;
	    }
	}
	// $c_icon = "<img src='" . o_get_proper_image_url($component["cimage"]) . "'>";

	$components_attributes_string	 = apply_filters( 'vpc_component_attributes', "data-component_id = '$component_id'", $this->product_id, $component );
	?>
	<div id = '<?php echo $component_id; ?>' class="vpc-component" <?php echo $components_attributes_string; ?>>

	    <div class="vpc-component-header">
		<?php
		echo "$c_icon<span style='display: inline-block;'><span>" . $component[ 'cname' ] . '</span>';
		?>

		<span class="vpc-selected txt"><?php esc_attr_e( 'none', 'vpc' ); ?></span></span>
		<span class="vpc-selected-icon"><img width="24" src="" alt="..."></span>

	    </div>
	    <div class="vpc-options" style="<?php echo $options_style; ?>">
		<?php
		do_action( 'vpc_' . $component[ 'behaviour' ] . '_begin', $component, $skin_name );
		$current_group			 = '';
		if ( ! is_array( $options ) || empty( $options ) ) {
		    esc_attr_e( 'No option detected for the component. You need at least one option per component for the configuration to work properly.', 'vpc' );
		} else {
		    $product_id	 = $this->product_id; // get_query_var("vpc-pid", false);
		    // WAD compatibility
		    $discount_rate	 = 0;
		    if ( function_exists( 'vpc_get_discount_rate' ) ) {
			$discount_rate = vpc_get_discount_rate( $product_id );
		    }

		    foreach ( $options as $option_index => $option ) {
			/* if ( '' == $option['name'] ) {
			  if ( intval( $option_index ) == intval( count( $options ) - 1 ) ) {
			  echo '</div>';
			  }
			  continue;
			  } */

			if ( ( $current_group != $option[ 'group' ] ) || ( $option_index == 0 ) ) {
			    if ( $option_index !== 0 ) {
				if ( 'dropdown' == $component[ 'behaviour' ] ) {
				    echo '</select>';
				}
				echo '</div>';
			    }
			    echo "<div class='vpc-group'><div class='vpc-group-name'>" . $option[ 'group' ] . '</div>'; // ."</div>";// . "<br>";
			    if ( $component[ 'behaviour' ] == 'dropdown' ) {
				if ( $option[ 'group' ] !== '' )
				    $selectName	 = $component[ 'cname' ] . " " . $option[ 'group' ];
				else
				    $selectName	 = $component[ 'cname' ];
				?>
				<select name="<?php echo $selectName; ?>" id="<?php echo $component_id; ?>">
				    <option value=''>Choose an option ...</option>
				    <?php
				}
			    }
			    $current_group	 = $option[ 'group' ];
			    $opt_img_id	 = get_proper_value( $option, 'image' );
			    $o_image	 = o_get_proper_image_url( $opt_img_id );
			    $opt_icon_id	 = get_proper_value( $option, 'icon' );
			    $o_img_url	 = o_get_proper_image_url( $opt_icon_id );
			    $o_icon		 = aq_resize( $o_img_url, get_proper_value( $vpc_settings, 'default-icon-width', 25 ), get_proper_value( $vpc_settings, 'default-icon-height', 25 ), true );
			    if ( ! $o_icon ) {
				$o_icon = $o_img_url;
			    }
			    $o_name		 = $component[ 'cname' ];
			    $name_tooltip	 = get_proper_value( $vpc_settings, 'view-name' );
			    $price_tooltip	 = get_proper_value( $vpc_settings, 'view-price' );

			    // $input_id = uniqid();
			    // $label_id = "cb$input_id";

			    $checked = '';
			    if ( $config_to_load && isset( $config_to_load[ $component[ 'cname' ] ] ) ) {
				$saved_options = $config_to_load[ $component[ 'cname' ] ];
				if ( ( is_array( $saved_options ) && in_array( $option[ 'name' ], $saved_options ) ) || ( $option[ 'name' ] == $saved_options )
				) {
				    $checked = "checked='checked'";
				}
			    } elseif ( isset( $option[ 'default' ] ) && $option[ 'default' ] == 1 ) {
				$checked = "checked='checked' data-default='1'";
			    }

			    $price = get_proper_value( $option, 'price', 0 );
			    if ( strpos( $price, ',' ) ) {
				$price = floatval( str_replace( ',', '.', $price ) );
			    }
			    if ( '' == $price ) {
				$price = 0;
			    }
			    $price			 = $price - $price * $discount_rate;
			    $price			 = vpc_apply_taxes_on_price_if_needed( $price, $this->product );
			    $linked_product		 = get_proper_value( $option, 'product', false );
			    $formated_price_raw	 = 0;
			    if ( $linked_product ) {
				$price = 0;
				if ( class_exists( 'Woocommerce' ) ) {
				    if ( vpc_woocommerce_version_check() ) {
					$product = new WC_Product( $linked_product );
				    } else {
					$product = wc_get_product( $linked_product );
				    }
				    if ( ! $product )
					continue;
				    $skip_option = apply_filters( 'vpc_skip_option', true, $option );
				    if ( $skip_option ) {
					if ( ! $product->is_purchasable() || ( $product->managing_stock() && ! $product->is_in_stock() ) ) {
					    if ( $option_index == count( $options ) - 1 ) {
						echo '</div>';
					    }
					    continue;
					}
				    } else {
					do_action( 'vpc_action_to_skip_option', $option );
				    }

				    $price	 = $product->get_price();
				    $price	 = vpc_apply_taxes_on_price_if_needed( $price, $product );
				}
			    }

			    if ( $WOOCS ) {
				$currencies	 = $WOOCS->get_currencies();
				$price		 = $price * $currencies[ $WOOCS->current_currency ][ 'rate' ];
			    }

			    $price			 = apply_filters( 'vpc_options_price', $price, $option, $component, $this );
			    if ( class_exists( 'Woocommerce' ) )
				$formated_price_raw	 = wc_price( $price );

			    if ( apply_filters( 'vpc_option_visibility', 1, $option ) != 1 ) {
				if ( $option_index == count( $options ) - 1 ) {
				    echo '</div>';
				}
				continue;
			    }

			    $formated_price	 = strip_tags( $formated_price_raw );
			    $option_id	 = 'component_' . sanitize_title( str_replace( ' ', '', $component[ 'cname' ] ) ) . '_group_' . sanitize_title( str_replace( ' ', '', $option[ 'group' ] ) ) . '_option_' . sanitize_title( str_replace( ' ', '', $option[ 'name' ] ) );
			    $option_id	 = get_proper_value( $option, 'option_id', $option_id );
			    $comp_index	 = get_proper_value( $component, 'c_index', 0 );
			    if ( empty( $comp_index ) ) {
				$comp_index = 0;
			    }
			    $customs_datas	 = " data-index='$comp_index'";
			    $customs_datas	 = apply_filters( 'vpc_options_customs_datas', $customs_datas, $option, $component, $config );
			    switch ( $component[ 'behaviour' ] ) {
				case 'radio':
				case 'checkbox':
				    $input_type = 'radio';
				    if ( $component[ 'behaviour' ] == 'checkbox' ) {
					$o_name		 .= '[]';
					$input_type	 = 'checkbox';
				    }

				    if ( $name_tooltip == 'Yes' ) {
					$tooltip = $option[ 'name' ];
				    } else {
					$tooltip = '';
				    }
				    if ( $price_tooltip == 'Yes' ) {
					if ( strpos( $formated_price, '-' ) !== false || strpos( $formated_price, '+' ) !== false ) {
					    $tooltip .= " $formated_price";
					} else {
					    $tooltip .= " +$formated_price";
					}
				    }
				    if ( ! empty( $option[ 'desc' ] ) ) {
					$tooltip .= ' (' . $option[ 'desc' ] . ')';
				    }

				    $label_id	 = "cb$option_id";
				    $tooltip	 = apply_filters( 'vpc_options_tooltip', $tooltip, $price, $option, $component );
				    ?>
				    <div class="vpc-single-option-wrap" data-oid="<?php echo $option_id; ?>" >
					<input id="<?php echo $option_id; ?>" type="<?php echo $input_type; ?>" name="<?php echo esc_attr( $o_name ); ?>" value="<?php echo esc_attr( $option[ 'name' ] ); ?>"  data-component-id="<?php echo $component[ 'component_id' ]; ?>" data-img="<?php echo $o_image; ?>" data-icon="<?php echo $o_icon; ?>" data-price="<?php echo $price; ?>" data-product="<?php echo isset( $option[ 'product' ] ) ? $option[ 'product' ] : ''; ?>" data-oid="<?php echo $option_id; ?>" <?php echo $checked . ' ' . $customs_datas; ?>>
					<label id="<?php echo $label_id; ?>" for="<?php echo $option_id; ?>" <?php echo $tooltip;?> class="custom"></label>
					<style>
					    #<?php echo $label_id; ?>:before
					    {
						background-image: url("<?php echo $o_icon; ?>");
						line-height: <?php echo get_proper_value( $vpc_settings, 'default-icon-height', 25 ) . 'px'; ?>;
					    }
					    #<?php echo $label_id; ?> ,#<?php echo $label_id; ?>:before
					    {
						width: <?php echo get_proper_value( $vpc_settings, 'default-icon-width', 25 ) . 'px'; ?>;
						height:  <?php echo get_proper_value( $vpc_settings, 'default-icon-height', 25 ) . 'px'; ?>;
					    }
					</style>
					<?php
					do_action( 'vpc_before_end_' . $component[ 'behaviour' ], $option, $o_image, $price, $option_id, $component, $skin_name, $config_to_load, $this->config->settings );
					?>
				    </div>
				    <?php
				    break;

				case 'dropdown':
				    $selected = '';
				    if ( $config_to_load && isset( $config_to_load[ $component[ 'cname' ] ] ) ) {
					$saved_options = $config_to_load[ $component[ 'cname' ] ];
					if ( ( is_array( $saved_options ) && in_array( $option[ 'name' ], $saved_options ) ) || ( $option[ 'name' ] == $saved_options )
					) {
					    $selected = 'selected';
					}
				    } elseif ( isset( $option[ 'default' ] ) && $option[ 'default' ] == 1 ) {
					$selected = 'selected';
				    }
				    ?>
				    <option id="<?php echo $option_id; ?>" value="<?php echo $option[ 'name' ]; ?>" data-component-id="<?php echo $component[ 'component_id' ]; ?>" data-img="<?php echo $o_image; ?>" data-icon="<?php echo $o_icon; ?>" data-price="<?php echo $price; ?>" data-product="<?php echo isset( $option[ 'product' ] ) ? $option[ 'product' ] : ''; ?>" data-oid="<?php echo $option_id; ?>" <?php echo $selected . ' ' . $customs_datas; ?>>
					<?php echo $option[ 'name' ]; ?>
				    </option>
				    <?php
				    break;

				default:
				    // do_action('vpc_'.$skin_name.'_' . $component["behaviour"], $component);
				    do_action( 'vpc_' . $component[ 'behaviour' ], $option, $o_image, $price, $option_id, $component, $skin_name, $config_to_load, $this->config->settings );
				    break;
			    }
			    do_action( 'vpc_each_' . $component[ 'behaviour' ] . '_end', $option, $o_image, $price, $option_id, $component, $skin_name, $config_to_load, $this->config, $option_index );

			    if ( $option_index == count( $options ) - 1 ) {
				if ( $component[ 'behaviour' ] == 'dropdown' ) {
				    echo '</select>';
				}
				echo '</div>';
			    }
			    $current_group = $option[ 'group' ];
			}
		    }

		    do_action( 'vpc_' . $component[ 'behaviour' ] . '_end', $component, $this->config, $skin_name );
		    ?>
	    </div>
	</div>
	<?php
    }

}
