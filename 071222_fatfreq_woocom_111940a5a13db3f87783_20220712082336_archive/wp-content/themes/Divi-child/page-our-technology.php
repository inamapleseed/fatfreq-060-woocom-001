<?php

get_header();

$is_page_builder_used = et_pb_is_pagebuilder_used( get_the_ID() );

?>

<div id="main-content" class="our-tech">

<?php if ( ! $is_page_builder_used ) : ?>

	<div class="container">
		<div id="content-area" class="clearfix">
			<div id="left-area">

<?php endif; ?>

			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

				<?php if ( ! $is_page_builder_used ) : ?>

					<div class="my-page-heading">
						<h2 class="entry-title main_title"><?php the_title(); ?></h2>
						<p><?php echo get_field('page_description'); ?></p>
					</div>

				<?php
					$thumb = '';

					$width = (int) apply_filters( 'et_pb_index_blog_image_width', 1080 );

					$height = (int) apply_filters( 'et_pb_index_blog_image_height', 675 );
					$classtext = 'et_featured_image';
					$titletext = get_the_title();
					$alttext = get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true );
					$thumbnail = get_thumbnail( $width, $height, $classtext, $alttext, $titletext, false, 'Blogimage' );
					$thumb = $thumbnail["thumb"];

					if ( 'on' === et_get_option( 'divi_page_thumbnails', 'false' ) && '' !== $thumb )
						print_thumbnail( $thumb, $thumbnail["use_timthumb"], $alttext, $width, $height );
				?>

				<?php endif; ?>

					<div class="entry-content tech-con">
						<?php
							the_content();

							if ( ! $is_page_builder_used )
								wp_link_pages( array( 'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'Divi' ), 'after' => '</div>' ) );
						?>
						<?php
							/* 
							* Paginatation on Advanced Custom Fields Repeater
							*/
							if( get_query_var('paged') ) { 
								$page = get_query_var( 'paged' );
							} else {
								$page = 1;
							}

							// Variables
							$row              = 0;
							$images_per_page  = 1; // How many images to display on each page
							$images           = get_field( 'technology' );
							$total            = count( $images );
							$pages            = ceil( $total / $images_per_page );
							$min              = ( ( $page * $images_per_page ) - $images_per_page ) + 1;
							$max              = ( $min + $images_per_page ) - 1;

							// ACF Loop
							if( have_rows( 'technology' ) ) : ?>

								<?php foreach ($images as $tech): 
									$row++;

									// Ignore this image if $row is lower than $min
									if($row < $min) { continue; }

									// Stop loop completely if $row is higher than $max
									if($row > $max) { break; } 									
									?>
											<div class="tech-box <?php echo $tech['image_position'] == 'right' ? 'image-on-right' : 'image-on-left'; ?>">
												<div class="left-con">
													<img src="<?php echo $tech['tech_image'];?>" alt="<?php echo $tech['tech_title'];?>"/>
												</div>
												
												<div class="right-con">
													<h3><?php echo $tech['tech_title'];?></h3>
													<p class="desc"><?php echo nl2br($tech['tech_description']);?></p>
												</div>
											</div>


								<?php endforeach ;

							// Pagination
							echo paginate_links( array(
								'base' => get_permalink() . 'page/%#%' . '/',
								'current' => $page,
								'total' => $pages,
								'format' => '%#%/'.$pages,
								'prev_text' => '<',
								'next_text' => '>',
								'show_all' => true,
							) );
							?>

							<?php else: ?>

								No content found

							<?php endif; ?>

					</div> <!-- .entry-content -->

				<?php
					if ( ! $is_page_builder_used && comments_open() && 'on' === et_get_option( 'divi_show_pagescomments', 'false' ) ) comments_template( '', true );
				?>

				</article> <!-- .et_pb_post -->

			<?php endwhile; ?>

<?php if ( ! $is_page_builder_used ) : ?>

			</div> <!-- #left-area -->

			<?php get_sidebar(); ?>
		</div> <!-- #content-area -->
	</div> <!-- .container -->

<?php endif; ?>

</div> <!-- #main-content -->

<script>

	if(navigator.userAgent.match(/Trident\/7\./)) {
		document.body.addEventListener("mousewheel", function() {
			event.preventDefault();
			var wd = event.wheelDelta;
			var csp = window.pageYOffset;
			window.scrollTo(0, csp - wd);
		});
	}

	jQuery(window).on('load', function(){
		
		AOS.init({
			duration: 1000
		});
	});	

	jQuery(".image-on-right").attr('data-aos', 'fade-right');
	jQuery(".image-on-left").attr('data-aos', 'fade-right');
</script>
<script>
	$(".prev.page-numbers").hide();
</script>
<?php

get_footer();
