<?php
/**
 * Customer new RFQ email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-rfq.php.
 *
 */




if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


$show_prices=false;
$show_prices = apply_filters('gpls_woo_rfq_show_prices_customer_email',$show_prices);

if($show_prices==false) {
    add_filter('woocommerce_email_order_items_args', 'filter_gpls_woo_rfq_add_hide_prices_to_wc_emails', 100, 1);
}else{
    add_filter('woocommerce_email_order_items_args', 'filter_gpls_woo_rfq_add_show_prices_to_wc_emails', 100, 1);
}


?>

<?php

do_action('woocommerce_email_header', $email_heading,$email);

?>

<p><?php printf( __("Your request has been received and is now being reviewed. Your request details are shown below for your reference:", 'woo-rfq-for-woocommerce')); ?></p>

<?php do_action('woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text, $email ); ?>

<h2><?php printf(__('Order #%s', 'woo-rfq-for-woocommerce'), $order->get_order_number()); ?></h2>

<table class="td" cellspacing="0" cellpadding="6"
       style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
    <thead>
    <tr>
        <th class="td" scope="col" style="text-align:left;"><?php printf( __('Product', 'woo-rfq-for-woocommerce')); ?></th>
        <th class="td" scope="col" style="text-align:left;"><?php printf( __('Quantity', 'woo-rfq-for-woocommerce')); ?></th>
        <?php if ($show_prices  == 'yes')  : ?>

            <th class="td" scope="col" style="text-align:left;"><?php printf( __('Price', 'woo-rfq-for-woocommerce')); ?></th>
        <?php endif; ?>

    </tr>
    </thead>
    <tbody>
    <?php



    ?>

    <?php echo rfqtk_get_email_order_items($order,array(
        'items' =>$order->get_items(),
        'show_sku' => true,
        'show_image' => true,
        'image_size' => array(128, 128),
        'plain_text' => $plain_text,
        'show_prices' => $show_prices

    )); ?>

    </tbody>
    <tfoot>
    <?php


    ?>
    <?php if ($show_prices==true)  : ?>

            <?php
            if ($totals = $order->get_order_item_totals()) {
                $i = 0;
                foreach ($totals as $total) {
                    $i++;
                    ?>
                    <tr>
                    <th class="td" scope="row" colspan="2"
                        style="text-align:left; <?php if ($i == 1) echo 'border-top-width: 4px;'; ?>"><?php echo $total['label']; ?></th>
                    <td class="td"
                        style="text-align:left; <?php if ($i == 1) echo 'border-top-width: 4px;'; ?>"><?php echo $total['value']; ?></td>
                    </tr><?php
                }
            }
            ?>

    <?php endif; ?>

    </tfoot>
</table>

<?php


?>

<?php

 do_action('woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email);

 do_action('woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email);

 do_action('woocommerce_email_confirmation_messages', $order, $sent_to_admin, $plain_text);

 do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );


if ( $additional_content ) {
    echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

do_action('woocommerce_email_footer', $email);






