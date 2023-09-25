<?php
/**
* The file that defines all the global functions
*
* @link       https://www.orionorigin.com/
* @since      1.0.0
*
* @package    Vpc_Upload
* @subpackage Vpc_Upload/includes
*/

/**
* Function to load allowed lsd tags in order to properly escape outputs.
*
* @return array
*/
function vpc_upload_get_allowed_tags() {
	$allowed_tags = wp_kses_allowed_html( 'post' );

	$allowed_tags['li'] = array(
		'id'             => array(),
		'name'           => array(),
		'class'          => array(),
		'value'          => array(),
		'style'          => array(),
		'data-ttf'       => array(),
		'data-fonturl'   => array(),
		'data-fontname'  => array(),
		'data-color'     => array(),
		'data-minwidth'  => array(),
		'data-minheight' => array(),
	);

	$allowed_tags['br'] = array();

	$allowed_tags['input'] = array(
		'type'           => array(),
		'id'             => array(),
		'name'           => array(),
		'style'          => array(),
		'class'          => array(),
		'value'          => array(),
		'min'            => array(),
		'max'            => array(),
		'row_class'      => array(),
		'selected'       => array(),
		'checked'        => array(),
		'readonly'       => array(),
		'placeholder'    => array(),
		'step'           => array(),
		'data-fonturl'   => array(),
		'data-fontname'  => array(),
		'data-minwidth'  => array(),
		'data-minheight' => array(),
		'readonly'       => array(),
		'autocomplete'   => array(),
		'autocorrect'    => array(),
		'autocapitalize' => array(),
		'spellcheck'     => array(),
	);

	$allowed_tags['div'] = array(
		'id'                   => array(),
		'name'                 => array(),
		'style'                => array(),
		'data-id'              => array(),
		'class'                => array(),
		'row_class'            => array(),
		'role'                 => array(),
		'aria-labelledby'      => array(),
		'aria-hidden'          => array(),
		'role'                 => array(),
		'data-fonturl'         => array(),
		'data-minwidth'        => array(),
		'data-minheight'       => array(),
		'data-tooltip-content' => array(),
		'tabindex'             => array(),
		'style'                => array(),
		'data-tooltip-title'   => array(),
		'data-placement'       => array(),
	);

	$allowed_tags['button'] = array(
		'id'           => array(),
		'name'         => array(),
		'class'        => array(),
		'value'        => array(),
		'data-tpl'     => array(),
		'style'        => array(),
		'data-id'      => array(),
		'data-dismiss' => array(),
		'aria-hidden'  => array(),
	);

	$allowed_tags['body'] = array(
		'id'                 => array(),
		'name'               => array(),
		'class'              => array(),
		'data-gr-c-s-loaded' => array(),
	);

	$allowed_tags['a']      = array(
		'id'               => array(),
		'name'             => array(),
		'class'            => array(),
		'data-tpl'         => array(),
		'href'             => array(),
		'data-toggle'      => array(),
		'data-target'      => array(),
		'data-modalid'     => array(),
		'target'           => array(),
		'data-tpl'         => array(),
		'data-group'       => array(),
		'data-slide-index' => array(),
		'download'         => array(),
	);
	$allowed_tags['select'] = array(
		'id'       => array(),
		'name'     => array(),
		'class'    => array(),
		'data-tpl' => array(),
		'style'    => array(),
		'multiple' => array(),
		'tabindex' => array(),
	);
	$allowed_tags['option'] = array(
		'id'       => array(),
		'name'     => array(),
		'class'    => array(),
		'value'    => array(),
		'style'    => array(),
		'selected' => array(),
		'tabindex' => array(),
	);

	$allowed_tags['span'] = array(
		'id'                 => array(),
		'name'               => array(),
		'class'              => array(),
		'value'              => array(),
		'style'              => array(),
		'data-tooltip-title' => array(),
		'data-placement'     => array(),
	);

	$allowed_tags['style'] = array();

	$allowed_tags['textarea'] = array(
		'autocomplete'   => array(),
		'autocorrect'    => array(),
		'autocapitalize' => array(),
		'spellcheck'     => array(),
		'class'          => array(),
	);
	return $allowed_tags;
}

/**
* Function to check if multiviews is activated or not.
*
* @param array $config A configuration's datas.
* @return boolean
*/
function vpc_upload_if_multi_views_active( $config ) {
	$multi_views = false;
	if ( isset( $config ) && ! empty( $config ) ) {
		if ( isset( $config['multi-views'] ) && 'Yes' === $config['multi-views'] ) {
			$multi_views = true;
		}
	}
	return $multi_views;
}

/**
* Function to get the active views when multiwiews is installed and activated.
*
* @param array $config A configuration's datas.
* @return array
*/
function vpc_upload_get_active_views( $config ) {
	$active_views = array();
	if ( true === ( isset( $config['multi-views'] ) && filter_var( $config['multi-views'], FILTER_VALIDATE_BOOLEAN ) ) ) {
		$views_arr = vpc_mva_get_config_views( $config );
		foreach ( $config['components'] as $components ) {
			if ( is_array( $components['options'] ) ) {
				foreach ( $components['options'] as $option ) {
					foreach ( $views_arr as $key => $view ) {
						if ( isset( $option[ 'view_' . $key ] ) && ! empty( $option[ 'view_' . $key ] ) ) {
							if ( ! in_array( sanitize_title( trim( $view ) ), $active_views, true ) ) {
								$active_views[$key] = sanitize_title( trim( $view ) );
							}
						}
					}
				}
			}
		}
	}
	return $active_views;
}

/**
* Function to create an upload option field.
*
* @param array   $option              An option's datas.
* @param float   $price               An option's price.
* @param array   $config_to_load      A configuration old selected options datas.
* @param boolean $multi_views         Tells if multiviews is activated or not.
* @param string  $component_behaviour The value of a component's behavior.
* @param array   $component           A component's datas.
* @param array   $config              A configuration's datas.
*/
function vpc_upload_create_option_field( $option, $price, $config_to_load, $multi_views, $component_behaviour, $component, $config ) {
	vpc_upload_create_field( $option, $config_to_load, $multi_views, $component_behaviour, $component, $config );
}

/**
* Function to create a custom upload field.
*
* @param array   $option              An option's datas.
* @param array   $config_to_load      A configuration old selected options datas.
* @param boolean $multi_views         Tells if multiviews is activated or not.
* @param string  $component_behaviour The value of a component's behavior.
* @param array   $component           A component's datas.
* @param array   $config              A configuration's datas.
*/
function vpc_upload_create_field( $option, $config_to_load, $multi_views, $component_behaviour, $component, $config ) {
	if (!get_option('vpc-upload-image-add-on-license-key')) {
		echo "<h2 class='error_msg'>You have not activated your license yet. Please, activate it in order to use Custom upload add on.</h2>";
	} else {
		if ( $multi_views && 'upload' === $component_behaviour ) {
			if ( class_exists( 'Vpc_Mva' ) ) {
				$views = vpc_upload_get_active_views( $config );
			}
			if ( isset( $views ) && ! empty( $views ) ) {
				foreach ( $views as $key => $view ) {
					$view_field_name = 'view_' . $key . '_upload_component';
					if ( isset( $option[ $view_field_name ] ) && ! empty( $option[ $view_field_name ] ) ) {
						vpc_upload_set_option_output( $view_field_name, $option, $config_to_load, $multi_views, $component_behaviour, $component, $config, $key, $view );
					}
				}
			}
		} elseif ( ! $multi_views && 'upload' === $component_behaviour ) {
			vpc_upload_set_option_output( 'upload-component', $option, $config_to_load, $multi_views, $component_behaviour, $component, $config, '', '' );
		}
	}
}

/**
* Function to set the custom upload option tooltip.
*
* @param array  $option                An option's datas.
* @param array  $upload_component_data A custom upload component's datas.
* @param string $key_view              The current view's key.
* @param string $value_view            The current view's value.
* @return array
*/
function vpc_upload_set_option_tooltip( $option, $upload_component_data, $key_view, $value_view ) {
	global $vpc_settings, $WOOCS;
	$tooltip       = '';
	$price         = ( isset( $upload_component_data[0] ) && isset( $upload_component_data[0]['price'] ) && '' !== $upload_component_data[0]['price'] ) ? $upload_component_data[0]['price'] : 0;
	$product_id = get_vpc_current_product_id();
        $formated_price =$price ;
        if(class_exists( 'Woocommerce' ))
        $price = vpc_apply_taxes_on_price_if_needed( $price, wc_get_product($product_id) );
        $price_tooltip = get_proper_value( $vpc_settings, 'view-price' );
	$name_tooltip  = get_proper_value( $vpc_settings, 'view-name' );

	if ( $WOOCS ) {
		$currencies = $WOOCS->get_currencies();
		$price      = $price * $currencies[ $WOOCS->current_currency ]['rate'];
	}
        if(class_exists( 'Woocommerce' ))
            $formated_price = wc_price( $price );

	if ( isset( $key_view ) && isset( $value_view ) && ! empty( $value_view ) ) {
		if ( 'Yes' === $name_tooltip ) {
			$tooltip = $option['name'] . ' ' . strtoupper( $value_view );
		}
	} else {
		if ( 'Yes' === $name_tooltip ) {
			$tooltip = $option['name'];
		}
	}

	if ( 'Yes' === $price_tooltip ) {
		if ( strpos( $formated_price, '-' ) || strpos( $formated_price, '+' ) ) {
			$tooltip .= " $formated_price";
		} else {
			$tooltip .= " +$formated_price";
		}
	}
	if ( ! empty( $option['desc'] ) ) {
		$tooltip .= ' (' . $option['desc'] . ')';
	}
	return apply_filters( 'vpc_upload_option_tooltip', $tooltip, $price, $option, $key_view, $value_view );
}

/**
* Function to set the custom upload option output.
*
* @param string  $field_name          The option's field name.
* @param array   $option              An option's datas.
* @param array   $config_to_load      A configuration old selected options datas.
* @param boolean $multi_views         Tells if multiviews is activated or not.
* @param string  $component_behaviour The value of a component's behavior.
* @param array   $component           A component's datas.
* @param array   $config              A configuration's datas.
* @param string  $key                 The current view's key.
* @param string  $view                The current view's value.
*/
function vpc_upload_set_option_output( $field_name, $option, $config_to_load, $multi_views, $component_behaviour, $component, $config, $key, $view ) {
	$upload_component_meta_key = 'vpc-upload-component';
	if ( isset( $option[ $field_name ] ) && '' !== $option[ $field_name ] ) {
		$upload_component_id = $option[ $field_name ];
		$upload_component_data = get_post_meta( $option[ $field_name ], $upload_component_meta_key );
	} else {
		$upload_component_id = '';
		$upload_component_data = array();
	}
	if ( ! empty( $upload_component_data ) ) {
		$datas = vpc_upload_get_option_datas( $option, $upload_component_data, $config_to_load, $multi_views, $key, $view, $component, $upload_component_id );
		vpc_upload_set_option_content( $option, $upload_component_data, $datas, $component, $multi_views, $key, $view, $config );
	}
}

/**
* Function to get custom upload's option's datas.
*
* @param  array   $option                An option's datas.
* @param  array   $upload_component_data An upload component's data.
* @param  array   $config_to_load        A configuration old selected options datas.
* @param  boolean $multi_views           Tells if multiviews is activated or not.
* @param  string  $key                   The current view's key.
* @param  string  $view                  The current view's value.
* @param  array   $component             A component's datas.
* @return array                           A custom's upload's option datas.
*/
function vpc_upload_get_option_datas( $option, $upload_component_data, $config_to_load, $multi_views, $key, $view, $component, $upload_component_id ) {
	global $WOOCS;
	$class          = '';
	$img_url        = '';
	$desc           = get_proper_value( $option, 'desc' );
	$sanitized_name = sanitize_title( $option['name'] );
	$opt_name       = get_proper_value( $option, 'name', '' );
	if ( isset( $key ) && isset( $view ) && ! empty( $view ) ) {
		$sanitized_name = sanitize_title( $sanitized_name . '-' . $key );
		$field_name     = strtoupper( $view );
		$opt_name       = $opt_name . ' ' . trim( $field_name );
	}
	$component_id = 'component_' . sanitize_title( str_replace( ' ', '', $component['cname'] ) );
	$component_id = get_proper_value( $component, 'component_id', $component_id );

	$tooltip   = vpc_upload_set_option_tooltip( $option, $upload_component_data, $key, $view );
	$top       = ( isset( $upload_component_data[0] ) && isset( $upload_component_data[0]['top'] ) && '' !== $upload_component_data[0]['top'] ) ? $upload_component_data[0]['top'] : 0;
	$left      = ( isset( $upload_component_data[0] ) && isset( $upload_component_data[0]['left'] ) && '' !== $upload_component_data[0]['left'] ) ? $upload_component_data[0]['left'] : 0;
	$angle     = ( isset( $upload_component_data[0] ) && isset( $upload_component_data[0]['angle'] ) && '' !== $upload_component_data[0]['angle'] ) ? $upload_component_data[0]['angle'] : 0;
	$box_width = ( isset( $upload_component_data[0] ) && isset( $upload_component_data[0]['width'] ) && '' !== $upload_component_data[0]['width'] ) ? $upload_component_data[0]['width'] : 30;
	$box_height = ( isset( $upload_component_data[0] ) && isset( $upload_component_data[0]['height'] ) && '' !== $upload_component_data[0]['height'] ) ? $upload_component_data[0]['height'] : 30;
	$lock_rotation   = ( isset( $upload_component_data[0] ) && isset( $upload_component_data[0]['lock-rotation'] ) ) ? $upload_component_data[0]['lock-rotation'] : 'no';
	$lock_scaling_x  = ( isset( $upload_component_data[0] ) && isset( $upload_component_data[0]['lock-scaling-x'] ) ) ? $upload_component_data[0]['lock-scaling-x'] : 'no';
	$lock_scaling_y  = ( isset( $upload_component_data[0] ) && isset( $upload_component_data[0]['lock-scaling-y'] ) ) ? $upload_component_data[0]['lock-scaling-y'] : 'no';
	$lock_movement_x = ( isset( $upload_component_data[0] ) && isset( $upload_component_data[0]['lock-movement-x'] ) ) ? $upload_component_data[0]['lock-movement-x'] : 'no';
	$lock_movement_y = ( isset( $upload_component_data[0] ) && isset( $upload_component_data[0]['lock-movement-y'] ) ) ? $upload_component_data[0]['lock-movement-y'] : 'no';
	$price     = ( isset( $upload_component_data[0] ) && isset( $upload_component_data[0]['price'] ) && '' !== $upload_component_data[0]['price'] ) ? $upload_component_data[0]['price'] : 0;
        $product_id = get_vpc_current_product_id();
        if(class_exists( 'Woocommerce' ))
         $price = vpc_apply_taxes_on_price_if_needed( $price, wc_get_product($product_id) );
	if ( $WOOCS ) {
		$currencies = $WOOCS->get_currencies();
		$price      = $price * $currencies[ $WOOCS->current_currency ]['rate'];
	}

	$field_selector = $sanitized_name . '-field';
	$canvas_id = ( isset( $key ) && '' !== $key ) ? 'text_and_upload_panel_' . $key : 'text_and_upload_panel';

	if (isset($config_to_load[ 'canvas_data' ]) && '' !== $config_to_load[ 'canvas_data' ]) {
		$canvases_data_to_load = json_decode(stripslashes($config_to_load[ 'canvas_data' ]));
		foreach($canvases_data_to_load as $canvas_key => $canvas) {
			if ($canvas_id === $canvas_key) {
				$option_datas = vpc_upload_get_canvas_data($canvas, $sanitized_name);
				if ( isset( $option_datas->type ) && 'image' === $option_datas->type ) {
					$img_url = $option_datas->src;
				}
			}
		}
	}

	$custom_attr = '';
	$custom_attr = apply_filters( 'custom_upload_attribut', $custom_attr, $option, $key );

	$datas = apply_filters(
		'vpc_upload_datas',
		array(
			'class'               => $class,
			'multi_views'         => $multi_views,
			'view'                => $view,
			'key'                 => $key,
			'img_url'             => $img_url,
			'sanitized_name'      => $sanitized_name,
			'upload_component_id' => $upload_component_id,
			'opt_name'            => $opt_name,
			'top'                 => $top,
			'left'                => $left,
			'angle'               => $angle,
			'box_width'           => $box_width,
			'box_height'          => $box_height,
			'lock_rotation'       => $lock_rotation,
			'lock_scaling_x'      => $lock_scaling_x,
			'lock_scaling_y'      => $lock_scaling_y,
			'lock_movement_x'     => $lock_movement_x,
			'lock_movement_y'     => $lock_movement_y,
			'price'               => $price,
			'tooltip'             => $tooltip,
			'container'           => $sanitized_name . '-container',
			'description'         => $desc,
			'option_id'           => $sanitized_name,
			'component_id'        => $component_id,
			'custom_attr'         => $custom_attr,
			'field_selector'      => $field_selector,
		),
		$option,
		$upload_component_data,
		$config_to_load,
		$multi_views,
		$key,
		$view,
		$component,
		$upload_component_id
	);
	return $datas;
}

function vpc_upload_get_canvas_data($canvas, $sanitized_name)
{
	foreach ($canvas as $key => $value) {
		if ('image-' . $sanitized_name . '-container' === $key) {
			return $value;
		}
	}
	return false;
}

/**
* Function to get the custom upload's option content.
*
* @param array   $option                An option's datas.
* @param array   $upload_component_data A custom upload component's datas.
* @param array   $datas                 All custom upload option settings datas.
* @param array   $component             A component's datas.
* @param boolean $multi_views           Tells if multiviews is activated or not.
* @param string  $key                   he current view's key.
* @param string  $view                  The current view's value.
* @param array   $config                A configuration's datas.
*/
function vpc_upload_set_option_content( $option, $upload_component_data, $datas, $component, $multi_views, $key, $view, $config ) {
	$class          = get_proper_value( $datas, 'class', '' );
	$img_url        = get_proper_value( $datas, 'img_url', '' );
	$sanitized_name = get_proper_value( $datas, 'sanitized_name' );
	$opt_name       = get_proper_value( $datas, 'opt_name' );
	$option_id      = get_proper_value( $option, 'option_id' );

	$top       = get_proper_value( $datas, 'top' );
	$left      = get_proper_value( $datas, 'left' );
	$angle     = get_proper_value( $datas, 'angle' );
	$box_width = get_proper_value( $datas, 'box_width' );
	$box_height = get_proper_value( $datas, 'box_height' );
	$descr     = get_proper_value( $datas, 'description' );

	$lock_rotation   = get_proper_value( $datas, 'lock_rotation' );
	$lock_scaling_x  = get_proper_value( $datas, 'lock_scaling_x' );
	$lock_scaling_y  = get_proper_value( $datas, 'lock_scaling_y' );
	$lock_movement_x = get_proper_value( $datas, 'lock_movement_x' );
	$lock_movement_y = get_proper_value( $datas, 'lock_movement_y' );

	$price          = get_proper_value( $datas, 'price' );
	$tooltip        = get_proper_value( $datas, 'tooltip' );
	$component_id   = get_proper_value( $datas, 'component_id' );
	$custom_attr    = get_proper_value( $datas, 'custom_attr' );
	$field_selector = get_proper_value( $datas, 'field_selector' );

	$z_index = get_proper_value( $component, 'c_index' );

	$key  = get_proper_value( $datas, 'key' );
	$view = get_proper_value( $datas, 'view' );

	$view_to_focus_on = ( isset( $view ) && '' !== $view ) ? 'data-view-focus=' . esc_html( sanitize_title( $view ) ) : '';

	$upload_component_id = get_proper_value( $datas, 'upload_component_id' );

	$canvas_id = ( isset( $key ) && '' !== $key ) ? 'data-canvas-id=text_and_upload_panel_' . $key : 'data-canvas-id=text_and_upload_panel';
		ob_start();
	?>
	<script>
	vpc.upload_component_all_data["<?php echo esc_html( $sanitized_name ) . '-field'; ?>"] = <?php echo wp_json_encode( $upload_component_data ); ?>;
	vpc.upload_component_data["<?php echo esc_html( $sanitized_name ) . '-field'; ?>"] = <?php echo wp_json_encode( $upload_component_id ); ?>;
	</script>
	<div class="vpc-single-option-wrap" data-oid="<?php echo esc_html( $option['option_id'] ); ?>" data-cid="<?php echo esc_html( $component_id ); ?>">
		<form id="userfile_upload_form_<?php echo esc_html( $option['option_id'] ) . '_' . esc_html( $sanitized_name ); ?>" class="custom-uploader userfile_upload_form" method="POST" action="<?php echo esc_html( admin_url( 'admin-ajax.php' ) ); ?>" enctype="multipart/form-data" data-name="<?php echo esc_html( $sanitized_name ); ?>" data-bare-name="<?php echo esc_html( $opt_name ); ?>" <?php echo esc_html( $view_to_focus_on ); ?> data-index="<?php echo esc_html( $z_index ); ?>" data-top="<?php echo esc_html( $top ); ?>" data-left="<?php echo esc_html( $left ); ?>" data-width="<?php echo esc_html( $box_width ); ?>" data-height="<?php echo esc_html( $box_height ); ?>" data-angle="<?php echo esc_html( $angle ); ?>" data-view="<?php echo esc_html( $view ); ?>" data-key="<?php echo esc_html( $key ); ?>" <?php echo esc_html( $canvas_id ); ?> data-lock-rotation="<?php echo esc_html( $lock_rotation ); ?>" data-lock-scaling-x="<?php echo esc_html( $lock_scaling_x ); ?>" data-lock-scaling-y="<?php echo esc_html( $lock_scaling_y ); ?>" data-lock-movement-x="<?php echo esc_html( $lock_movement_x ); ?>" data-lock-movement-y="<?php echo esc_html( $lock_movement_y ); ?>"
			<?php
			if ( isset( $custom_attr ) ) {
				echo esc_html( $custom_attr );
			}
			?>
			>
			<input type="hidden" name="nonce" value="<?php echo esc_html( wp_create_nonce( 'vpc-picture-upload-nonce' ) ); ?>">
			<input type="hidden" name="action" value="handle_picture_upload">
			<input class="img_link" type="hidden" value="<?php echo esc_html( $img_url ); ?>">
			<div class="upload_option_name"><?php echo wp_kses_post( $tooltip ); ?></div>
			<div  class="drop">
				<label class="upload_in_pause" for="userfile">
					<a><?php esc_html_e( 'Upload a file', 'vpc-upload' ); ?></a>
					<div class="acd-upload-info" data-price="<?php echo esc_html( $price ); ?>" >
						<?php
						if ( '' === $img_url ) {
							?>
							<img alt=''>
							<?php
						} else {
							?>
							<img src="<?php echo esc_html( $img_url ); ?>" alt="">
							<?php
						}
						?>
					</div>
				</label>
				<input type="file" name="userfile" />
			</div>
		</form>
		<div class="opt_descr"><?php echo esc_html( $descr ); ?></div>
	</div>
	<?php
	$content = ob_get_contents();
	ob_end_clean();
	$content = apply_filters( 'vpc_upload_container', $content, $option, $upload_component_data, $datas, $component, $multi_views, $key, $view, $config );
	echo $content;
}

/**
* @method Function to get text recapitulatif from the canvas in order to properly escape outputs.
*
* @param  string $output       Initial output.
* @param  object $canvas_datas Canvas data.
* @return string               Returned output.
*/
function vpc_upload_get_canvas_datas_recap( $output, $canvas_datas )
{
	$new_output = '';
	if (isset($canvas_datas) && is_object($canvas_datas)) {
		foreach ($canvas_datas as $key => $object) {
			if ( isset( $object ) &&  isset( $object->type ) && 'image' === $object->type ) {
				$upload_html = "<div class='vpc-cart-options'><div><div>Image link: </div><div><img src='" . $object->src . "' data-tooltip-title='" . $object->src . "'></div></div></div>";
				$new_output .= "<div><div class='vpc-cart-component'>". $object->name ."</div>$upload_html</div>";
			}
		}
	}
	$new_output = apply_filters( 'vpc_upload_get_canvas_datas_recap', $new_output, $canvas_datas);
	$output .= $new_output;
	return $output;
}

function vpc_upload_get_component_data($upload_component_id)
{
	$upload_component_meta_key = 'vpc-upload-component';
	if ( isset( $upload_component_id ) && '' !== $upload_component_id ) {
		$upload_component_data = get_post_meta( $upload_component_id, $upload_component_meta_key )[0];
	} else {
		$upload_component_data = array();
	}
	return $upload_component_data;
}
