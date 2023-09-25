<?php get_header(); ?>


<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <div class="single_news">
	<?php echo do_shortcode('[et_pb_section global_module="411"][/et_pb_section]'); ?>
		<div class="container">

			<div class="single_news_10">
				<div class="img-container" style="background-image: url('<?=get_the_post_thumbnail_url(get_the_ID(),'full');?>');" ></div>

				<div class="text-container">
					<h4 class="fs36"><?php the_title(); ?></h4>
					<div class="date"><?php echo get_the_date( 'M d, Y' ); ?></div>
					<div class="content-container">
					<?php the_content(); ?>
					</div>
					
					<div class="btn-container">
						<div class="share-container">
							<span>Share</span>
							<div class="addthis_inline_share_toolbox"></div>
							<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5fd98f1872ad1fd5"></script>
						</div>

						<div class="clearfix"></div>
					</div>

				</div>


                <div class="review-container">
                <?php
						if ( ( comments_open() || get_comments_number() ) && 'on' == et_get_option( 'divi_show_postcomments', 'on' ) ) {
							comments_template( '', true );
						}
					?>
                </div>
               
				
			</div>
		</div>
    </div>
    <?php endwhile; ?>
    <?php endif; ?>


<?php get_footer(); ?>