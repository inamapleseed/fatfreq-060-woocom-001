<?php get_header(); ?>

<div class="category_page">
<?php echo do_shortcode('[et_pb_section global_module="411"][/et_pb_section]'); ?>
<div class="container">

<?php
global $wp;
$limit = 6;
$post_type = 'post'; 
// $post_type = get_post_type(); 
$post_taxonomy = 'category';

// $obj = get_queried_object();
// if($post_type == ''){
//     $post_taxonomy = $obj->taxonomy;
//     $post_type = get_post_types_by_taxonomy($post_taxonomy);
// }else{
//     $post_taxonomy = get_object_taxonomies( $post_type );
// }


// print_r($post_taxonomy );
// print_r($obj );
$page_name = '';

if($obj->label){
    $page_name = $obj->label;
}else{
    $page_name = $obj->name;
}

// $items_per_page = 20;
// $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

$cur_term_id = get_queried_object()->term_id;

$paged = ( $_GET['pg'] ) ? $_GET['pg'] : 1;
$tax = array();
		
if($cur_term_id > 0) {
    $tax[] = array(
        'taxonomy' => $post_taxonomy[0],
        'field'    => 'term_id',
        'terms'    => $cur_term_id,
    );
}

// $terms = get_terms( 
//     array(
//         'taxonomy' => $post_taxonomy,
//         'hide_empty' => false,
//     ) 
// );

$args_taxonomy = array(
    'hide_empty'         => 0,
    'echo'               => 1,
    'taxonomy'      => $post_taxonomy,
    'hierarchical'  =>1,
    'show_count' => 0,
    'title_li' => ''
);

function add_class_wp_list_categories($wp_list_categories) {
        $pattern = '/<li class="/is';
        $replacement = '<li class="first ';
        return preg_replace($pattern, $replacement, $wp_list_categories);
}
add_filter('wp_list_categories','add_class_wp_list_categories');

$year = get_query_var('year');
$month = get_query_var('monthnum');


$args = array( 
    'post_type'      => $post_type,
    'posts_per_page' => $limit,
    'post_status'    => 'publish',
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
    // 'orderby'        => 'modified',
    // 'order'          => 'DESC',
    'tax_query'      => $tax,
    'paged'          => $paged,
	'date_query' => array(
        'year' => $year,
        'monthnum' => $month
    )

);

$news = new WP_Query( $args );

$args = array( 
    'post_type'      => $post_type,
    'posts_per_page' => 4,
    'post_status'    => 'publish',
    'orderby'        => 'modified',
    'order'          => 'DESC',
);
$upcoming = new WP_Query( $args );

?>



<div class="newscontainer">
    <div class="container_left">

        <div class="content category">
            <h4>Category</h4>
            <ul>
            <?php echo wp_list_categories( $args_taxonomy ); ?>
            </ul>
            <?php /*
            <ul>
            <!-- <li><a href="<?php echo $article_page_link; ?>" class=""><?=$all_text;?></a></li> -->
                <?php foreach($terms as $term){ ?>
                    <li><a href="<?=home_url().'/'.$term->taxonomy.'/'.$term->slug ; ?>" class=" <?=$cur_term_id==$term->term_id?'active':''; ?>" ><?=$term->name; ?></a></li>
                <?php } ?>
            </ul>
            */ ?>
        </div>

        <div class="content archive">
            <h4>Archives</h4>
            <div class="archive-container">
                <?php
                global $wpdb;

                $limit = 0;
                $year_prev = null;
                // $months = $wpdb->get_results("SELECT DISTINCT MONTH( post_date ) AS month ,	YEAR( post_date ) AS year, COUNT( id ) as post_count FROM $wpdb->posts WHERE post_status = 'publish' and post_date <= now( ) and post_type = 'post' GROUP BY month , year ORDER BY post_date DESC");
                $months = $wpdb->get_results("SELECT DISTINCT MONTH( post_date ) AS month ,	YEAR( post_date ) AS year, COUNT( id ) as post_count FROM $wpdb->posts WHERE post_status = 'publish' and post_date <= now( ) and post_type = 'post' GROUP BY month , year ORDER BY year DESC,  month DESC");

                foreach($months as $month) :

                    $year_current = $month->year;
                    
                    if ($year_current != $year_prev)
                    {
                        if($year_current != date('Y'))
                        {
                        ?>
                            </ul>
                        <?php
                        }
                        ?>

                        <div class="year">
                            <a href="<?php bloginfo('url') ?>/<?php echo $month->year; ?>/"><?php echo $month->year; ?></a>
                        </div>	
                        <ul class='list-group'>			
                    <?php 
                    } 
                    ?>
                    
                    <li class='list-group-item'>
                        <a href="<?php bloginfo('url') ?>/<?php echo $month->year; ?>/<?php echo date("m", mktime(0, 0, 0, $month->month, 1, $month->year)) ?>"><span class="archive-month"><?php echo date_i18n("F", mktime(0, 0, 0, $month->month, 1, $month->year)) ?></span></a>
                    </li>
                    
                    <?php 
                    $year_prev = $year_current;

                endforeach; 
                ?>
                </ul>
            </div>
        </div>


    </div>

    <div class="container_right">

        <?php if( $news->have_posts() ):?>
        <ul class="news_listing_ul">
        <?php while ($news->have_posts()) : $news->the_post();
            ?>
            <li>
                <div class="li-container news-block">
                    <div class="img-container" style="background-image: url('<?=get_the_post_thumbnail_url(get_the_ID(),'full');?>');" >
                        <a href="<?php echo get_permalink(); ?>"></a>
                        <div class="hover-bg">
                            <div class="btn-container">
                                <a href="<?php echo get_permalink(); ?>" class="btn btn-white">Read More</a>
                            </div>
                        </div>
                    </div>

                    <div class="text-container">
                        <h4 class="title"><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h4>
                        <div class="date"><?php echo get_the_date( 'M d, Y' ); ?></div>
                        <p><?php the_excerpt(); ?></p>

                        
                    </div>
                </div>
            </li>
        <?php endwhile; ?>
        </ul>
        <?php else: ;?>
        <p>There is not any News at the moment</p>
        <?php endif; wp_reset_query(); ?>

        <div class="pg-div"><?php echo custom_pagination($news->max_num_pages, "", $paged); ?></div>

    </div>
</div>


</div>
</div>

<script type="text/javascript"> 
jQuery(document).ready(function($){ 

$( ".newscontainer .category > ul > li" ).each(function( index ) {
    if ( $(this).children('ul').length > 0 ) {
        $(this).children('a').append("<div class='caret'></div>");
    }



});

$( document ).on( "click", ".category .caret", function(event) {
        // event.stopImmediatePropagation();
        event.preventDefault();
        event.stopPropagation();
        $(this).parents('li').toggleClass('active');
  });



$( ".newscontainer .archive-container" ).each(function( index ) {
    if ( $(this).children('ul').length > 0 ) {
        $(this).children('.year').append("<div class='caret'></div>");
    }
});

$( document ).on( "click", ".archive-container .caret", function(event) {
        // event.stopImmediatePropagation();
        event.preventDefault();
        event.stopPropagation();
        $(this).parents('.archive-container').toggleClass('active');
  });



  
});
</script>

<?php get_footer(); ?>