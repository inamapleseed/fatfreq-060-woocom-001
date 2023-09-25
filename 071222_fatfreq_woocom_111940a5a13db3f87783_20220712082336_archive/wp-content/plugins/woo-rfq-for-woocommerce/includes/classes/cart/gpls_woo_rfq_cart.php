<?php

/**
 * Main class
 *
 */
if (!class_exists('gpls_woo_rfq_CART')) {

    class gpls_woo_rfq_CART
    {
        public function __construct()
        {

            $purchase_only = false;


            add_action("woocommerce_add_to_cart", "gpls_woo_rfq_woocommerce_add_to_cart", PHP_INT_MAX - 1, 6);


            add_filter('woocommerce_product_single_add_to_cart_text', array($this, 'woo_custom_cart_button_text'), 100, 2);
            add_filter('woocommerce_product_add_to_cart_text', array($this, 'woo_custom_cart_button_text'), 100, 2);
            add_filter('woocommerce_loop_add_to_cart_link', array($this, 'gpls_woo_rfq_add_to_cart_link_shop'), -1000, 2);

            add_action('woocommerce_after_add_to_cart_button', array($this, 'gpls_woo_rfq_after_add_to_cart_button'), 1000);

            //  add_action('woocommerce_before_add_to_cart_button', array($this, 'gpls_woo_rfq_before_add_to_cart_button'), 1000);


            $hook = get_option('settings_gpls_woo_rfq_normal_checkout_quote_position', 'woocommerce_after_shop_loop_item');
            add_action($hook, array($this, 'gpls_woo_rfq_after_after_shop_loop_item'), 100);

            //  add_action('woocommerce_after_shop_loop_item', array($this, 'gpls_woo_rfq_after_after_shop_loop_item'), 100);

            add_filter('woocommerce_cart_item_remove_link', 'gpls_woo_rfq_cart_item_remove_link', 100, 2);
            add_action("woocommerce_after_cart", array($this, "gpls_woo_rfq_woocommerce_after_cart"), 1000);
            add_action("woocommerce_after_cart_totals", array($this, "gpls_woo_rfq_woocommerce_after_cart"), 1000);


            $is_checkout_cart_routine = false;
            $checkout_option = $GLOBALS["gpls_woo_rfq_checkout_option"];

            if ($checkout_option === "normal_checkout" || isset($_POST["rfq_product_id"])) {

                $is_checkout_cart_routine = true;

            }

            if ($checkout_option === "rfq") {

                $is_checkout_cart_routine = false;

            }

            $is_checkout_cart_routine = apply_filters('gpls_woo_rfq_normal_cart_routine_filter', $is_checkout_cart_routine, $checkout_option, $_REQUEST);

            if ($is_checkout_cart_routine == true) {

                $this->gpls_woo_rfq_normal_checkout_cart_routine();
            }


            add_action('wp_print_footer_scripts', array($this, 'gpls_woo_rfq_ajax_add_to_quote_print_script'), 1000);


            add_action('wp_print_footer_scripts', array($this, 'gpls_woo_rfq_ajax_add_to_rfq_single_page'), 100);


        }


        public function gpls_woo_rfq_ajax_add_to_rfq_single_page()
        {

            global $wp_query;
            if (isset($wp_query) && function_exists('is_product')) {

                if (!is_product()) {
                    return;
                }
            }

            //    $is_ajax = get_option('settings_gpls_woo_rfq_product_page_ajax', 'no');

            //    if ($is_ajax=="no") return;


            global $product;


            if (!is_object($product) && !function_exists('wc_get_product')) return;

            if (!is_object($product)) $product = wc_get_product(get_the_ID());

            if (!isset($product) || !is_object($product)) {
                return;
            }

            if ($product->get_type() == 'external') {
                return;
            }


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

            if (function_exists('wp_get_current_user')) {
                if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') == 'yes' && !wp_get_current_user()->exists()) {
                    $rfq_check = true;
                    $normal_check = false;

                }
            }


            ?>


            <script type="application/javascript">

                jQuery(".gpls_rfq_set").click(function (e) {

                    jQuery(window).ajaxComplete(function (event, xhr, settings) {

                        var called = false;

                        if (settings.url == '/?wc-ajax=add_to_cart' && called == false) {

                            //  alert(settings.url);
                            called = true;

                            <?php if ($normal_check == true && get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') != 'yes'): ?>

                            <?php

                            if(get_transient('redirect_to_quote_request') == 'yes')
                            {
                            $url_new = get_option('rfq_cart_sc_section_show_link_to_rfq_page', '');
                            ?>
                            window.location.replace('<?php echo $url_new ?>');
                            return;
                            <?php
                            }
                            ?>

                            jQuery(".gpls_rfq_set_div").find('.added_to_cart').hide();
                            jQuery(".gpls_rfq_set_div").find('.added_to_cart').attr('style', 'display: none !important');

                            <?php endif; ?>

                            var rfqcart_link = ".rfqcart-link";
                            jQuery(rfqcart_link).show();
                            jQuery("rfqcart_link").attr('style', 'display: block');


                            <?php if(get_transient('rfq_page_plus_Widget') == "yes"): ?>

                            jQuery.ajax({
                                async: true,
                                type: 'GET',
                                url: '/?rfq_widget2=0',
                                error: function (xhr, status, error) {
                                    console.log(error.Message);
                                },
                                success:
                                    function (msg) {

                                        jQuery('.gpls_woo_rfq_request_mini_page').html(msg);

                                    }
                            });
                            <?php endif; ?>

                        }
                    });

                });

            </script>


            <?php
        }


        public function gpls_woo_rfq_ajax_add_to_quote_print_script()
        {


            $home = home_url() . '/quote-request/';

            $rfq_page = get_option('rfq_cart_sc_section_show_link_to_rfq_page', $home);

            $actual_link = get_site_url() . $_SERVER['REQUEST_URI'];

            if (parse_url(trim($rfq_page))['path'] === parse_url(trim($actual_link))['path']) {
                return;
            }


            if ('yes' !== get_option('woocommerce_enable_ajax_add_to_cart')) return;

            //   if (is_product() || is_admin()) return;

            global $product;

            if (!is_object($product) && !function_exists('wc_get_product')) return;

            if (!is_object($product)) $product = wc_get_product(get_the_ID());

            if (!isset($product) || !is_object($product)) {
                //  return;
            }

            do_action('gpls_woo_rfq_before_ajax_add_to_quote', $product);

            $return = "no";

            $return = apply_filters('gpls_woo_rfq_skip_ajax_add_to_quote', $return);

            if ($return === "yes") {
                //  return;
            }
            $link_to_rfq_page = pls_woo_rfq_get_link_to_rfq();
            ob_start();
            wc_get_template('woo-rfq/link-to-cart.php',
                array('link_to_rfq_page' => $link_to_rfq_page,
                ), '', gpls_woo_rfq_WOO_PATH);
            $result = ob_get_clean();
            ?>
            <script type="application/javascript">

                function handle_long_str(str) {
                    var lines = str.split(/\n/);
                    var output = [];
                    var outputText = [];
                    for (var i = 0; i < lines.length; i++) {

                        if (/\S/.test(lines[i])) {
                            outputText.push('"' + $.trim(lines[i]) + '"');
                            output.push($.trim(lines[i]));
                        }
                    }
                    return outputText;
                }


                jQuery(window).on("load",function () {
                    var image_div;

                    jQuery(".woo_rfq_after_shop_loop_button").submit(function (e) {

                        var form = jQuery(this); //wrap this in jQuery
                        var is_var = jQuery(form).find('input[name="rfq_var"]').val();
                        if (is_var === "yes") {
                            //    return true;
                        }

                        e.preventDefault();

                        var rfq_button_id = "#rfq_button_" + jQuery(form).data('rfq-product-id');
                        var rfq_qty_id = "#quantity_" + jQuery(form).data('rfq-product-id');//new

                        image_div = "#image_" + jQuery(form).data('rfq-product-id');
                        jQuery(image_div).show();
                        var str = jQuery(this).serialize();


                        jQuery.ajax({
                            type: "POST",
                            url: form.attr('action'),
                            data: str,
                            success: function (msg) {
                                <?php
                                $link_to_rfq_page = pls_woo_rfq_get_link_to_rfq();

                                ?>
                                if (typeof msg.data !== 'undefined' && typeof msg.data.location !== 'undefined') {

                                    jQuery(image_div).hide();
                                    window.location.replace(msg.data.location);
                                    return;

                                } else {

                                    var note_id = "#note_" + jQuery(form).data('rfq-product-id');

                                    jQuery(note_id).html('<?php echo $result ?>');

                                    jQuery(image_div).hide();
                                    jQuery(rfq_button_id).addClass('gpls_hidden');
                                    jQuery(rfq_qty_id).addClass('gpls_hidden');

                                    <?php if(get_transient('rfq_page_plus_Widget') === "yes"): ?>

                                    jQuery.ajax({
                                        async: true,
                                        type: 'GET',
                                        url: '/?rfq_widget=0',
                                        error: function (xhr, status, error) {
                                            console.log(error.Message);
                                        },
                                        success: function (msg_back) {

                                            jQuery('.gpls_woo_rfq_request_mini_page').html(msg_back);

                                        }
                                    });

                                    <?php endif; ?>


                                }


                            }
                        });

                    });

                    jQuery(image_div).hide();


                });
            </script>


            <?php


            do_action('gpls_woo_rfq_after_ajax_add_to_quote', $product);




        }

        public function gpls_woo_rfq_normal_checkout_cart_routine()
        {
            //  if (!is_admin())
            {
                /*    */

                // gpls_woo_rfq_remove_filters_normal_checkout();
                if (!is_admin()) {
                    //    add_action('init', 'gpls_woo_rfq_print_script_init', 1000);
                }

                add_filter('woocommerce_add_cart_item_data', array($this, 'gpls_woo_rfq_add_cart_item_data'), 1000, 3);
                add_action("gpls_woo_rfq_before_cart", array($this, "gpls_woo_rfq_cart_before_cart"), 1000);
                add_filter('woocommerce_widget_cart_is_hidden', array($this, 'filter_woocommerce_widget_cart_is_hidden'), 1000, 1);


            }
        }


        public function filter_woocommerce_widget_cart_is_hidden($is_cart)
        {

            $link_to_rfq_page = pls_woo_rfq_get_link_to_rfq();

            $link_to_rfq_page = (trim(preg_replace('{/$}', '', $link_to_rfq_page)));

            $current_page = (trim(preg_replace('{/$}', '', (get_site_url()) . $_SERVER['REQUEST_URI'])));

            if (trim($link_to_rfq_page) == trim($current_page) || isset($_REQUEST['removed_item'])) {

                $is_cart = true;
            }

            return $is_cart;


        }

        public function gpls_woo_rfq_cart_before_cart()
        {
            $gpls_woo_rfq_cart = gpls_woo_rfq_get_item(gpls_woo_rfq_cart_tran_key() . '_' . 'gpls_woo_rfq_cart');
        }


        public function gpls_woo_rfq_woocommerce_cart_is_empty()
        {


        }

        public function gpls_woo_rfq_woocommerce_before_cart()
        {


        }


        public function gpls_woo_rfq_woocommerce_after_mini_cart()
        {
        }

        public function gpls_woo_rfq_woocommerce_before_mini_cart()
        {


        }

        public function gpls_woo_rfq_woocommerce_after_cart()
        {
            $rfq_check = false;
            $normal_check = false;

            if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') === "rfq") {

                $rfq_check = true;
                $normal_check = false;
            }

            if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') === "normal_checkout") {
                $rfq_check = false;
                $normal_check = true;
            }

            if (function_exists('wp_get_current_user')) {
                if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') === 'yes' && !wp_get_current_user()->exists()) {
                    $rfq_check = true;
                    $normal_check = false;

                }
            }


            if ($rfq_check && get_option('settings_gpls_woo_rfq_show_prices', 'no') === 'no') {

                $rfq_product_script = "<script>jQuery( document ).ready( function() { jQuery( '.tax-rate' ).hide(); jQuery( '.cart-subtotal' ).hide(); jQuery( '.order-total' ).hide();jQuery( '.tax-total' ).hide();} ); </script>";
            } else {
                $rfq_product_script = '';
            }

            echo $rfq_product_script;


        }


        public function gpls_woo_rfq_woocommerce_cart_item_visible($visible, $cart_item, $cart_item_key)
        {
            // echo 'gpls_woo_rfq_woocommerce_cart_item_visible';
            if (isset($cart_item['rfq'])) {
                if ($cart_item['rfq'] === 'yes') {
                    $visible = false;
                }
            }

            return $visible;

        }


        public function gpls_woo_rfq_add_cart_item_data($cart_item_data, $product_id, $variation_id)
        {
            //echo 'gpls_woo_rfq_add_cart_item_data';

            $checkout_option = $GLOBALS["gpls_woo_rfq_checkout_option"];

            if ($checkout_option == "rfq") {
                return;
            }
            //echo $_REQUEST["rfq_product_id"].'<br />';
            $is_an_rfq = false;

            $rfq_enable = get_post_meta($product_id, '_gpls_woo_rfq_rfq_enable', true);
            $rfq_enable = apply_filters('gpls_rfq_enable', $rfq_enable, $product_id);

            if ($rfq_enable == 'yes' && isset($_REQUEST["rfq_product_id"])) {
                $is_an_rfq = true;
            }

            if (isset($_REQUEST['action'])) {
                if ($_REQUEST['action'] == "add_vpc_configuration_to_cart") {
                    //$is_an_rfq_true_false = true;
                }
            }


            $is_an_rfq = apply_filters('gpls_woo_rfq_is_an_rfq_add_cart_item_data', $is_an_rfq, $_REQUEST, $cart_item_data, $product_id, $variation_id, $rfq_enable);

            if ($is_an_rfq) {
                $cart_item_data['rfq'] = 'yes';
                $cart_item_data['restore'] = 'no';
                $cart_item_data['man_deleted'] = 'no';
            } else {
                $cart_item_data['rfq'] = 'no';
                $cart_item_data['restore'] = 'no';
                $cart_item_data['man_deleted'] = 'no';
            };


            return $cart_item_data;

        }


        public function gpls_woo_rfq_remove_rfq_cart_item()
        {
        }


        public function gpls_woo_rfq_after_after_shop_loop_item()
        {


            global $product;
            // WC()->init();
            if (!is_object($product) && !function_exists('wc_get_product')) return;
            if (!is_object($product)) $product = wc_get_product(get_the_ID());
            // if (!is_object($product)) $product = WC()->product_factory->get_product(get_the_ID());

            if (!isset($product) || !is_object($product)) {
                return;
            }

            if ($product->get_type() == 'external') {
                return;
            }

            $hide_button = apply_filters('gplsrfq_hide_after_shop_loop_item', false, $product);

            if ($hide_button) {
                return;
            }


            if (function_exists('wp_get_current_user')) {
                if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') == 'yes' && !wp_get_current_user()->exists()
                    && get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "normal_checkout"
                ) {
                    return;
                }
            }


            $data_var = "no";
            if ($product->get_type() == 'variable') {
                $data_var = "yes";
            }


            $form_label = gpls_woo_rfq_INQUIRE_TEXT;

            $rfq_product_script = "";

            $rfq_enable = get_post_meta($product->get_id(), '_gpls_woo_rfq_rfq_enable', true);
            $rfq_enable = apply_filters('gpls_rfq_enable', $rfq_enable, $product->get_id());


            if ($rfq_enable != 'yes' && $GLOBALS["gpls_woo_rfq_checkout_option"] != "rfq") {

                if ('yes' == get_option('woocommerce_manage_stock') && $product->get_stock_status() != 'instock') {

                    $request_quote = __('Read more', 'woo-rfq-for-woocommerce', '');// "Request Quote"
                    $read_more = get_option('settings_gpls_woo_rfq_read_more', '');// "Request Quote"
                    $read_more = __($read_more, 'woo-rfq-for-woocommerce');

                    if ($read_more != "") {
                        $request_quote = $read_more;// "Request Quote"
                    }

                    $request_quote = apply_filters('gpls_woo_rfq_out_of_stock_text', $request_quote);
                }

                global $rfq_cart;

                global $rfq_variations;

                $in_rfq = false;

                $gpls_woo_rfq_cart = gpls_woo_rfq_get_item(gpls_woo_rfq_cart_tran_key() . '_' . 'gpls_woo_rfq_cart');

                if (($gpls_woo_rfq_cart != false)) {

                    foreach ($gpls_woo_rfq_cart as $cart_item_key => $values) {

                        if (isset($values['product_id'])) {
                            $product_id = $values['product_id'];


                            if (get_the_ID() == $product_id && $values['rfq'] == "yes" && $values['restore'] == 'yes') {
                                $in_rfq = true;
                            }
                        }
                    }

                }

                if (($in_rfq == true && $product->get_type() != 'variable' && $product->get_type() != 'bundle')) {

                    $request_quote = get_option('rfq_cart_wordings_in_rfq', __('In Quote List', 'woo-rfq-for-woocommerce'));//"In RFQ"
                    $request_quote = __($request_quote, 'woo-rfq-for-woocommerce');

                    $request_quote = apply_filters('gpls_woo_rfq_in_rfq_text', $request_quote);

                } else {
                    $request_quote = get_option('rfq_cart_wordings_add_to_rfq', __('Add To Quote', 'woo-rfq-for-woocommerce'));// "Request Quote"
                    $request_quote = __($request_quote, 'woo-rfq-for-woocommerce');

                    $request_quote = apply_filters('gpls_woo_rfq_request_quote_text', $request_quote);
                }


                $link_to_rfq_page = pls_woo_rfq_get_link_to_rfq();

                //$view_your_cart_text = get_option('rfq_cart_wordings_view_rfq_cart', __('View List', 'woo-rfq-for-woocommerce'));
                //$view_your_cart_text = __($view_your_cart_text, 'woo-rfq-for-woocommerce');


                ?>

                <?php if (($in_rfq == true) && isset($link_to_rfq_page)) : ?>
                    <?php

                    //echo <<< eod
//<div style="display: block"><a  class="link_to_rfq_page_link"  href="{$link_to_rfq_page}" >&nbsp;{$view_your_cart_text}&nbsp;</a></div>
//eod;
                    $link_to_rfq_page = pls_woo_rfq_get_link_to_rfq();
                    wc_get_template('woo-rfq/link-to-cart.php',
                        array('link_to_rfq_page' => $link_to_rfq_page,
                        ), '', gpls_woo_rfq_WOO_PATH);


                    ?>
                <?php endif; ?>

                <?php
            }


            ?>
            <?php if ($rfq_enable == 'yes' && $GLOBALS["gpls_woo_rfq_checkout_option"] == "normal_checkout") : ?>

            <?php

            $rfq_check = false;
            $normal_check = false;

            if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "rfq") {

                $rfq_check = true;
                $normal_check = false;
            }

            if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "normal_checkout") {
                $rfq_check = false;
                $normal_check = true;
            }

            if (function_exists('wp_get_current_user')) {
                if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') == 'yes' && !wp_get_current_user()->exists()) {
                    $rfq_check = true;
                    $normal_check = false;

                }
            }
            if ($rfq_check) {
                $rfq_product_script = "";
            }


            $rfq_id = $product->get_id();

            global $rfq_cart;

            global $rfq_variations;

            $in_rfq = false;

            $gpls_woo_rfq_cart = gpls_woo_rfq_get_item(gpls_woo_rfq_cart_tran_key() . '_' . 'gpls_woo_rfq_cart');

            if (($gpls_woo_rfq_cart != false)) {

                foreach ($gpls_woo_rfq_cart as $cart_item_key => $values) {

                    if (isset($values['product_id'])) {
                        $product_id = $values['product_id'];


                        if (get_the_ID() == $product_id && $values['rfq'] == "yes" && $values['restore'] == 'yes') {
                            $in_rfq = true;
                        }
                    }
                }

            }

            if (($in_rfq == true && $product->get_type() != 'variable' && $product->get_type() != 'bundle')) {

                $request_quote = get_option('rfq_cart_wordings_in_rfq', __('In Quote List', 'woo-rfq-for-woocommerce'));//"In RFQ"
                $request_quote = __($request_quote, 'woo-rfq-for-woocommerce');

                $request_quote = apply_filters('gpls_woo_rfq_in_rfq_text', $request_quote);

            } else {
                $request_quote = get_option('rfq_cart_wordings_add_to_rfq', __('Add To Quote', 'woo-rfq-for-woocommerce'));// "Request Quote"
                $request_quote = __($request_quote, 'woo-rfq-for-woocommerce');

                $request_quote = apply_filters('gpls_woo_rfq_request_quote_text', $request_quote);
            }


            $no_add_to_cart = 'no';
            $no_add_to_cart = apply_filters('gpls_woo_rfq_no_add_to_cart', $no_add_to_cart, $product, $normal_check);

            if ($normal_check == true) {

                if (($in_rfq == false && $product->get_type() == 'simple')) {
                    //return;
                }

                if (($in_rfq == false && $product->get_type() == 'variable')) {

                    if (get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') == 'yes' && $no_add_to_cart == 'no') {
                        return;
                    }

                    if (get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') == 'yes' && $no_add_to_cart == 'yes') {
                        //  return;
                        $request_quote = __('Select options', 'woo-rfq-for-woocommerce');//"In RFQ"
                        $select_options = get_option('settings_gpls_woo_rfq_Select_Options', $request_quote);// "Request Quote"
                        $select_options = __($select_options, 'woo-rfq-for-woocommerce');

                        if ($select_options != "") {
                            $request_quote = $select_options;// "Request Quote"
                        }

                        $request_quote = apply_filters('gpls_woo_rfq_in_rfq_text', $request_quote);
                    }


                    if (get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') == 'no' && $no_add_to_cart == 'no') {
                        //  return;
                        $request_quote = __('Select options', 'woo-rfq-for-woocommerce');//"In RFQ"
                        $select_options = get_option('settings_gpls_woo_rfq_Select_Options', $request_quote);// "Request Quote"
                        $select_options = __($select_options, 'woo-rfq-for-woocommerce');

                        if ($select_options != "") {
                            $request_quote = $select_options;// "Request Quote"
                        }

                        $request_quote = apply_filters('gpls_woo_rfq_in_rfq_text', $request_quote);


                    }

                    if (get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') == 'no' && $no_add_to_cart == 'yes') {
                        //  return;
                        $request_quote = __('Select options', 'woo-rfq-for-woocommerce');//"In RFQ"
                        $select_options = get_option('settings_gpls_woo_rfq_Select_Options', $request_quote);// "Request Quote"
                        $select_options = __($select_options, 'woo-rfq-for-woocommerce');

                        if ($select_options != "") {
                            $request_quote = $select_options;// "Request Quote"
                        }

                        $request_quote = apply_filters('gpls_woo_rfq_in_rfq_text', $request_quote);


                    }


                }
            }


            $link_to_rfq_page = pls_woo_rfq_get_link_to_rfq();


            $proceed = apply_filters('gpls_woo_rfq_after_after_shop_loop_item_proceed', true);

            if ('yes' == get_option('woocommerce_manage_stock') && $product->get_stock_status() != 'instock') {

                $request_quote = __('Read more', 'woo-rfq-for-woocommerce', '');// "Request Quote"
                $read_more = get_option('settings_gpls_woo_rfq_read_more', '');// "Request Quote"
                $read_more = __($read_more, 'woo-rfq-for-woocommerce');

                if ($read_more != "") {
                    $request_quote = $read_more;// "Request Quote"
                }

                $request_quote = apply_filters('gpls_woo_rfq_out_of_stock_text', $request_quote);
            }
            ?>

            <?php if ($in_rfq == false)  : ?>


                <?php if ($proceed == true) : ?>

                    <?php
                    $gpls_woo_rfq_file_add_to_quote_styles = array();
                    $gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_styles'] = '';
                    $gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_onmouseover'] = '';
                    $gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_background_onmouseover'] = '';
                    $gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_onmouseout'] = '';
                    $gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_background_onmouseout'] = '';

                    $gpls_woo_rfq_file_add_to_quote_styles = apply_filters('gpls_woo_rfq_add_to_quote_styles', $gpls_woo_rfq_file_add_to_quote_styles);

                    wc_get_template('woo-rfq/add-to-quote.php',
                        array('rfq_id' => $rfq_id,
                            'product' => $product,
                            'rfq_check' => $rfq_check,
                            'data_var' => $data_var,
                            'request_quote' => $request_quote,
                            'gpls_woo_rfq_file_add_to_quote_styles' => $gpls_woo_rfq_file_add_to_quote_styles,
                        ), '', gpls_woo_rfq_WOO_PATH);
                    ?>


                <?php endif; ?>

            <?php elseif (($in_rfq == true) && isset($link_to_rfq_page)) : ?>
                <?php

                /*  echo <<< eod
  <div style="display: block"><a  class="link_to_rfq_page_link" href="{$link_to_rfq_page}" >&nbsp;{$view_your_cart_text}&nbsp;</a></div>
  eod;*/
                wc_get_template('woo-rfq/link-to-cart.php',
                    array('link_to_rfq_page' => $link_to_rfq_page,
                    ), '', gpls_woo_rfq_WOO_PATH);


                ?>
            <?php endif; ?>


        <?php endif; ?>

            <?php
        }


        public function gpls_woo_rfq_before_add_to_cart_button()
        {


            global $product;
            //WC()->init();
            if (!is_object($product) && !function_exists('wc_get_product')) return;
            if (!is_object($product)) $product = wc_get_product(get_the_ID());
            // if (!is_object($product)) $product = WC()->product_factory->get_product(get_the_ID());

            if (!isset($product) || !is_object($product)) {
                return;
            }

            if ($product->get_type() == 'external') {
                return;
            }

            $rfq_enable = get_post_meta($product->get_id(), '_gpls_woo_rfq_rfq_enable', true);
            $rfq_enable = apply_filters('gpls_rfq_enable', $rfq_enable, $product->get_id());

            if (!is_admin()) {


                $rfq_check = false;
                $normal_check = false;
                //gpls_woo_rfq_get_mode($rfq_check, $normal_check);
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

                if (function_exists('wp_get_current_user')) {
                    if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') == 'yes' && !wp_get_current_user()->exists()) {
                        $rfq_check = true;
                        $normal_check = false;

                    }
                }


                if ($rfq_check == false) {

                    if ($rfq_enable == 'no' && $product->get_price() == 0) {

                        //   exit();
                    }


                }

            }


        }


        public function gpls_woo_rfq_after_add_to_cart_button()
        {




            global $product;
            //  WC()->init();
            if (!is_object($product) && !function_exists('wc_get_product')) return;
            if (!is_object($product)) $product = wc_get_product(get_the_ID());
            // if (!is_object($product)) $product = WC()->product_factory->get_product(get_the_ID());

            if (!isset($product) || !is_object($product)) {
                return;
            }

            if ($product->get_type() == 'external') {
                return;
            }

            $rfq_enable = get_post_meta($product->get_id(), '_gpls_woo_rfq_rfq_enable', true);
            $rfq_enable = apply_filters('gpls_rfq_enable', $rfq_enable, $product->get_id());

            $form_label = gpls_woo_rfq_INQUIRE_TEXT;

            $rfq_product_script = "";

            $rfq_enable = get_post_meta($product->get_id(), '_gpls_woo_rfq_rfq_enable', true);
            $rfq_enable = apply_filters('gpls_rfq_enable', $rfq_enable, $product->get_id());

            $rfq_check = false;
            $normal_check = false;
            //gpls_woo_rfq_get_mode($rfq_check, $normal_check);
            $rfq_check = false;
            $normal_check = false;

            if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "rfq") {
                add_filter('woocommerce_cart_needs_payment', 'gpls_woo_rfq_cart_needs_payment', 1000, 2);

                $rfq_check = true;
                $normal_check = false;

                if (get_option('settings_gpls_woo_rfq_show_prices', 'no') == 'yes') {
                    $rfq_check = false;
                    $normal_check = true;
                }
            }

            if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "normal_checkout") {
                $rfq_check = false;
                $normal_check = true;
            }

            if (function_exists('wp_get_current_user')) {
                if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') == 'yes' && !wp_get_current_user()->exists()) {
                    $rfq_check = true;
                    $normal_check = false;

                }
            }

            ?>


            <?php if ($rfq_enable == 'yes' && $GLOBALS["gpls_woo_rfq_checkout_option"] != "rfq") : ?>

            <?php
            if (($normal_check && get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') == 'no')) {

                if (!is_admin()) {
                    add_action('wp_print_footer_scripts', 'gpls_woo_rfq_print_script', 1000);
                    add_action('wp_add_inline_script', 'gpls_woo_rfq_print_script', 1000);
                    add_action('wp_enqueue_script', 'gpls_woo_rfq_print_script', 1000);

                    $rfq_product_script = "<script>jQuery(document ).ready( function() { jQuery( '.single_add_to_cart_button' ).hide();jQuery( '.single_add_to_cart_button' ).attr('style','display: none !important');
jQuery( '.gpls_rfq_set' ).show();jQuery( '.gpls_rfq_set' ).attr('style','display: inline-block !important');
jQuery( '.amount,.bundle_price' ).hide();jQuery( '.amount,.bundle_price' ).attr('style','display: none !important');


} ); </script>";


                    echo $rfq_product_script;

                    $rfq_product_script = "<script>jQuery(document ).ready( function() {
jQuery( '.woocommerce-Price-amount,.from, .price,.total, .bundle_price,.wc-pao-col2,.wc-pao-subtotal-line, .product-selector__price' ).hide();
jQuery( '.woocommerce-Price-amount,.from, .price,.total, .bundle_price,.wc-pao-col2,.wc-pao-subtotal-line, .product-selector__price' ).attr('style','display: none !important'); 

} ); </script>";
                    echo $rfq_product_script;
                }
            }


            $rfq_id = $product->get_id();

            global $rfq_cart;

            global $rfq_variations;

            $in_rfq = false;


            $gpls_woo_rfq_cart = gpls_woo_rfq_get_item(gpls_woo_rfq_cart_tran_key() . '_' . 'gpls_woo_rfq_cart');

            if (($gpls_woo_rfq_cart != false)) {

                foreach ($gpls_woo_rfq_cart as $cart_item_key => $values) {

                    if (isset($values['product_id'])) {
                        $product_id = $values['product_id'];


                        if (get_the_ID() == $product_id && $values['rfq'] == "yes" && $values['restore'] == 'yes') {
                            $in_rfq = true;
                        }
                    }
                }

            }

            if (($in_rfq == true && $product->get_type() != 'variable' && $product->get_type() != 'bundle')) {

                $request_quote = get_option('rfq_cart_wordings_in_rfq', __('Add To Quote', 'woo-rfq-for-woocommerce'));//"In RFQ"
                $request_quote = __($request_quote, 'woo-rfq-for-woocommerce');
                $request_quote = apply_filters('gpls_woo_rfq_in_rfq_text', $request_quote);

            } else {
                $request_quote = get_option('rfq_cart_wordings_add_to_rfq', __('Add To Quote', 'woo-rfq-for-woocommerce'));// "Request Quote"
                $request_quote = __($request_quote, 'woo-rfq-for-woocommerce');

                $request_quote = apply_filters('gpls_woo_rfq_request_quote_text', $request_quote);

            }


            $link_to_rfq_page = pls_woo_rfq_get_link_to_rfq();

            $view_your_cart_text = get_option('rfq_cart_wordings_view_rfq_cart', __('View List', 'woo-rfq-for-woocommerce'));
            $view_your_cart_text = __($view_your_cart_text, 'woo-rfq-for-woocommerce');
            $view_your_cart_text = apply_filters('gpls_woo_rfq_request_quote_text', $view_your_cart_text);

            $gpls_woo_rfq_file_add_to_quote_styles = array();
            $gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_styles'] = '';
            $gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_onmouseover'] = '';
            $gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_background_onmouseover'] = '';
            $gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_onmouseout'] = '';
            $gpls_woo_rfq_file_add_to_quote_styles['gpls_woo_rfq_page_button_background_onmouseout'] = '';

            $gpls_woo_rfq_file_add_to_quote_styles = apply_filters('gpls_woo_rfq_add_to_quote_styles', $gpls_woo_rfq_file_add_to_quote_styles);

            wc_get_template('woo-rfq/add-to-quote-single.php',
                array('rfq_product_script' => $rfq_product_script,
                    'product' => $product,
                    'in_rfq' => $in_rfq,
                    'rfq_check' => $rfq_check,
                    'normal_check' => $normal_check,
                    'request_quote' => $request_quote,
                    'view_your_cart_text' => $view_your_cart_text,
                    'rfq_enable' => $rfq_enable,
                    'link_to_rfq_page' => $link_to_rfq_page,
                    'gpls_woo_rfq_file_add_to_quote_styles' => $gpls_woo_rfq_file_add_to_quote_styles,
                ), '', gpls_woo_rfq_WOO_PATH);
            ?>


        <?php else: ?>
            <?php
            if (!is_admin()) {
                add_action('wp_print_footer_scripts', 'gpls_woo_rfq_print_script_show_single_add', 1000);


                $rfq_product_script = "<script>jQuery(document ).ready( function() { 
    jQuery( '.single_add_to_cart_button' ).show();
    jQuery( '.single_add_to_cart_button' ).attr('style','display: inline-block !important');
jQuery('.single_add_to_cart_button').prop('disabled',false);;
                 jQuery('.gpls_rfq_set').prop('disabled', false);
    }); </script>";


                echo $rfq_product_script;
            }
            ?>
        <?php endif; ?>

            <?php

            if ($rfq_check) {
                //  $rfq_product_script = "";
                if (get_option('settings_gpls_woo_rfq_show_prices', 'no') == "no") {

                    if (!is_admin()) {

                        add_action('wp_print_footer_scripts', 'gpls_woo_rfq_print_script', 1000);
                        add_action('wp_add_inline_script', 'gpls_woo_rfq_print_script', 1000);
                        add_action('wp_enqueue_script', 'gpls_woo_rfq_print_script', 1000);

                        $rfq_product_script = "<script>jQuery(document ).ready( function() { jQuery( '.amount,.bundle_price, .product-selector__price' ).hide();
jQuery( '.amount,.bundle_price, .product-selector__price' ).attr('style','display: none !important');
            } ); </script>";
                        echo $rfq_product_script;

                        $rfq_product_script = "<script>jQuery(document ).ready( function() {
jQuery( '.woocommerce-Price-amount,.from, .price,.total, .bundle_price,.wc-pao-col2,.wc-pao-subtotal-line, .product-selector__price' ).hide();
jQuery( '.woocommerce-Price-amount,.from, .price,.total, .bundle_price,.wc-pao-col2,.wc-pao-subtotal-line, .product-selector__price' ).attr('style','display: none !important');
 

} ); </script>";
                        echo $rfq_product_script;
                    }


                }
            }

            if ($normal_check) {

                if (function_exists('wp_get_current_user')) {
                    if (get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') == 'yes'

                        && !(get_option('settings_gpls_woo_rfq_hide_visitor_prices_normal', 'no') == 'yes' && !wp_get_current_user()->exists())) {
                        if (!is_admin()) {

                            {
                                $rfq_product_script = "<script>jQuery(document ).ready( function() { jQuery( '.single_add_to_cart_button' ).show();
jQuery( '.single_add_to_cart_button' ).attr('style','display: inline-block !important');
jQuery('.single_add_to_cart_button').prop('disabled',false);;
                 jQuery('.gpls_rfq_set').prop('disabled', false);

} ); </script>";
                                echo $rfq_product_script;


                                add_action('wp_print_footer_scripts', 'gpls_woo_rfq_print_script_show_single_add', 1000);

                            }


                        }


                    }
                }
            }

        }


        public function gpls_woo_rfq_add_to_cart_link_shop($link, $product)
        {




            if ($product->get_type() === 'external') {
                return $link;
            }

            $read_more = "";
            //  global $product;

            $data = $product->get_data();

            $this_price = $data["price"];

            if (trim($data["sale_price"]) != '') {
                $this_price = $data["sale_price"];
            }

            $rfq_enable = get_post_meta($product->get_id(), '_gpls_woo_rfq_rfq_enable', true);
            $rfq_enable = apply_filters('gpls_rfq_enable', $rfq_enable, $product->get_id());


            $form_label = gpls_woo_rfq_INQUIRE_TEXT;

            $rfq_product_script = "";


            $rfq_check = false;
            $normal_check = false;
            //gpls_woo_rfq_get_mode($rfq_check, $normal_check);
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
            if (function_exists('wp_get_current_user')) {
                if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') == 'yes' && !wp_get_current_user()->exists()) {
                    $rfq_check = true;
                    $normal_check = false;

                }
            }


            $pf = new WC_Product_Factory();
            $product = $pf->get_product($product->get_id());


            if ($rfq_enable == 'yes') {

                if (($GLOBALS["gpls_woo_rfq_checkout_option"] == "normal_checkout"
                    && get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') == "no")
                ) {
                    return;
                }

            }

            if (($GLOBALS["gpls_woo_rfq_checkout_option"] == "normal_checkout"
            )
            ) {

                if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') == 'yes'

                ) {
                    $pf = new WC_Product_Factory();

                    $product = $pf->get_product($product->get_id());

                    $rfq_enable = get_post_meta($product->get_id(), '_gpls_woo_rfq_rfq_enable', true);
                    $rfq_enable = apply_filters('gpls_rfq_enable', $rfq_enable, $product->get_id());

                    //echo $product->id.' '.$rfq_enable.'<br />';
                    if ($rfq_enable == 'no') {

                        return $link = apply_filters("gplsrfq_add_to_cart_link_shop", $link, $product);

                    }

                    if ($rfq_enable == "yes") {
                        $data = $product->get_data();

                        $this_price = $data["price"];

                        if (trim($data["sale_price"]) != '') {
                            $this_price = $data["sale_price"];
                        }
                        $type = $product->get_type();
                        // if ($type == 'simple' || $type == 'variable')
                        {
                            if (trim($this_price) === '') {

                                /*  $request_quote = __('Read more', 'woo-rfq-for-woocommerce', '');// "Request Quote"
                                  $request_quote = get_option('settings_gpls_woo_rfq_read_more', $request_quote);// "Request Quote"
                                  $request_quote = __($request_quote, 'woo-rfq-for-woocommerce');


                                  $id = $product->get_id();
                                  $sku = $product->get_sku();
                                  //$url=esc_url($product->add_to_cart_url());
                                  $url = esc_url($product->get_permalink());


                                  $link = '<a rel="nofollow" href="' . $url . '" data-product_id="' . $id . '" data-product_sku="' . $sku . '" class="button product_type_simple ajax_add_to_cart">' . $request_quote . '</a>';

                                  return $link = apply_filters("gplsrfq_add_to_cart_link_shop", $link, $product);;*/


                            }
                        }
                        return $link = apply_filters("gplsrfq_add_to_cart_link_shop", $link, $product);;
                    }
                }


                if ($rfq_enable == 'yes') {

                    if (($GLOBALS["gpls_woo_rfq_checkout_option"] == "normal_checkout"
                        && get_option('settings_gpls_woo_rfq_normal_checkout_show_prices', 'no') == "yes")
                    ) {

                        $type = $product->get_type();
                        //if ($type == 'simple' || $type == 'variable')
                        {
                            if (trim($this_price) === '') {

                                /* $request_quote = __('Read more', 'woo-rfq-for-woocommerce', '');// "Request Quote"
                                 $read_more = get_option('settings_gpls_woo_rfq_read_more', '');// "Request Quote"

                                 if ($read_more != "") {
                                     $request_quote = $read_more;// "Request Quote"
                                 }
                                 $id = $product->get_id();
                                 $sku = $product->get_sku();
                                 //$url=esc_url($product->add_to_cart_url());
                                 $url = esc_url($product->get_permalink());
                                 $link = '<a rel="nofollow" href="' . $url . '" data-product_id="' . $id . '" data-product_sku="' . $sku . '" class="button product_type_simple ajax_add_to_cart">' . $request_quote . '</a>';

                                 return $link = apply_filters("gplsrfq_add_to_cart_link_shop", $link, $product);*/

                            }
                        }


                    }
                    return $link = apply_filters("gplsrfq_add_to_cart_link_shop", $link, $product);
                }


            }

            if ($rfq_check == true) {

                $GLOBALS["gpls_woo_rfq_checkout_option"] = "rfq";

            }

            if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "rfq"
                && get_option('settings_gpls_woo_rfq_show_prices', 'no') == "yes"
                && get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') == 'yes'
            ) {


                return $link = apply_filters("gplsrfq_add_to_cart_link_shop", $link, $product);
            }


            if ($GLOBALS["gpls_woo_rfq_checkout_option"] == "rfq"
                && get_option('settings_gpls_woo_rfq_show_prices', 'no') == "yes"

            ) {


                return $link = apply_filters("gplsrfq_add_to_cart_link_shop", $link, $product);
            }

            if ($GLOBALS["gpls_woo_rfq_checkout_option"] == "normal_checkout" && $rfq_enable != 'yes') {

                return $link = apply_filters("gplsrfq_add_to_cart_link_shop", $link, $product);

            }


            if ($GLOBALS["gpls_woo_rfq_checkout_option"] == "rfq" && get_option('settings_gpls_woo_rfq_show_prices', 'no') == "no") {

                return $link = apply_filters("gplsrfq_add_to_cart_link_shop", $link, $product);
            }

            if ($GLOBALS["gpls_woo_rfq_checkout_option"] == "rfq" && get_option('settings_gpls_woo_rfq_show_prices', 'no') == "yes"
                && get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') == 'yes'
            ) {
                return $link = apply_filters("gplsrfq_add_to_cart_link_shop", $link, $product);
            }


        }


        public function woo_custom_cart_button_text($product_add_to_cart_text, $product)
        {

//global $product;
            if ($product->get_type() == 'external') {
                return $product_add_to_cart_text;
            }


            $is_product = false;

            global $wp_query;
            if (isset($wp_query)) {
                if (function_exists('is_product')) {
                    $is_product = is_product();
                }
            }


            if (($product->get_type() == 'variable') && $is_product == false) {

                return $product_add_to_cart_text;//"In RFQ"

            }


            $rfq_check = false;
            $normal_check = false;
            //gpls_woo_rfq_get_mode($rfq_check, $normal_check);
            $rfq_check = false;
            $normal_check = false;
            $checkout = "";

            if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "rfq") {
                add_filter('woocommerce_cart_needs_payment', 'gpls_woo_rfq_cart_needs_payment', 1000, 2);
                $rfq_check = true;
                $normal_check = false;
                $checkout = "rfq";
            }

            if (get_option('settings_gpls_woo_rfq_checkout_option', 'normal_checkout') == "normal_checkout") {
                $rfq_check = false;
                $normal_check = true;
                $checkout = "normal";
            }

            if (function_exists('wp_get_current_user')) {
                if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') == 'yes' && !wp_get_current_user()->exists()) {
                    $rfq_check = true;
                    $normal_check = false;
                    $checkout = "rfq";
                }
            }


            if ($rfq_check) {
                $default_text = __('Add to Quote', 'woo-rfq-for-woocommerce');
                $default_text = __($default_text, 'woo-rfq-for-woocommerce');
            } else {
                $default_text = __('Add to Cart', 'woo-rfq-for-woocommerce');
                $default_text = __($default_text, 'woo-rfq-for-woocommerce');
            }


            //   global $product;
            //  WC()->init();
            //    if (!is_object($product)) $product = wc_get_product(get_the_ID());
            // if (!is_object($product)) $product = WC()->product_factory->get_product(get_the_ID());

            global $woocommerce;

            if ($normal_check) {

                $rfq_enable = get_post_meta($product->get_id(), '_gpls_woo_rfq_rfq_enable', true);
                $rfq_enable = apply_filters('gpls_rfq_enable', $rfq_enable, $product->get_id());


                if ($rfq_enable != "yes" || !isset($rfq_enable)) {
                    // if (!(get_option('settings_gpls_woo_rfq_hide_visitor_prices_normal', 'no') == 'yes' && !wp_get_current_user()->exists())) {
                    $add_txt = $product_add_to_cart_text;
                    return $product_add_to_cart_text;
                    // }
                }
            }

            $add_txt = get_option('rfq_cart_wordings_add_to_cart', $default_text);
            $add_txt = __($add_txt, 'woo-rfq-for-woocommerce');

            $in_txt = get_option('rfq_cart_wordings_in_cart', $default_text);
            $in_txt = __($in_txt, 'woo-rfq-for-woocommerce');


            if (function_exists('wp_get_current_user')) {
                if (get_option('settings_gpls_woo_rfq_hide_visitor_prices', 'no') == 'yes' && !wp_get_current_user()->exists()) {

                    $add_txt = get_option('rfq_cart_wordings_add_to_rfq', $default_text);
                    $add_txt = __($add_txt, 'woo-rfq-for-woocommerce');

                    $in_txt = get_option('rfq_cart_wordings_in_rfq', $default_text);
                    $in_txt = __($in_txt, 'woo-rfq-for-woocommerce');

                }
            }


            if (isset($woocommerce) && $woocommerce != null && $woocommerce->cart != null) {
                foreach ($woocommerce->cart->get_cart() as $cart_item_key => $values) {
                    $_product = $values['data'];

                    if (get_the_ID() == $_product->get_id()) {

                        $add_txt = $in_txt;
                        break;

                    }
                }
            }

            $rfq_checkout_mode = $checkout;

            $add_txt = apply_filters('gpls_woo_rfq_custom_add_to_cart_button_text', $add_txt, $product, $rfq_checkout_mode);

            do_action('gpls_woo_rfq_add_to_cart_button_text_action', $add_txt, $product, $rfq_checkout_mode);


            return $add_txt;
            //return $btn_txt;


        }

        /**
         * @param $wp
         * @return array
         */
        public function get_url()
        {
            $link_to_rfq_page = pls_woo_rfq_get_link_to_rfq();
            global $wp;
            $current_url = gpls_woo_rfq_remove_http(home_url(add_query_arg(array(), $wp->request)));
            return $current_url;

        }


    }

}

