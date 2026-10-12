<?php
/**
 * Plugin Name: Local Mailpit
 * Description: Routes all wp_mail() through the local Mailpit container. Local only — not deployed.
 *
 * @package Glitter
 */

// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- PHPMailer's own property names.

if ( wp_get_environment_type() !== 'local' ) {
	return;
}

add_action(
	'phpmailer_init',
	function ( $phpmailer ) {
		$phpmailer->isSMTP();
		$phpmailer->Host        = 'mailpit';
		$phpmailer->Port        = 1025;
		$phpmailer->SMTPAuth    = false;
		$phpmailer->SMTPAutoTLS = false;
	}
);
