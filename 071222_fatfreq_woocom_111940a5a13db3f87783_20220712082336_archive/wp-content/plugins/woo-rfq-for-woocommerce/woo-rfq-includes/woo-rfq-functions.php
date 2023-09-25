<?php
/**
 * Functions used by plugins
 */




add_filter('woocommerce_valid_order_statuses_for_payment_complete','rfqtk_statuses_for_payment',100,2);
add_filter('woocommerce_valid_order_statuses_for_payment','rfqtk_statuses_for_payment',100,2);
//apply_filters( 'woocommerce_data_get_stock_quantity', $value, 'WC_Data' );

add_filter('woocommerce_product_get_price','gpls_woo_rfq_woocommerce_data_get_price',1000,2);

add_action( 'woocommerce_payment_complete', 'gpls_woo_rfq_woocommerce_pre_payment_complete',100,1 );

add_filter('woocommerce_can_reduce_order_stock', 'rfqtk_can_reduce_order_stock', 1000, 2);


if (!function_exists('rfqtk_can_reduce_order_stock')) {
    function rfqtk_can_reduce_order_stock($flag, $order)
    {


        $statuses = array('wc-gplsquote-sent', 'gplsquote-sent', 'wc-gplsquote-req', 'gplsquote-req');

        $status = $order->get_status();

        $status = 'wc-' === substr($status, 0, 3) ? substr($status, 3) : $status;

        if (in_array($status, $statuses)) {
            return false;
        }

        return $flag;

    }
}



if (!function_exists('rfqtk_first_main')) {

    function rfqtk_first_main()
    {



        if (isset($_REQUEST['pay_for_order']) && strpos($_REQUEST['key'], 'wc_order_', 0) === 0) {

            $GLOBALS["gpls_woo_rfq_show_prices"] = "yes";
            $GLOBALS["hide_for_visitor"] = "no";

            return true;
        }




    }
}

function gpls_woo_rfq_woocommerce_pre_payment_complete($orderid){

    $order = WC_Order_Factory::get_order($orderid);

    if($order->get_payment_method()=='gpls-rfq' && $order->get_status() !='wc-gplsquote-req'
        && $order->get_status() !='gplsquote-req'
    ){
        $order->update_status('wc-gplsquote-req', __('RFQ', 'woo-rfq-for-woocommerce'));
        $order->save();
    }
}


function gpls_woo_rfq_woocommerce_data_get_price( $base_price, $_product )
{

    if(!is_admin()) {
        if (gpls_empty($base_price)) {
            return 0;
        }
    }
    return $base_price;
}





if (!function_exists('rfqtk_statuses_for_payment')) {

    function rfqtk_statuses_for_payment($array, $order)
    {

        array_push($array, 'gplsquote-sent');
        array_push($array, 'wc-gplsquote-sent');
        return $array;
    }
}


if (!function_exists('gpls_woo_rfq_get_mode')) {
    function gpls_woo_rfq_get_mode(&$rfq_check, &$normal_check)
    {
        $rfq_check = false;
        $normal_check = false;

        if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "rfq") {
            add_filter('woocommerce_cart_needs_payment', 'gpls_woo_rfq_cart_needs_payment', 1000, 2);
            $rfq_check = true;
            $normal_check = false;
        }

        if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "normal_checkout") {
            $rfq_check = false;
            $normal_check = true;
        }

        if(function_exists('wp_get_current_user')) {
            if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') == 'yes' && !wp_get_current_user()->exists()) {
                $rfq_check = true;
                $normal_check = false;

            }
        }

    }
}

//add_filter( 'woocommerce_get_price_html','gpls_woo_rfq_woocommerce_empty_price_html',10,2 );

if (!function_exists('gpls_woo_rfq_woocommerce_empty_price_html')) {
        function gpls_woo_rfq_woocommerce_empty_price_html($html, $product)
        {
            if (isset($product) && is_object($product)) {

                if ($GLOBALS["gpls_woo_rfq_checkout_option"] == "rfq") {


                    $data = $product->get_data();

                    $this_price = $data["price"];

                    if (trim($data["sale_price"]) != '') {
                        $this_price = $data["sale_price"];
                    }

                    $type = $product->get_type();
                    if ($type == 'simple' || $type == 'variable') {
                        if (trim($this_price) === '') {

                          //  return false;
                        }
                    }


                }
            }
            return $html;
        }
    }


if(!function_exists('gpls_woo_rfq_plus_startsWith')) {
    function gpls_woo_rfq_plus_startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
}

if(!function_exists('gpls_woo_rfq_plus_endsWith')) {
    function gpls_woo_rfq_plus_endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }
}

if (!function_exists('gpls_empty')) {
    function gpls_empty($var)
    {
        if(!isset($var) || $var == false ){
            return true;
        }else{
            return false;
        }
    }
}

if(!function_exists('gpls_woo_rfq_add_notice')) {
    function gpls_woo_rfq_add_notice($message, $type = 'info')
    {
        //$all_notices  = array();
        $notice = array('message' => $message, 'type' => $type, 'expired' => false);
       // set_transient('gpls_woo_rfq_cart_notices', $notice, 5);
        $gpls_woo_rfq_cart_notices = gpls_woo_rfq_get_item('gpls_woo_rfq_cart_notices');

        if(is_array($gpls_woo_rfq_cart_notices)){
            array_push($gpls_woo_rfq_cart_notices,$gpls_woo_rfq_cart_notices);
        }

        gpls_woo_rfq_cart_set('gpls_woo_rfq_cart_notices',$notice);

    }
}

if(!function_exists('gpls_woo_rfq_print_notices')) {
    function gpls_woo_rfq_print_notices()
    {

        $notice = gpls_woo_rfq_get_item('gpls_woo_rfq_cart_notices');

       // $notice = get_transient('gpls_woo_rfq_cart_notices');

//d($all_notices);
        if (isset($notice['type']) && trim($notice['message']) != "") {
            ?>

            <?php if ($notice['type'] == 'error') : ?>
                <div class="woocommerce-error">
                    <?php echo trim(wp_kses_post($notice['message'])); ?>
                </div>
            <?php endif; ?>
            <?php if ($notice['type'] == 'info') : ?>
                <div class="woocommerce-info">
                    <?php echo trim(wp_kses_post($notice['message'])); ?>
                </div>
            <?php endif; ?>
            <?php if ($notice['type'] == 'notice') : ?>
                <div class="woocommerce-notice">
                    <?php echo trim(wp_kses_post($notice['message'])); ?>
                </div>
            <?php endif; ?>


            <?php

        }
        gpls_woo_rfq_cart_delete('gpls_woo_rfq_cart_notices');

    }
}

if(!function_exists('rfq_cart_get_item_data')) {
    function rfq_cart_get_item_data($cart_item, $flat = false)
    {
        $item_data = array();

        // Variation values are shown only if they are not found in the title as of 3.0.
        // This is because variation titles display the attributes.
        if ($cart_item['data']->is_type('variation') && is_array($cart_item['variation']))
        {
            foreach ($cart_item['variation'] as $name => $value) {
               if(is_array($name))continue;


                $taxonomy = wc_attribute_taxonomy_name(str_replace('attribute_pa_', '', urldecode($name)));

                if (taxonomy_exists($taxonomy)) {
                    // If this is a term slug, get the term's nice name.
                    $term = get_term_by('slug', $value, $taxonomy);
                    if (!is_wp_error($term) && $term && $term->name) {
                        $value = $term->name;
                    }
                    $label = wc_attribute_label($taxonomy);
                } else {
                    // If this is a custom option slug, get the options name.
                    $value = apply_filters('woocommerce_variation_option_name', $value, null, $taxonomy, $cart_item['data']);
                    $label = wc_attribute_label(str_replace('attribute_', '', $name), $cart_item['data']);
                }

                // Check the nicename against the title.
                if ('' === $value || wc_is_attribute_in_product_name($value, $cart_item['data']->get_name())) {
                   // continue;
                }

                $item_data[] = array(
                    'key' => $label,
                    'value' => $value,
                );
            }
        }

        // Filter item data to allow 3rd parties to add more to the array.
        $item_data = apply_filters('woocommerce_get_item_data', $item_data, $cart_item);

        // Format item data ready to display.
        foreach ($item_data as $key => $data) {
            // Set hidden to true to not display meta on cart.
            if (!empty($data['hidden'])) {
                unset($item_data[$key]);
                continue;
            }
            $item_data[$key]['key'] = !empty($data['key']) ? $data['key'] : $data['name'];
            $item_data[$key]['display'] = !empty($data['display']) ? $data['display'] : $data['value'];
        }



        // Output flat or in list format.
        if (count($item_data) > 0) {
            ob_start();

            if ($flat) {
                foreach ($item_data as $data) {
                    echo esc_html($data['key']) . ': ' . wp_kses_post($data['display']) . "\n";
                }
            } else {
                wc_get_template('cart/cart-item-data.php',
                    array('item_data' => $item_data)
                );
            }

            return ob_get_clean();
        }

        return '';
    }

}

if(!function_exists('rfq_cart_get_item_data_old')) {
    function rfq_cart_get_item_data_old($cart_item, $flat = false)
    {
        $item_data = array();

        // Variation data
        if (isset($cart_item['data']->variation_id) && is_array($cart_item['variation']))
        {

            foreach ($cart_item['variation'] as $name => $value) {

                if ('' === $value)
                    continue;

                $taxonomy = wc_attribute_taxonomy_name(str_replace('attribute_pa_', '', urldecode($name)));

                // If this is a term slug, get the term's nice name
                if (taxonomy_exists($taxonomy)) {
                    $term = get_term_by('slug', $value, $taxonomy);
                    if (!is_wp_error($term) && $term && $term->name) {
                        $value = $term->name;
                    }
                    $label = wc_attribute_label($taxonomy);

                    // If this is a custom option slug, get the options name
                } else {
                    $value = apply_filters('woocommerce_variation_option_name', $value);
                    $label = wc_attribute_label(str_replace('attribute_', '', $name), $cart_item['data']);
                }

                $item_data[] = array(
                    'key' => $label,
                    'value' => $value
                );
            }
        }

        // Filter item data to allow 3rd parties to add more to the array
        $item_data = apply_filters('woocommerce_get_item_data', $item_data, $cart_item);



        // Format item data ready to display
        foreach ($item_data as $key => $data) {
                // Set hidden to true to not display meta on cart.
                if (isset($data['hidden'])) {
                    unset($item_data[$key]);
                    continue;
                }

                $item_data[$key]['key'] = isset($data['key']) && $data['key'] !=""  ? $data['key'] : $data['name'];
                $item_data[$key]['display'] = isset($data['display']) && $data['display'] !="" ? $data['display'] : $data['value'];
        }

        // Output flat or in list format
        if (sizeof($item_data) > 0) {
            //ob_start();

            if ($flat) {
                foreach ($item_data as $data) {

                    echo esc_html($data['key']) . ': ' . wp_kses_post($data['display'])   . "\n";
                }
            } else {
                wc_get_template('cart/cart-item-data.php',
                    array('item_data' => $item_data)
                );

                return;
            }

            //return ob_get_clean();
        }

        return '';




    }
}

add_action( 'woocommerce_before_calculate_totals','gpls_woo_rfq_remove_warnings', -1000);
add_action( 'woocommerce_remove_cart_item','gpls_woo_rfq_remove_cart_item_warnings',-1000,2  );
if (!function_exists('gpls_woo_rfq_remove_warnings')) {

    function gpls_woo_rfq_remove_warnings()
    {
        ini_set('display_errors', 'Off');


    }
}
if (!function_exists('gpls_woo_rfq_remove_cart_item_warnings')) {

    function gpls_woo_rfq_remove_cart_item_warnings($cart_item_key, $cart)
    {
        ini_set('display_errors', 'Off');


    }
}