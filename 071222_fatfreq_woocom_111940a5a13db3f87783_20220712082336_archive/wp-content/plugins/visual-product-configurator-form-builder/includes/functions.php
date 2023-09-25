<?php

/**
 * display form field
 * @global type $wpdb
 * @global type $WOOCS
 * @param type $form_id
 * @param type $config_to_load
 * @return string
 */
function display_form_builder($form_id, $config_to_load = array()) {
     if (!get_option('vpc-form-builder-add-on-license-key')) {
        $html ="<h2 class='error_msg'>You have not activated your license yet. Please, activate it in order to use Form builder add on.</h2>";
    } else {
    $html = "<div id='display_ofb'>";
    global $wpdb;
    global $WOOCS;
    $woocs = $WOOCS;
    // Select all form_builder ID
    $resultats = $wpdb->get_results(
            "SELECT ID From {$wpdb->prefix}posts Where post_type = 'ofb' And post_status = 'publish'",
            OBJECT_K
    );
    if ($resultats) {
        foreach ($resultats as $index => $data) {
            if ($data->ID === $form_id) {

                $meta_values = get_post_meta($form_id, 'ofb');
                $each = $meta_values[0]['components'];
                $html .= "<form class='formbuilt' id='form' method='POST' enctype='multipart/form-data'>";

                foreach ($each as $data) {
                    if ($woocs) {
                        $currencies = $woocs->get_currencies();
                        $data['price'] = $data['price'] * $currencies[$woocs->current_currency]['rate'];
                    }
                    switch ($data['type']) {
                        case 'text':
                            $text_html = '<div>';
                            if (isset($data['label']) && isset($data['name']) && isset($data['length'])) {
                                if (isset($config_to_load[$data['name']])) {
                                    if (isset($data['required']) && 'Yes' === $data['required']) {
                                        $text_html .= '<label>' . $data['label'] . '</label><input type="' . $data['type'] . '" name="' . $data['name'] . '" class="validate[required]" maxlength="' . $data['length'] . '" data-price="' . $data['price'] . '" value="' . $config_to_load[$data['name']] . '">';
                                    } else {
                                        $text_html .= '<label>' . $data['label'] . '</label><input type="' . $data['type'] . '" name="' . $data['name'] . '" maxlength="' . $data['length'] . '" data-price="' . $data['price'] . '" value="' . $config_to_load[$data['name']] . '">';
                                    }
                                } else {
                                    if (isset($data['required']) && 'Yes' === $data['required']) {
                                        $text_html .= '<label>' . $data['label'] . '</label><input type="' . $data['type'] . '" name="' . $data['name'] . '" class="validate[required]" maxlength="' . $data['length'] . '" data-price="' . $data['price'] . '">';
                                    } else {
                                        $text_html .= '<label>' . $data['label'] . '</label><input type="' . $data['type'] . '" name="' . $data['name'] . '" maxlength="' . $data['length'] . '" data-price="' . $data['price'] . '">';
                                    }
                                }
                            }
                            $text_html .= '</div>';
                            $html .= apply_filters('vpc_text_form', $text_html);
                            break;

                        case 'textarea':
                            $textarea_html = '<div>';
                            if (isset($data['label']) && isset($data['name']) && isset($data['length'])) {
                                if (isset($config_to_load[$data['name']])) {
                                    if (isset($data['required']) && 'Yes' === $data['required']) {
                                        $textarea_html .= '<label>' . esc_html($data['label']) . '</label> <textarea class="validate[required]" name="' . esc_html($data['name']) . '"  maxlength="' . esc_html($data['length']) . '" data-price="' . esc_html($data['price']) . '">' . $config_to_load[$data['name']] . '</textarea>';
                                    } else {
                                        $textarea_html .= '<label>' . esc_html($data['label']) . '</label> <textarea name="' . esc_html($data['name']) . '"  maxlength="' . esc_html($data['length']) . '" data-price="' . esc_html($data['price']) . '">' . $config_to_load[$data['name']] . '</textarea>';
                                    }
                                } else {
                                    if (isset($data['required']) && 'Yes' === $data['required']) {
                                        $textarea_html .= '<label>' . esc_html($data['label']) . '</label> <textarea class="validate[required]" name="' . esc_html($data['name']) . '"  maxlength="' . esc_html($data['length']) . '" data-price="' . esc_html($data['price']) . '"></textarea>';
                                    } else {
                                        $textarea_html .= '<label>' . esc_html($data['label']) . '</label> <textarea name="' . esc_html($data['name']) . '"  maxlength="' . esc_html($data['length']) . '" data-price="' . esc_html($data['price']) . '"></textarea>';
                                    }
                                }
                            }
                            $textarea_html .= '</div>';
                            $html .= apply_filters('vpc_textarea_form', $textarea_html);
                            break;

                        case 'file':
                            $file_html = '<div>';
                            if (isset($data['label']) && isset($data['type']) && isset($data['name'])) {
                                if ('Yes' === $data['required']) {
                                    $file_html .= '<label>' . esc_html($data['label']) . '</label><input type="' . esc_html($data['type']) . '" class="validate[required]" name="' . esc_html(sanitize_title($data['name'])) . '"  maxlength="' . esc_html($data['length']) . '" data-price="' . esc_html($data['price']) . '" >';
                                } else {
                                    $file_html .= '<label>' . esc_html($data['label']) . '</label><input type="' . esc_html($data['type']) . '" name="' . esc_html(sanitize_title($data['name'])) . '" data-price="' . esc_html($data['price']) . '">';
                                }
                            }
                            $file_html .= '</div>';
                            $html .= apply_filters('vpc_file_form', $file_html);
                            break;

                        case 'radio':
                            $radio_html = '<div>';
                            if (isset($data['label']) && isset($data['options'])) {
                                $option = $data['options'];
                                $radio_html .= '<label>' . esc_html($data['label']) . '</label>';
                                if (isset($data['required']) && 'Yes' === $data['required']) {
                                    foreach ($option as $optionvalue) {
                                        if (isset($config_to_load[$data['name']])) {
                                            unset($optionvalue['default']);
                                        }
                                        if ($woocs) {
                                            $currencies = $woocs->get_currencies();
                                            $optionvalue['option_price'] = $optionvalue['option_price'] * $currencies[$woocs->current_currency]['rate'];
                                        }
                                        if (isset($optionvalue['op_value'])) {
                                            if (isset($config_to_load[$data['name']]) && $config_to_load[$data['name']] === $optionvalue['op_value']) {
                                                $radio_html .= '<input type="radio" name="' . esc_html($data['name']) . '" class="validate[required]"  value="' . esc_html($optionvalue['op_value']) . '" checked="checked" data-price="' . esc_html($optionvalue['option_price']) . '"><span>' . esc_html__($optionvalue['op_value']) . '</span>';
                                            } elseif (isset($optionvalue['default'])) {
                                                $radio_html .= '<input type="radio" name="' . esc_html($data['name']) . '" class="validate[required]"  value="' . esc_html($optionvalue['op_value']) . '" checked="checked" data-price="' . esc_html($optionvalue['option_price']) . '"><span>' . esc_html__($optionvalue['op_value']) . '</span>';
                                            } else {
                                                $radio_html .= '<input type="radio" name="' . esc_html($data['name']) . '" class="validate[required]"  value="' . esc_html($optionvalue['op_value']) . '" data-price="' . esc_html($optionvalue['option_price']) . '" ><span>' . esc_html__($optionvalue['op_value']) . '</span>';
                                            }
                                        }
                                    }
                                } else {
                                    foreach ($option as $optionvalue) {
                                        if (isset($config_to_load[$data['name']])) {
                                            unset($optionvalue['default']);
                                        }
                                        if ($woocs) {
                                            $currencies = $woocs->get_currencies();
                                            $optionvalue['option_price'] = $optionvalue['option_price'] * $currencies[$woocs->current_currency]['rate'];
                                        }
                                        if (isset($optionvalue['op_value'])) {
                                            if (isset($config_to_load[$data['name']]) && $config_to_load[$data['name']] === $optionvalue['op_value']) {
                                                $radio_html .= '<input type="radio" name="' . esc_html($data['name']) . '"  value="' . esc_html($optionvalue['op_value']) . '" checked="checked" data-price="' . esc_html($optionvalue['option_price']) . '" ><span>' . esc_html__($optionvalue['op_value']) . '</span>';
                                            } elseif (isset($optionvalue['default'])) {
                                                $radio_html .= '<input type="radio" name="' . esc_html($data['name']) . '"  value="' . esc_html($optionvalue['op_value']) . '" checked="checked" data-price="' . esc_html($optionvalue['option_price']) . '" ><span>' . esc_html__($optionvalue['op_value']) . '</span>';
                                            } else {
                                                $radio_html .= '<input type="radio" name="' . esc_html($data['name']) . '"  value="' . esc_html($optionvalue['op_value']) . '" data-price="' . esc_html($optionvalue['option_price']) . '" ><span>' . esc_html__($optionvalue['op_value']) . '</span>';
                                            }
                                        }
                                    }
                                }
                            }
                            $radio_html .= '</div>';
                            $html .= apply_filters('vpc_radio_form', $radio_html);
                            break;

                        case 'checkbox':
                            $checkbox_html = '<div>';
                            if (isset($data['label']) && isset($data['options'])) {
                                $option = $data['options'];
                                $checkbox_html .= '<label>' . esc_html($data['label']) . '</label>';
                                if ('Yes' === $data['required']) {
                                    foreach ($option as $optionvalue) {
                                        if (isset($config_to_load[$data['name']])) {
                                            unset($optionvalue['default']);
                                        }
                                        if ($woocs) {
                                            $currencies = $woocs->get_currencies();
                                            $optionvalue['option_price'] = $optionvalue['option_price'] * $currencies[$woocs->current_currency]['rate'];
                                        }
                                        if (isset($optionvalue['op_value'])) {
                                            if (isset($config_to_load[$data['name']]) && $config_to_load[$data['name']] === $optionvalue['op_value']) {
                                                $checkbox_html .= '<input type="checkbox" name="' . esc_html($data['name']) . '[]" class="validate[required]"  " value="' . esc_html($optionvalue['op_value']) . '" checked="checked" data-price="' . esc_html($optionvalue['option_price']) . '" ><span>' . esc_html__($optionvalue['op_value']) . '</span>';
                                            } elseif (isset($optionvalue['default'])) {
                                                $checkbox_html .= '<input type="checkbox" name="' . esc_html($data['name']) . '[]" class="validate[required]"  " value="' . esc_html($optionvalue['op_value']) . '" checked="checked" data-price="' . esc_html($optionvalue['option_price']) . '" ><span>' . esc_html__($optionvalue['op_value']) . '</span>';
                                            } else {
                                                $checkbox_html .= '<input type="checkbox" name="' . esc_html($data['name']) . '[]" class="validate[required]"  " value="' . esc_html($optionvalue['op_value']) . '" data-price="' . esc_html($optionvalue['option_price']) . '"><span>' . esc_html__($optionvalue['op_value']) . '</span>';
                                            }
                                        }
                                    }
                                } else {
                                    foreach ($option as $optionvalue) {
                                        if (isset($config_to_load[$data['name']])) {
                                            unset($optionvalue['default']);
                                        }
                                        if ($woocs) {
                                            $currencies = $woocs->get_currencies();
                                            $optionvalue['option_price'] = $optionvalue['option_price'] * $currencies[$woocs->current_currency]['rate'];
                                        }
                                        if (isset($optionvalue['op_value'])) {
                                            if (isset($config_to_load[$data['name']]) && $config_to_load[$data['name']] === $optionvalue['op_value']) {
                                                $checkbox_html .= '<input type="checkbox" name="' . esc_html($data['name']) . '[]" value="' . esc_html($optionvalue['op_value']) . '" checked="checked" data-price="' . esc_html($optionvalue['option_price']) . '"><span>' . esc_html__($optionvalue['op_value']) . '</span>';
                                            } elseif (isset($optionvalue['default'])) {
                                                $checkbox_html .= '<input type="checkbox" name="' . esc_html($data['name']) . '[]" value="' . esc_html($optionvalue['op_value']) . '" checked="checked" data-price="' . esc_html($optionvalue['option_price']) . '"><span>' . esc_html__($optionvalue['op_value']) . '</span>';
                                            } else {
                                                $checkbox_html .= '<input type="checkbox" name="' . esc_html($data['name']) . '[]" value="' . esc_html($optionvalue['op_value']) . '" data-price="' . esc_html($optionvalue['option_price']) . '"><span>' . esc_html__($optionvalue['op_value']) . '</span>';
                                            }
                                        }
                                    }
                                }
                            }
                            $checkbox_html .= '</div>';
                            $html .= apply_filters('vpc_checkbox_form', $checkbox_html);
                            break;

                        case 'select':
                            $select_html = '<div>';
                            if (isset($data['label']) && isset($data['options'])) {
                                $selectoption = $data['options'];
                                $select_html .= '<label>' . esc_html($data['label']) . '</label>';
                                if (isset($data['required']) && 'Yes' === $data['required']) {
                                    $select_html .= '<select  name="' . esc_html($data['name']) . '" class="validate[required]"  >';
                                } else {
                                    $select_html .= '<select  name="' . esc_html($data['name']) . '">';
                                }
                                foreach ($selectoption as $selectvalue) {
                                    if (isset($config_to_load[$data['name']])) {
                                        unset($selectvalue['default']);
                                    }
                                    if ($woocs) {
                                        $currencies = $woocs->get_currencies();
                                        $selectvalue['option_price'] = $selectvalue['option_price'] * $currencies[$woocs->current_currency]['rate'];
                                    }
                                    if (isset($selectvalue['op_value'])) {
                                        if (isset($config_to_load[$data['name']]) && $config_to_load[$data['name']] === $selectvalue['op_value']) {
                                            $select_html .= '<option value="' . esc_html($selectvalue['op_value']) . '" data-price="' . esc_html($selectvalue['option_price']) . '" selected>' . esc_html($selectvalue['op_value']) . '</option>';
                                        } elseif (isset($selectvalue['default'])) {
                                            $select_html .= '<option value="' . esc_html($selectvalue['op_value']) . '" data-price="' . esc_html($selectvalue['option_price']) . '" selected>' . esc_html($selectvalue['op_value']) . '</option>';
                                        } else {
                                            $select_html .= '<option value="' . esc_html($selectvalue['op_value']) . '" data-price="' . esc_html($selectvalue['option_price']) . '" >' . esc_html($selectvalue['op_value']) . '</option>';
                                        }
                                    }
                                }
                                $select_html .= '</select>';
                            }
                            $select_html .= '</div>';
                            $html .= apply_filters('vpc_select_form', $select_html);
                            break;

                        // case 'number':
                        // $html .= "<div>";
                        // if (isset($data['label']) && isset($data['type']) && isset($data['name'])) {
                        // if ( 'Yes' === $data['required'] ) {
                        // $html .= '<label>' . esc_html($data['label']) . '</label> <input type="' . esc_html($data['type']) . '" class="validate[required]" name="' . esc_html($data['name']) . '" data-price="'. esc_html($data['price']).'" >' ;
                        // } else {
                        // $html .= '<label>' . esc_html($data['label']) . '</label> <input type="' . esc_html($data['type']) . '" name="' . esc_html($data['name']) . '" data-price="'. esc_html($data['price']).'">' ;
                        // }
                        // }
                        // $html .= "</div>";
                        // break;
                        // case 'tel':
                        // $html .= "<div>";
                        // if (isset($data['label']) && isset($data['type']) && isset($data['name'])) {
                        // if ( 'Yes' === $data['required'] ) {
                        // $html .= '<label>' . esc_html($data['label']) . '</label> <input type="' . esc_html($data['type']) . '" class="validate[required]" name="' . esc_html($data['name']) . '"  data-price="'. esc_html($data['price']).'">' ;
                        // } else {
                        // $html .= '<label>' . esc_html($data['label']) . '</label><input type="' . esc_html($data['type']) . '" name="' . esc_html($data['name']) . '" data-price="'. esc_html($data['price']).'">';
                        // }
                        // }
                        // break;
                        // $html .= "</div>";
                        // case 'url':
                        // $html .= "<div>";
                        // if (isset($data['label']) && isset($data['type']) && isset($data['name'])) {
                        // if ( 'Yes' === $data['required'] ) {
                        // $html .= '<label>' . esc_html($data['label']) . '</label> <input type="' . esc_html($data['type']) . '" class="validate[required]" name="' . esc_html($data['name']) . '"  data-price="'. esc_html($data['price']).'" >' ;
                        // } else {
                        // $html .= '<label>' . esc_html($data['label']) . '</label><input type="' . esc_html($data['type']) . '" name="' . esc_html($data['name']) . '" data-price="'. esc_html($data['price']).'" >' ;
                        // }
                        // }
                        // $html .= "</div>";
                        // break;
                        case 'email':
                            $email_html = '<div>';
                            if (isset($data['label']) && isset($data['type']) && isset($data['name'])) {
                                if (isset($config_to_load[$data['name']])) {
                                    if ('Yes' === $data['required']) {
                                        $email_html .= '<label>' . esc_html($data['label']) . '</label><input type="' . esc_html($data['type']) . '" class="validate[required,custom[email]]"  name="' . esc_html($data['name']) . '"  data-price="' . esc_html($data['price']) . '" value="' . $config_to_load[$data['name']] . '" >';
                                    } else {
                                        $email_html .= '<label>' . esc_html($data['label']) . '</label><input type="' . esc_html($data['type']) . '"  name="' . esc_html($data['name']) . '" data-price="' . esc_html($data['price']) . '" value="' . $config_to_load[$data['name']] . '">';
                                    }
                                } else {
                                    if (isset($data['required']) && 'Yes' === $data['required']) {
                                        $email_html .= '<label>' . esc_html($data['label']) . '</label><input type="' . esc_html($data['type']) . '" class="validate[required,custom[email]]"  name="' . esc_html($data['name']) . '"  data-price="' . esc_html($data['price']) . '" >';
                                    } else {
                                        $email_html .= '<label>' . esc_html($data['label']) . '</label><input type="' . esc_html($data['type']) . '"  name="' . esc_html($data['name']) . '" data-price="' . esc_html($data['price']) . '" >';
                                    }
                                }
                            }
                            $email_html .= '</div>';
                            $html .= apply_filters('vpc_email_form', $email_html);
                            break;

                        // case 'date':
                        // $html .= "<div>";
                        // if (isset($data['label']) && isset($data['type']) && isset($data['name'])) {
                        // if ( 'Yes' === $data['required'] ) {
                        // $html .= '<label>' . esc_html($data['label']) . '</label> <input type="' . esc_html($data['type']) . '" class="validate[required]" name="' . esc_html($data['name']) . '" data-price="'. esc_html($data['price']).'" >';
                        // } else {
                        // $html .= '<label>' . esc_html($data['label']) . '</label><input type="' . esc_html($data['type']) . '" name="' . esc_html($data['name']) . '" data-price="'. esc_html($data['price']).'" >';
                        // }
                        // }
                        // $html .= "</div>";
                        // break;
                        case 'range':
                            $range_html = '<div>';
                            if (isset($data['label']) && isset($data['type']) && isset($data['name'])) {
                                if (isset($data['required']) && 'Yes' === $data['required']) {
                                    $range_html .= '<label>' . esc_html($data['label']) . '</label> <input type="' . esc_html($data['type']) . '" class="validate[required]" name="' . esc_html($data['name']) . '" data-price="' . esc_html($data['price']) . '" >';
                                } else {
                                    $range_html .= '<label>' . esc_html($data['label']) . '</label><input type="' . esc_html($data['type']) . '" name="' . esc_html($data['name']) . '" data-price="' . esc_html($data['price']) . '" >';
                                }
                            }
                            $range_html .= '</div>';
                            $html .= apply_filters('vpc_range_form', $range_html);
                            break;

                        case 'acceptance':
                            $acceptance_html = '<div>';
                            if (isset($data['label']) && isset($data['type']) && isset($data['name'])) {
                                if (isset($data['required']) && 'Yes' === $data['required']) {
                                    $acceptance_html .= '<label>' . esc_html($data['label']) . '</label><input type="' . esc_html($data['type']) . '" class="validate[required]" name="' . esc_html($data['name']) . '" data-price="' . esc_html($data['price']) . '">';
                                } else {
                                    $acceptance_html .= '<label>' . esc_html($data['label']) . '</label><input type="' . esc_html($data['type']) . '" name="' . esc_html($data['name']) . '" data-price="' . esc_html($data['price']) . '" >';
                                }
                            }
                            $acceptance_html .= '</div>';
                            $html .= apply_filters('vpc_acceptance_form', $acceptance_html);
                            break;

                        case 'password':
                            $password_html = '<div>';
                            if (isset($data['label']) && isset($data['type']) && isset($data['name'])) {
                                if (isset($data['required']) && 'Yes' === $data['required']) {
                                    $password_html .= '<label>' . esc_html($data['label']) . '</label> <input type="' . esc_html($data['type']) . '" class="validate[required]" name="' . esc_html($data['name']) . '"  minlength="' . esc_html($data['length']) . '" data-price="' . esc_html($data['price']) . '">';
                                } else {
                                    $password_html .= '<label>' . esc_html($data['label']) . '</label><input type="' . esc_html($data['type']) . '" name="' . esc_html($data['name']) . '"  minlength="' . esc_html($data['length']) . '" data-price="' . esc_html($data['price']) . '" >';
                                }
                            }
                            $password_html .= '</div>';
                            $html .= apply_filters('vpc_password_form', $password_html);
                            break;
                    }
                }

                $html .= '<input type="hidden" value="' . esc_html($form_id) . '" name="id_ofb">';
                // $html .= '<p align="center"><input type="submit" value="Submit" tabindex="6"/></p>';
                $html .= '</form>';
            }
        }
    }
    $html .= '</div>';
    }
    return $html;
}

function get_form_data($form_id, $fields) {
    $meta_values = get_post_meta($form_id, 'ofb');
    $each = $meta_values[0]['components'];
    $total_price = 0;
    if (is_array($each) || is_object($each)) {
        foreach ($each as $data) {
            if ('select' === $data['type']) {
                $name = $data['name'];
                foreach ($data['options'] as $datas => $array) {
                    foreach ($fields as $index => $field) {
                        if ($name === $index && $array['op_value'] === $field) {
                            if ('' !== $array['option_price']) {
                                $total_price += $array['option_price'];
                            }
                        }
                    }
                }
            } elseif ('checkbox' === $data['type'] || 'radio' === $data['type']) {
                $name = $data['name'];
                foreach ($data['options'] as $datas => $array) {
                    foreach ($fields as $index => $field) {
                        if ($name === $index) {
                            if (is_array($field)) {
                                $options = $field;
                                foreach ($options as $j => $option) {
                                    if ($array['op_value'] === $option) {
                                        if ('' !== $array['option_price']) {
                                            $total_price += $array['option_price'];
                                        }
                                    }
                                }
                            } else {
                                if ($array['op_value'] === $field) {
                                    if ('' !== $array['option_price']) {
                                        $total_price += $array['option_price'];
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                $name = $data['name'];
                foreach ($fields as $index => $field) {
                    
                    if ($name === $index && '' !== $field) {
                        if ('' !== $data['price']) {
                            $total_price += $data['price'];
                        }
                    }
                }
            }
        }
        return $total_price;
    }
}
