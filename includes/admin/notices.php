<?php
/**
 * Bootstraps and outputs notices.
 *
 * @package EDD_Stripe
 * @since   2.6.19
 */

/**
 * Registers scripts to manage dismissing notices.
 *
 * @since 2.6.19
 */
function edds_admin_notices_scripts() {
	wp_register_script(
		'edds-admin-notices',
		EDDSTRIPE_PLUGIN_URL . 'assets/js/build/notices.min.js',
		array(
			'wp-util',
		)
	);
}
add_action( 'admin_enqueue_scripts', 'edds_admin_notices_scripts' );

/**
 * Registers admin notices.
 *
 * @since 2.6.19
 *
 * @return true|WP_Error True if all notices are registered, otherwise WP_Error.
 */
function edds_admin_notices_register() {
	$registry = edds_get_registry( 'admin-notices' );

	if ( ! $registry ) {
		return new WP_Error( 'edds-invalid-registry', esc_html__( 'Unable to locate registry', 'edds' ) );
	}

	try {
		// Upcoming PHP requirement change.
		$registry->add(
			'php-56-requirement',
			array(
				'message'     => function() {
					ob_start();
					require_once EDDS_PLUGIN_DIR . '/includes/admin/notices/php-56-requirement.php';
					return ob_get_clean();
				},
				'type'        => 'error',
				'dismissible' => false,
			)
		);

		// Recurring 2.10.0 requirement.
		$registry->add(
			'recurring-2100-requirement',
			array(
				'message'     => wpautop(
					wp_kses(
						'<strong> ' .
						__( 'Credit card payments with Stripe are currently disabled.', 'edds' ) .
						'</strong>'
						. '<br />' .
						sprintf(
							/* translators: %s Required Recurring Payments plugin version. */
							__( 'To continue accepting credit card payments with Stripe please update the Recurring Payments extension to version %s', 'edds' ),
							'<code>2.10</code>'
						),
						array(
							'br'     => true,
							'strong' => true,
							'code'   => true,
						)
					)
				),
				'type'        => 'error',
				'dismissible' => false,
			)
		);
	} catch ( Exception $e ) {
		return new WP_Error( 'edds-invalid-notices-registration', esc_html__( $e->getMessage() ) );
	};

	return true;
}
add_action( 'admin_init', 'edds_admin_notices_register' );

/**
 * Conditionally prints registered notices.
 *
 * @since 2.6.19
 */
function edds_admin_notices_print() {
	// Current user needs capability to dismiss notices.
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$registry = edds_get_registry( 'admin-notices' );

	if ( ! $registry ) {
		return;
	}

	$notices = new EDD_Stripe_Admin_Notices( $registry );

	wp_enqueue_script( 'edds-admin-notices' );

	try {
		// Upcoming PHP requirement change.
		if ( version_compare( phpversion(), '5.6', '<' ) ) {
			$notices->output( 'php-56-requirement' );
		}

		// Recurring 2.10.0 requirement.
		if ( defined( 'EDD_RECURRING_VERSION' ) && ! version_compare( EDD_RECURRING_VERSION, '2.9.99', '>' ) ) {
			$notices->output( 'recurring-2100-requirement' );
		}
	} catch( Exception $e ) {}
}
add_action( 'admin_notices', 'edds_admin_notices_print' );

/**
 * Handles AJAX dismissal of notices.
 *
 * WordPress automatically removes the notices, so the response here is arbitrary.
 * If the notice cannot be dismissed it will simply reappear when the page is refreshed.
 *
 * @since 2.6.19
 */
function edds_admin_notices_dismiss_ajax() {
	$notice_id = isset( $_REQUEST[ 'id' ] ) ? esc_attr( $_REQUEST['id'] ) : false;
	$nonce     = isset( $_REQUEST[ 'nonce' ] ) ? esc_attr( $_REQUEST['nonce'] ) : false;

	if ( ! ( $notice_id && $nonce ) ) {
		return wp_send_json_error();
	}

	if ( ! wp_verify_nonce( $nonce, "edds-dismiss-{$notice_id}-nonce" ) ) {
		return wp_send_json_error();
	}

	$registry = edds_get_registry( 'admin-notices' );

	if ( ! $registry ) {
		return wp_send_json_error();
	}

	$notices   = new EDD_Stripe_Admin_Notices( $registry );
	$dismissed = $notices->dismiss( $notice_id );

	if ( true === $dismissed ) {
		return wp_send_json_success();
	} else {
		return wp_send_json_error();
	}
}
add_action( 'wp_ajax_edds_admin_notices_dismiss_ajax', 'edds_admin_notices_dismiss_ajax' );
