<?php

get_header();

$is_page_builder_used = et_pb_is_pagebuilder_used( get_the_ID() );

?>

<div id="main-content" class="artist-collabs">

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

					<div class="entry-content">
						<?php
							the_content();

							if ( ! $is_page_builder_used )
								wp_link_pages( array( 'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'Divi' ), 'after' => '</div>' ) );
						?>

						<?php $artists = get_field('artists'); ?>

						<?php if($artists) { ?>
							<?php foreach($artists as $artist){?>
								<div class="artist-box">
									<div class="image-left img-box">
										<img src="<?php echo $artist['image_left'];?>" alt="<?php echo $artist['artist_name'];?>"/>
									</div>
									
									<div class="center">
										<img src="<?php echo $artist['artist_image'];?>" alt="<?php echo $artist['artist_name'];?>"/>
										<a class="le-btn" style="background: <?php echo $artist['artist_name_color'];?>" href="<?php echo $artist['artist_url'];?>"><?php echo $artist['artist_name'];?></a>
										<p class="desc"><?php echo nl2br($artist['artist_description']);?></p>
									</div>

									<div class="image-right img-box">
										<img src="<?php echo $artist['image_right'];?>" alt="<?php echo $artist['artist_name'];?>"/>
									</div>
								</div>
							<?php } ?>
						<?php } ?>
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

	jQuery(".image-left").attr('data-aos', 'fade-right');
	jQuery(".image-right").attr('data-aos', 'fade-right');
	jQuery(".my-ctr").attr('data-aos', 'fade-up');
</script>

<?php

get_footer();
