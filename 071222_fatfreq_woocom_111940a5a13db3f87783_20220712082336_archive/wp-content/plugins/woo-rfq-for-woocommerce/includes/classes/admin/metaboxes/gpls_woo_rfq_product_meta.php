<?php

/**
 * Main class
 *
 */
if (!class_exists('gpls_woo_rfq_product_meta')) {

    class gpls_woo_rfq_product_meta
    {



        public static function init()
        {
            add_action( 'woocommerce_product_options_advanced', array(__CLASS__, 'gpls_woo_rfq_add_custom_general_fields' ),10,0);
            add_action( 'woocommerce_process_product_meta', array(__CLASS__,'gpls_woo_rfq_add_custom_general_fields_save' ),10,1);

        }



        public static function gpls_woo_rfq_add_custom_general_fields() {

            global $woocommerce, $post;
// Text Field

            ?>
            <h2><b>RFQ-ToolKit</b></h2>
            <hr style="display: block;margin-top: 1em;margin-bottom: 1em;margin-left: auto;margin-right: auto;border-style: inset;color: black; border-width: 2px;">
<?php

            woocommerce_wp_checkbox(
                array(
                    'id' => '_gpls_woo_rfq_rfq_enable',
                    'label' => __( 'Enable RFQ for this product.', 'woo-rfq-for-woocommerce' ),
                    'placeholder' => 'Enable RFQ for this product',
                    'desc_tip' => 'true',
                    'description' => __( "Enable quote requests for this product.", 'woo-rfq-for-woocommerce' )

                ));

        }

        public static function gpls_woo_rfq_add_custom_general_fields_save( $post_id ){


            if(isset($_POST['_gpls_woo_rfq_rfq_enable'])) {

                    update_post_meta($post_id, '_gpls_woo_rfq_rfq_enable', 'yes');

            }else{


                 update_post_meta($post_id, '_gpls_woo_rfq_rfq_enable', 'no');
            }
        }
    }

}
