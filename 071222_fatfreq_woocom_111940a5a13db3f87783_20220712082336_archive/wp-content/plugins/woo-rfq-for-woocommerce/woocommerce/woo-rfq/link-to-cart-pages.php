<?php


if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


$view_your_cart_text = get_option('rfq_cart_wordings_view_rfq_cart', __('View List', 'woo-rfq-for-woocommerce'));
$view_your_cart_text = __($view_your_cart_text, 'woo-rfq-for-woocommerce');


echo <<< eod
<div class="fqcart-link-div-shop  fqcart-link-div-shop-custom"><a  class="rfqcart-link-shop rfqcart-link-shop-custom" href="{$link_to_rfq_page}" >$view_your_cart_text</a></div>
eod;
?>