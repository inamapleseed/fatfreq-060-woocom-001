<div class="xoo-wl-table-container">
	<div class="xoo-wl-notices"></div>
	<table id="xoo-wl-history-table" class="display xoo-wl-table">
		<thead>

			<tr>
				<th><?php _e( 'Date', 'waitlist-woocommerce' ); ?></th>
				<th><?php _e( 'Status', 'waitlist-woocommerce' ); ?></th>
				<th><?php _e( 'Product', 'waitlist-woocommerce' ); ?></th>
				<th><?php _e( 'Emails', 'waitlist-woocommerce' ); ?></th>
			</tr>

			<tbody>
			<?php foreach ( $crons as $timestamp => $_cron ) {
				$product_id 	= (int) $_cron['product_id'];
				$product 		= wc_get_product( $product_id );

				if( !$product || !is_object( $product ) ) continue;

				$timestamp 		= esc_attr( $timestamp );
				$status 		= esc_attr( $_cron['status'] );
				$count 			= (int) $_cron['count'];
				
				$edit_link 		= $product->is_type('variation') ? get_edit_post_link( $product->get_parent_id() ) : get_edit_post_link( $product_id );
				$product_title 	= '<span>'.$product->get_formatted_name().'</span>';

			?>
				<tr data-product_id="<?php echo $product_id; ?>">

					<td data-sort="<?php echo $timestamp ?>"><?php echo get_date_from_gmt( date( 'd M y h:i a', $timestamp ), 'd M y <\b\r> h:i a' ); ?></td>

					<td class="xoo-wlht-status xoo-wlht-status-<?php echo $status; ?>" ><?php echo $status; ?></td>

					<td class="xoo-wltd-pname">
						<div class="xoo-wl-pimg">
							<?php echo $product->get_image(); ?>
							<a href="<?php echo $edit_link; ?>" target="_blank"><?php echo $product_title; ?></a>
						</div>
					</td>

					<td><?php echo $count; ?></td>

				</tr>

			<?php }; ?>

			</tbody>
		</thead>
	</table>
</div>


<?php if( xoo_wl_core()->history_count < 10 ): ?>
	<style type="text/css">
		div#xoo-wl-history-table_length {
		    display: none;
		}
	</style>
<?php endif; ?>