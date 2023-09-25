<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action( 'woocommerce_before_main_content' );

?>


<!-- <header class="woocommerce-products-header">
	<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
		<h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
	<?php endif; ?>

	<?php
	/**
	 * Hook: woocommerce_archive_description.
	 *
	 * @hooked woocommerce_taxonomy_archive_description - 10
	 * @hooked woocommerce_product_archive_description - 10
	 */
	do_action( 'woocommerce_archive_description' );
	?>
</header> -->

<!-- <div class="breadcrumb">
	<?php echo do_shortcode( '[wpseo_breadcrumb]' ); ?>
</div> -->

<!-- <div class="title-header center">
	<h2><strong><?php woocommerce_page_title(); ?></strong></h2>
</div> -->
<!-- here -->
<div class="category-tabs-con">
	<?php
		$obj = get_queried_object()->term_id;

		$thumbnail_id = get_term_meta( $obj, 'thumbnail_id', true ); 

		// get the image URL
		$image = wp_get_attachment_url( $thumbnail_id ); 

		$terms = get_terms( array(
			'taxonomy' => 'product_cat',
			'hide_empty' => false,
		) );
		$count = count($terms);
		foreach ($terms as $term){
			$active = '';
			if($obj == $term->term_id) {
				$active = 'active';
				$bottom_banner = get_field('bottom_banner_image', $term->taxonomy . '_' . $term->term_id);
			}
			$link = get_term_link($term->term_id);
			$bgcolor = get_field('cat_background_color', $term->taxonomy . '_' . $term->term_id);
	?>
		<a href="<?= $link; ?>" class="<?php echo $active; ?> category-tab my-category-tab<?php echo $term->term_id; ?>"><?php echo $term->name; ?></a>
		<style>
			.my-category-tab<?php echo $term->term_id; ?> {
				color: <?php echo $bgcolor; ?> !important;
				border: 1px solid <?php echo $bgcolor; ?>;
			}
			.my-category-tab<?php echo $term->term_id; ?>:hover, .my-category-tab<?php echo $term->term_id; ?>.active {
				background: <?php echo $bgcolor; ?>;
			}
		</style>
	<?php
		}
	?>
	<span class="fidelius top-banner-img" data-id="<?php echo $image; ?>"></span>
	<span class="fidelius bottom-banner-img" data-id="<?php echo $bottom_banner; ?>"></span>
</div>
<div class="top-banner-con"><img src="" alt=""></div>
<div class="shoppage-container">
<div class="left" style="display: none">
	<div id="close_filter"><span></span><span></span></div>
	<?php if ( is_active_sidebar( 'sidebarshop' ) ) {
			dynamic_sidebar( 'sidebarshop' );
	} ?>
</div>
<div class="right">
<?php
if ( woocommerce_product_loop() ) {

	/**
	 * Hook: woocommerce_before_shop_loop.
	 *
	 * @hooked woocommerce_output_all_notices - 10
	 * @hooked woocommerce_result_count - 20
	 * @hooked woocommerce_catalog_ordering - 30
	 */
	do_action( 'woocommerce_before_shop_loop' );

	woocommerce_product_loop_start();

	if ( wc_get_loop_prop( 'total' ) ) {
		while ( have_posts() ) {
			the_post();

			/**
			 * Hook: woocommerce_shop_loop.
			 */
			do_action( 'woocommerce_shop_loop' );

			wc_get_template_part( 'content', 'product' );
		}
	}

	woocommerce_product_loop_end();

	/**
	 * Hook: woocommerce_after_shop_loop.
	 *
	 * @hooked woocommerce_pagination - 10
	 */
	do_action( 'woocommerce_after_shop_loop' );
} else {
	/**
	 * Hook: woocommerce_no_products_found.
	 *
	 * @hooked wc_no_products_found - 10
	 */
	do_action( 'woocommerce_no_products_found' );
}
?>

</div>

<div id="open_filter">Filter</div>
</div>
<div class="bottom-banner-con"><img src="" alt=""></div>

<script>
	let banner_image = jQuery(".top-banner-img").data("id");
	jQuery(".top-banner-con").find("img").attr('src', banner_image);

	let banner_image2 = jQuery(".bottom-banner-img").data("id");
	jQuery(".bottom-banner-con").find("img").attr('src', banner_image2);
</script>
<?php
/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );

/**
 * Hook: woocommerce_sidebar.
 *
 * @hooked woocommerce_get_sidebar - 10
 */
do_action( 'woocommerce_sidebar' );

?>
    <script type="text/javascript"> 
jQuery(document).ready(function($){ 

$( ".shoppage-container .widget_product_categories .product-categories > li" ).each(function( index ) {
    if ( $(this).children('ul').length > 0 ) {
        $(this).children('a').append("<div class='caret'></div>");
		
		if($(this).hasClass('current-cat') || $(this).hasClass('current-cat-parent')){
			$(this).addClass('active');
		}
    }



});


$( ".shoppage-container .widget_product_categories .product-categories > li > ul > li" ).each(function( index ) {
    if ( $(this).children('ul').length > 0 ) {
        $(this).children('a').append("<div class='caret'></div>");

		if($(this).hasClass('current-cat') || $(this).hasClass('current-cat-parent')){
			$(this).addClass('active');
		}
    }
});

$( ".shoppage-container .widget_product_categories .product-categories > li > ul > li > ul > li" ).each(function( index ) {

		if($(this).hasClass('current-cat')){
			$(this).addClass('active');
		}

});

$( document ).on( "click", ".caret", function(event) {
        // event.stopImmediatePropagation();
        event.preventDefault();
        event.stopPropagation();

        $(this).closest('li').toggleClass('active');
  });

  $( document ).on( "click", "#open_filter", function(event) {
    event.preventDefault();
    event.stopPropagation();
    $('.shoppage-container .left').addClass('open');
});
  
  
$( document ).on( "click", "#close_filter", function(event) {
    event.preventDefault();
    event.stopPropagation();
    $('.shoppage-container .left').removeClass('open');
    
});
  
  
});
</script>


<?php

get_footer( 'shop' );
