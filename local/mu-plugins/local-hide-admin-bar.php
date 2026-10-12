<?php
/**
 * Plugin Name: Local: hide admin bar
 * Description: Hides the front-end admin toolbar so the local site looks like production. Local only — not deployed.
 *
 * @package Glitter
 */

if ( wp_get_environment_type() !== 'local' ) {
	return;
}

add_filter( 'show_admin_bar', '__return_false' );
