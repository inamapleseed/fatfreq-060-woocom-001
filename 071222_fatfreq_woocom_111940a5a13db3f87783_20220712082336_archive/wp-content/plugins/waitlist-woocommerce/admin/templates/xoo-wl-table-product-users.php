<?php


$show_qty = $fieldsData['xoo_wl_required_qty']['settings']['active'] === "yes";

$customFieldsData = $fieldsData;
unset( $customFieldsData['xoo_wl_required_qty'] );
unset( $customFieldsData['xoo_wl_user_email'] );

$extraFields = array();
$index 		 = 0;
foreach ( $customFieldsData as $field_id => $field_data ) {
	$settings = $field_data['settings'];
	if( $settings['active'] !== "yes" ) continue;
	$extraFields[ $index ]['heading'] = $settings['label'] ? $settings['label'] : ( $settings['placeholder'] ? $settings['placeholder'] : $settings['unique_id'] );
	$extraFields[ $index ]['id'] = $field_id;
	$index++;
}

if( $product = wc_get_product( $product_id ) ){
	?>

	<div class="xoo-wl-ut-header">
		<div class="xoo-wl-uth-cont">
			<?php echo $product->get_image(); ?>
			<div class="xoo-wl-uth-right">

				<span><a href="<?php echo $product->is_type('variation') ? get_edit_post_link( $product->get_parent_id() ) : get_edit_post_link( $product_id ); ?>" target="_blank"><?php echo $product->get_formatted_name(); ?></a></span>

				<span class="xoo-wl-ut-ucount"><?php printf( __( '<b>No of users:</b> <span>%s</span>', 'waitlist-woocommerce' ), $count['rowsCount'] ); ?></span>

				<span class="xoo-wl-ut-qcount"><?php printf( __( '<b>Total quantity requested:</b> <span>%s</span>', 'waitlist-woocommerce' ), $count['totalQuantity'] ); ?></span>

				<span><b><?php _e( 'Stock Status: ', 'waitlist-woocommerce' ); ?></b><span><?php echo $product->get_stock_status(); ?></span></span>

			</div>
		</div>
	</div>

	<?php
}

?>



<div class="xoo-wl-table-container">
	<div class="xoo-wl-notices"></div>
	<table id="xoo-wl-users-table" class="display xoo-wl-table" data-product_id="<?php echo $product_id; ?>">
		<thead>

			<tr>
				<th class="no-sort"><span class="dashicons dashicons-no-alt"></span></th>
				<th><?php _e( 'Joined on', 'waitlist-woocommerce' ); ?></th>
				<th><?php _e( 'Email', 'waitlist-woocommerce' ); ?></th>

				<?php if( $show_qty ): ?>
					<th><?php _e( 'Quantity', 'waitlist-woocommerce' ); ?></th>
				<?php endif; ?>

				<?php foreach ( $extraFields as $extraFieldData ): ?>
					<th><?php esc_attr_e( $extraFieldData['heading'] ); ?></th>
				<?php endforeach; ?>

				<th class="no-sort"><?php _e( 'Back in Stock Email', 'waitlist-woocommerce' ); ?></th>

			</tr>

			<tbody>
			<?php
				foreach ( $rows as $userRow ) {
				$timestamp 	= strtotime( $userRow->join_date);
				$row_id 	= (int) $userRow->xoo_wl_id;
				$meta_data 	= xoo_wl_db()->get_waitlist_meta( $row_id  );
				$sent_count = isset( $meta_data['_sent_count'] ) ? (int) $meta_data['_sent_count'] : '';
			?>
				<tr data-row_id="<?php echo $row_id; ?>">

					<td><span class="dashicons dashicons-no-alt xoo-wl-remove-row"></span></td>
					<td data-sort="<?php echo $timestamp ?>" ><?php echo date( "d M y", $timestamp ); ?></td>
					<td class="xoo-wl-ut-email"><?php esc_attr_e( $userRow->email ); ?><?php echo $userRow->user_id ? '<span class="dashicons dashicons-yes-alt"></span>' : ''; ?></td>

					<?php if( $show_qty ): ?>

					<td><?php esc_attr_e( $userRow->quantity ); ?></td>

					<?php endif; ?>

					<?php foreach ( $extraFields as $extraFieldData ):

						$field_id 	 = $extraFieldData['id'];
						$field_value = isset( $meta_data[ $field_id ] ) ? $meta_data[ $field_id ] : '';
						if( $field_value ){
							$field_value = xoo_wl()->aff->fields->get_field_value_label( $field_id, $field_value );
						}
						
					?>

					<td><?php esc_attr_e( $field_value ); ?></td>
	
					<?php endforeach; ?>

					<td><span class="xoo-wl-bis-btn xoo-wl-table-btn"><?php _e( 'Send', 'waitlist-woocommerce' ); ?> <span class="xoo-wl-sent-count"><?php echo $sent_count ? '( '.$sent_count.' )': ''; ?></span></span></td>

				</tr>

			<?php }; ?>

			</tbody>
		</thead>
	</table>
</div>