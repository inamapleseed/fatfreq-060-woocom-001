<?php

/**
 * Main class
 *
 */
if (!class_exists('gpls_woo_rfq_checkout')) {

    class gpls_woo_rfq_checkout
    {
        public function __construct()
        {


            //if (isset($_GET['pay_for_order']))
            //if (isset($_GET['pay_for_order']))
            {

                add_filter( 'woocommerce_get_order_item_totals', array($this,'gpls_woo_get_order_item_totals' ),100,2 );

            }

            add_action('woocommerce_before_checkout_form', array($this,'gpls_woo_woocommerce_before_checkout_form' ), 100);

            add_action('woocommerce_after_checkout_form', array($this,'gpls_woo_woocommerce_after_checkout_form' ), 1000, 1);

            add_filter( 'woocommerce_order_button_html', array($this,'gpls_woo_woocommerce_order_button_html' ), 100, 1);

            add_filter( 'woocommerce_thankyou_order_received_text', array($this,'gpls_woo_woocommerce_thankyou_order_received_text' ), 100, 2);




            add_action( 'gpls_woo_create_an_account', 'gpls_woo_create_an_account_function', 10);

        }




        public function  gpls_woo_before_pay_action($order){

            if(!$order->has_status( 'gplsquote-req' )){
                $GLOBALS["gpls_woo_rfq_show_prices"] = "yes";
                $GLOBALS["hide_for_visitor"] = "no";

                gpls_woo_rfq_remove_filters();
            }

        }



        public function gpls_woo_rfq_is_shipping_enabled(){

            if (WC()->shipping()->enabled == true) {
               // add_filter('woocommerce_form_field_country', array($this, 'gpls_woo_rfq_form_field_country'), 1000, 4);
            }

        }





        public function gpls_woo_rfq_form_field_country($field, $key, $args, $value){

            // d($field);d($key);d($args);d($value);
            $custom_attributes = array();

            if ( isset( $args['custom_attributes'] ) && is_array( $args['custom_attributes'] ) ) {
                foreach ( $args['custom_attributes'] as $attribute => $attribute_value ) {
                    $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
                }
            }


            $label_id = $args['id'];
            $field_container = '<p class="form-row %1$s" id="%2$s">%3$s</p>';

            $countries = 'shipping_country' === $key ? WC()->countries->get_shipping_countries() : WC()->countries->get_allowed_countries();

            if ( $args['required'] ) {
                $args['class'][] = 'validate-required';
                $required = ' <abbr class="required" title="' . esc_attr__( 'required', 'woo-rfq-for-woocommerce'  ) . '">*</abbr>';
            } else {
                $required = '';
            }

            if ( 1 === sizeof( $countries ) ) {
                $args['autocomplete']='autocomplete="off"';
//d($args['autocomplete']);
                //$field = '<strong>' . current( array_values( $countries ) ) . '</strong>';
                $field = '<select  disabled name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ). '" '  . $args['autocomplete'].'  class="country_to_state country_select ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'" ' . implode( ' ', $custom_attributes ) . '>';


                foreach ( $countries as $ckey => $cvalue ) {
                    $field .= '<option value="' . esc_attr( $ckey ) . '" '. selected( $value, $ckey, false ) . '>'. __( $cvalue, 'woo-rfq-for-woocommerce' ) .'</option>';
                }

                $field .= '</select>';

                $field .= '<noscript><input type="submit" name="woocommerce_checkout_update_totals" value="' . esc_attr__( 'Update country', 'woo-rfq-for-woocommerce' ) . '" /></noscript>';
                $field .= '<input type="hidden" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="' . current( array_keys($countries ) ) . '" ' . implode( ' ', $custom_attributes ) . ' class="country_to_state" />';

                if ( isset( $field ) ) {
                    $field_html = '';

                    if ( $args['label'] && 'checkbox' != $args['type'] ) {
                        $field_html .= '<label for="' . esc_attr( $label_id ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) .'">' . $args['label'] . $required . '</label>';
                    }

                    $field_html .= $field;

                    if ( $args['description'] ) {
                        $field_html .= '<span class="description">' . esc_html( $args['description'] ) . '</span>';
                    }

                    $container_class = 'form-row ' . esc_attr( implode( ' ', $args['class'] ) );
                    $container_id = esc_attr( $args['id'] ) . '_field';

                    $after = isset( $args['clear'] ) ? '<div class="clear"></div>' : '';

                    $field = sprintf( $field_container, $container_class, $container_id, $field_html ) . $after;
                }

            }




            return $field;


        }

        public function gpls_woo_woocommerce_thankyou_order_received_text($message, $order){

            if(is_object($order)==false || $order == null || $order == false ){
                return;
            }

            if (  $order->get_status()=='gplsquote-req' ) {
                $confirmation_message = get_option('gpls_woo_rfq_quote_submit_confirm_message', __('Your quote request has been successfully submitted!', 'woo-rfq-for-woocommerce'));
                $confirmation_message = __($confirmation_message,'woo-rfq-for-woocommerce');

                return $confirmation_message;
            }else{
                return $message;
            }
        }

        public function gpls_woo_woocommerce_before_checkout_form(){
          //  d(WC()->cart);

        }
        public function gpls_woo_woocommerce_after_checkout_form($checkout){
            //  d(WC()->cart);


        }


        public function gpls_woo_woocommerce_order_button_html($button){
            //d(WC()->cart);
            if ( $GLOBALS["gpls_woo_rfq_checkout_option"] === 'rfq' ) {

                $order_button_text =  get_option('rfq_cart_wordings_submit_your_rfq_text',__('Submit Your Request For Quote', 'woo-rfq-for-woocommerce' ));
                $order_button_text = __($order_button_text,'woo-rfq-for-woocommerce');

                $order_button_text = apply_filters('gpls_woo_rfq_rfq_submit_your_order_text',$order_button_text);

            }else{

               // $button = apply_filters('gpls_woo_rfq_checkout_submit_your_order_text',$button);

                return $button;
            }

            return '<input type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '" />';
        }




        public function gpls_woo_get_order_item_totals($total_rows, $order )
        {
            foreach ($total_rows as $key => $val) {


                if ($key == 'payment_method' && $val['value']=='Request Quote')
                    unset($total_rows[$key]);
            }

            return $total_rows;
        }



    }
}
