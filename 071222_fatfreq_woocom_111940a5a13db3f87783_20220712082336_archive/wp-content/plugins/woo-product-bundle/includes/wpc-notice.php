<?php
defined( 'ABSPATH' ) || exit;

$theme = wp_get_theme();

if ( 'WPCstore' == $theme ) {
	return;
}

if ( ! class_exists( 'WPCleverNotice' ) ) {
	class WPCleverNotice {
		function __construct() {
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );
			add_action( 'admin_init', array( $this, 'notice_ignore' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'notice_scripts' ) );
		}

		function notice_scripts() {
			wp_enqueue_style( 'wpclever-notice', WOOSB_URI . 'assets/css/notice.css' );
		}

		function admin_notice() {
			global $current_user, $current_screen;
			$user_id = $current_user->ID;

			if ( ! $current_screen || ! isset( $current_screen->base ) || ( strpos( $current_screen->base, 'wpclever' ) === false ) ) {
				return;
			}

			if ( class_exists( 'THNotice' ) && ! get_user_meta( $user_id, 'th_thunk_notice_ignore', true ) ) {
				return;
			}

			if ( ! get_user_meta( $user_id, 'wpclever_wpcstore_ignore', true ) ) {
				?>
                <div class="wpclever-notice notice">
                    <div class="wpclever-notice-thumbnail">
                        <a href="https://wordpress.org/themes/wpcstore/" target="_blank">
                            <img src="<?php echo WOOSB_URI . 'assets/images/wpc-store.png'; ?>" alt="WPCstore"/>
                        </a>
                    </div>
                    <div class="wpclever-notice-text">
                        <h3>WPCstore - Powerful WooCommerce Theme</h3>
                        <p>
                            Integrated with a great deal of awesome features from WPC plugins trusted by 250,000+ users
                            on WordPress, WPCstore brings about an easier way of running your store, enhancing the
                            experience, and expanding your business. This well-coded, sleek, user-friendly, and
                            responsive theme can do wonders for online stores, even exceed your expectations. Get rid of
                            your pains concerning incompatibility, bug-fixing, asynchronous performance, etc and enjoy a
                            higher stability, security, and harmony with WPCstore.
                        </p>
                        <ul class="wpclever-notice-ul">
                            <li class="show-mor-message">
                                <a href="https://demo.wpclever.net/wpcstore/" target="_blank">
                                    <span class="dashicons dashicons-desktop"></span> Live Demo
                                </a>
                            </li>
                            <li class="free-download-message">
                                <a href="https://wordpress.org/themes/wpcstore/" target="_blank">
                                    <span class="dashicons dashicons-external"></span> Check Detail
                                </a>
                            </li>
                            <li class="hide-message">
                                <a href="<?php echo admin_url( '?wpclever_wpcstore_ignore=1' ); ?>"
                                   class="dashicons-dismiss-icon">
                                    <span class="dashicons dashicons-welcome-comments"></span> Hide Message
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
				<?php
			}
		}

		function notice_ignore() {
			global $current_user;
			$user_id = $current_user->ID;

			if ( isset( $_GET['wpclever_wpcstore_ignore'] ) ) {
				if ( $_GET['wpclever_wpcstore_ignore'] == '1' ) {
					update_user_meta( $user_id, 'wpclever_wpcstore_ignore', 'true' );
				} else {
					delete_user_meta( $user_id, 'wpclever_wpcstore_ignore' );
				}
			}
		}
	}

	new WPCleverNotice();
}