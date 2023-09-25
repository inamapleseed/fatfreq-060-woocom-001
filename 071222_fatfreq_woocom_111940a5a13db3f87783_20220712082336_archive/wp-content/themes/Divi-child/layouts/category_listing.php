
	<?php
		$obj = get_queried_object()->term_id;

		$thumbnail_id = get_term_meta( $obj, 'thumbnail_id', true ); 

		// get the image URL
		$image = wp_get_attachment_url( $thumbnail_id ); 

		$terms = get_terms( array(
			'taxonomy' => 'product_cat',
			'hide_empty' => false,
		) );
        foreach ($terms as $term){
			$link = get_term_link($term->term_id);
			$bgcolor = get_field('cat_background_color', $term->taxonomy . '_' . $term->term_id);
			$bottom_banner = get_field('bottom_banner_image', $term->taxonomy . '_' . $term->term_id);
	?>
		<a href="<?= $link; ?>" class="category-tab ct-big my-category-tab<?php echo $term->term_id; ?>">
            <div>
                <span class="cat-title"><?php echo $term->name; ?></span>
                <p class="desc"><?= $term->description; ?></p>
            </div>
        </a>

		<style>
			.my-category-tab<?php echo $term->term_id; ?> {
				background: <?php echo $bgcolor; ?> !important;
			}
		</style>
	<?php
		}
	?>