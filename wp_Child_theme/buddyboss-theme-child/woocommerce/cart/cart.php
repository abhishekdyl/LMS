<?php
/**
 * Cart Page
 *
 * This template overrides woocommerce/templates/cart/cart.php.
 *
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' );

$course_qty_error = false;
$moodle_courses_enable = array();
foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
    $moodle_courses_enable[] = get_post_meta( $cart_item['product_id'], '_moodle_courses_enable', true );
}

if (filter_input(INPUT_GET, 'update') === 'course-qty') :
    $error = sprintf( __( '<li class="course-qty-error">Please change quantity for "%s" product!</li>' ), $cart_item['data']->get_name() );
    wc_add_notice($error,'notice');
elseif (filter_input(INPUT_GET, 'update') === 'course-stock') :
    $error = sprintf( __( '<li class="course-qty-error">Sorry, "%s" is not available. Please edit your cart and try again. We apologize for any inconvenience caused.</li>' ), $cart_item['data']->get_name() );
    wc_add_notice($error,'error');
elseif ( filter_input( INPUT_GET, 'update' ) === 'cart' ) :
    wc_add_notice(sprintf('Please select student from the list or create a new student.'), 'error');
elseif ( filter_input( INPUT_GET, 'update' ) === 'group' ) :
    wc_add_notice(sprintf('Please select group from the list.'), 'error');
endif;

$current_user = wp_get_current_user();
$current_user_meta = metadata_exists('user', $current_user->ID, 'student_login_id');
$parent_value = metadata_exists('user', $current_user->ID,'parent_value_'.$current_user->ID);

if (is_user_logged_in() && empty($current_user_meta)){
    $style = 'style="width:auto;"';
    $student_style = 'style="width: 20%;"';
}else{
    $style = 'style="width:auto;"';
    $student_style = 'style="width: 20%;"';
}
$moodle_courses_enable = array();
foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
    $moodle_courses_enable[] = get_post_meta( $cart_item['product_id'], '_moodle_courses_enable', true );
}
if (count(array_values(array_unique($moodle_courses_enable))) == '1'){
    $val = in_array('1',$moodle_courses_enable);
}else{
    $val = '0';
}
?>
<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
	<?php do_action( 'woocommerce_before_cart_table' ); ?>

	<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
		<thead>
			<tr>
				<th class="product-remove"><span class="screen-reader-text"><?php esc_html_e( 'Remove item', 'woocommerce' ); ?></span></th>
				<th class="product-thumbnail"><span class="screen-reader-text"><?php esc_html_e( 'Thumbnail image', 'woocommerce' ); ?></span></th>
				<th class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
                <?php if (!is_user_logged_in() || (!array_intersect(array('student'),$current_user->roles) && count(array_values(array_unique($moodle_courses_enable))) != $val)) :
                        $label = 'Select Student';
                ?>
				<th class="product-student" <?php echo $student_style; ?>><?php esc_html_e( $label, 'woocommerce' ); ?></th>
                <?php endif; ?>
				<th class="product-price"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
				<th class="product-quantity"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
                <?php if (count(array_values(array_unique($moodle_courses_enable))) != $val) :
                ?>
				<th class="product-moodlegroup"><?php esc_html_e( 'Group', 'woocommerce' ); ?></th>
                <?php endif; ?>
				<th class="product-subtotal"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php do_action( 'woocommerce_before_cart_contents' ); ?>

			<?php
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
				$selectedgroups = !empty($cart_item['product_group_id']) ? moodleservice('get_groups', ['groupids' => (array) $cart_item['product_group_id']]): [];
				$moodle_courses_enable = get_post_meta( $cart_item['product_id'], '_moodle_courses_enable', true );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
					?>
					<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

						<td class="product-remove">
							<?php
								echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									'woocommerce_cart_item_remove_link',
									sprintf(
										'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
										esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
										esc_html__( 'Remove this item', 'woocommerce' ),
										esc_attr( $product_id ),
										esc_attr( $_product->get_sku() )
									),
									$cart_item_key
								);
							?>
						</td>

						<td class="product-thumbnail">
						<?php
						$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

						if ( ! $product_permalink ) {
							echo $thumbnail; // PHPCS: XSS ok.
						} else {
							printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
						}
						?>
						</td>

						<td class="product-name student-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>" <?php echo $style; ?>>
						<?php
						if ( ! $product_permalink ) {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
						} else {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
						}

						do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

						// Meta data.
						echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

						// Backorder notification.
						if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
						}
						?>
						</td>

                        <?php if (!is_user_logged_in() || (!array_intersect(array('student'),$current_user->roles) && $moodle_courses_enable == '0')) : ?>
                            <td class="product-student" data-title="<?php esc_attr_e( 'Select Student', 'woocommerce' ); ?>" <?php echo $student_style; ?>>
                                <?php if (!is_user_logged_in()) :
                                echo '<select class="create-login form-group"><option value="">-Select-</option></select>';
                                echo '<input type="hidden" id="new-login" value="1">';
                                // echo sprintf( '<a href="%s">%s</a>', esc_url( get_site_url() . '/student-register' ), 'New Student' ).'<br>';
                                // echo sprintf( '<a href="%s">%s</a>', esc_url( get_site_url() . '/student-login' ), 'Existing Student' );
                                // echo '<input type="hidden" id="new_student_id" value="">';
                                elseif ($moodle_courses_enable == '0' && !array_intersect(array('student'),$current_user->roles)):
                                    //print_r($moodle_courses_enable);die;
                                    $edit_url = esc_url(get_site_url() . '/student-list');
                                    $user_args = array(
                                        'order' => 'ASC',
                                        'orderby' => 'user_nicename',
                                        'meta_key' => 'parent_login_id',
                                        'meta_value' => $current_user->ID,
                                    );
                                    $user_query = new WP_User_Query($user_args);
                                    $user_results = $user_query->get_results();
                                    $total_users = $user_query->get_total();
                                    $primarystudent = get_field('select_student', 'user_' . $current_user->ID);
                                    if ($total_users > 0) :
                                    echo '<select name="product_student" id="product_student_'.$cart_item_key.'" class="product_student form-group" data-cart-id="'.$cart_item_key.'">
                                            <option value="">-Select-</option>';
                                    foreach ($user_results as $user) :
                                        if ($cart_item['product_student_id'] == $user->ID) {
                                            $selected = "selected=selected";
                                        } else {
                                            $selected = '';
                                        }
                                        echo '<option value="'.$user->ID.'" '.$selected.'>'.$user->first_name.' '.$user->last_name.'</option>';
                                    endforeach;
                                    if (!array_intersect(array('student'),$current_user->roles)):
                                        if ($cart_item['product_student_id'] == 'self_purchase') {
                                            $self_selected = "selected=selected";
                                        } else {
                                            $self_selected = '';
                                        }
                                        echo '<option value="self_purchase" '.$self_selected.'>Enroll Myself</option>';
                                    endif;
                                    echo '</select><br>';
                                    endif;
                                    echo '<input type="hidden" id="newstd" value="1">';
                                    if ($total_users == 0 && !array_intersect(array('student'),$current_user->roles)):
                                    echo '<select name="product_student" id="product_student_'.$cart_item_key.'" class="product_student form-group" data-cart-id="'.$cart_item_key.'">';
                                            if ($cart_item['product_student_id'] == 'self_purchase') {
                                                $self_selected = "selected=selected";
                                            } else {
                                                $self_selected = '';
                                            }
                                            echo '<option value="">-Select-</option><option value="self_purchase" '.$self_selected.'>Enroll Myself</option>';
                                    endif;
                                    echo '<input type="hidden" value="" id="new_student_id">';
                                    if ($total_users > 0) :
                                        echo '<input type="hidden" id="editstd" value="1" data-href="'.$edit_url.'">';
                                    endif;
                                     // echo sprintf( '<a href="%s">%s</a>', esc_url( get_site_url() . '/register' ), 'New Student' ).'<br>';
                                    // echo sprintf( '<a href="%s" class="edit-student" data-id="'.$cart_item['product_student_id'].'">%s</a>', esc_url(get_site_url() . '/edit-student/?id='.$cart_item['product_student_id']), 'Edit Student' );
                                endif; ?>
                            </td>
                        <?php endif; ?>

						<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
							<?php
								echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
							?>
						</td>

						<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>">
						<?php
						if ( $_product->is_sold_individually() ) {
							$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
						} else {
							$product_quantity = woocommerce_quantity_input(
								array(
									'input_name'   => "cart[{$cart_item_key}][qty]",
									'input_value'  => $cart_item['quantity'],
									'max_value'    => $_product->get_max_purchase_quantity(),
									'min_value'    => '0',
									'product_name' => $_product->get_name(),
								),
								$_product,
								false
							);
						}

						echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
						?>
						</td>
                        <?php if ($moodle_courses_enable == '0'): ?>
                        <td class="product-moodlegroup">
							<?php if(!empty($selectedgroups)): $selectedgroupid = $cart_item['product_group_id'] ?? 0; ?>
									<?php foreach($selectedgroups as $group): if($selectedgroupid == $group->id): ?>
										<input type="text" value="<?php echo $group->name ?>" disabled>
									<?php endif; endforeach; ?>
							<?php else: ?>
								-
							<?php endif; ?>
                        </td>
                        <?php elseif ($moodle_courses_enable == '1'): ?>
                        <td class="product-moodlegroup">
                            -
                        </td>
                        <?php endif; ?>
						<td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>">
							<?php
								echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
							?>
						</td>
					</tr>
					<?php
				}
			}
			?>

			<?php do_action( 'woocommerce_cart_contents' ); ?>

			<tr>
				<td colspan="6" class="actions">

					<?php if ( wc_coupons_enabled() ) { ?>
						<div class="coupon">
							<label for="coupon_code"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" /> <button type="submit" class="button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?></button>
							<?php do_action( 'woocommerce_cart_coupon' ); ?>
						</div>
					<?php } ?>

					<button type="submit" class="button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></button>

					<?php do_action( 'woocommerce_cart_actions' ); ?>

					<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
				</td>
			</tr>

			<?php do_action( 'woocommerce_after_cart_contents' ); ?>
		</tbody>
	</table>
	<?php do_action( 'woocommerce_after_cart_table' ); ?>
</form>

<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>

<div class="cart-collaterals">
	<?php
		/**
		 * Cart collaterals hook.
		 *
		 * @hooked woocommerce_cross_sell_display
		 * @hooked woocommerce_cart_totals - 10
		 */
		do_action( 'woocommerce_cart_collaterals' );
	?>
</div>

<?php do_action( 'woocommerce_after_cart' ); ?>