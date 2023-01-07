<?php
/**
 * Internationalization
 *
 * @package EDD_Stripe
 * @copyright Copyright (c) 2020, Sandhills Development, LLC
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since 2.8.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns a list of error codes and corresponding localized error messages.
 *
 * @since 2.8.0
 *
 * @return array $error_list List of error codes and corresponding error messages.
 */
function edds_get_localized_error_messages() {
	$error_list = array(
		'invalid_number'           => __( 'The card number is not a valid credit card number.', 'edds' ),
		'invalid_expiry_month'     => __( 'The card\'s expiration month is invalid.', 'edds' ),
		'invalid_expiry_year'      => __( 'The card\'s expiration year is invalid.', 'edds' ),
		'invalid_cvc'              => __( 'The card\'s security code is invalid.', 'edds' ),
		'incorrect_number'         => __( 'The card number is incorrect.', 'edds' ),
		'incomplete_number'        => __( 'The card number is incomplete.', 'edds' ),
		'incomplete_cvc'           => __( 'The card\'s security code is incomplete.', 'edds' ),
		'incomplete_expiry'        => __( 'The card\'s expiration date is incomplete.', 'edds' ),
		'expired_card'             => __( 'The card has expired.', 'edds' ),
		'incorrect_cvc'            => __( 'The card\'s security code is incorrect.', 'edds' ),
		'incorrect_zip'            => __( 'The card\'s zip code failed validation.', 'edds' ),
		'invalid_expiry_year_past' => __( 'The card\'s expiration year is in the past', 'edds' ),
		'card_declined'            => __( 'The card was declined.', 'edds' ),
		'processing_error'         => __( 'An error occurred while processing the card.', 'edds' ),
		'invalid_request_error'    => __( 'Unable to process this payment, please try again or use alternative method.', 'edds' ),
		'email_invalid'            => __( 'Invalid email address, please correct and try again.', 'edds' ),
	);

	/**
	 * Filters the list of available error codes and corresponding error messages.
	 *
	 * @since 2.8.0
	 *
	 * @param array $error_list List of error codes and corresponding error messages.
	 */
	$error_list = apply_filters( 'edds_get_localized_error_list', $error_list );

	return $error_list;
}

/**
 * Returns a localized error message for a corresponding Stripe
 * error code.
 *
 * @link https://stripe.com/docs/error-codes
 *
 * @since 2.8.0
 *
 * @param string $error_code Error code.
 * @param string $error_message Original error message to return if a localized version does not exist.
 * @return string $error_message Potentially localized error message.
 */
function edds_get_localized_error_message( $error_code, $error_message ) {
	$error_list = edds_get_localized_error_messages();

	if ( ! empty( $error_list[ $error_code ] ) ) {
		return $error_list[ $error_code ];
	}

	return $error_message;
}
