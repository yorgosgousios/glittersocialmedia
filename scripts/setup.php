<?php
/**
 * Local `make install` step: runs the same setup as Appearance → Glitter setup.
 * See theme/inc/site-setup.php. Run via wp eval-file /scripts/setup.php.
 */

try {
	glitter_site_setup( [ 'WP_CLI', 'log' ] );
} catch ( RuntimeException $e ) {
	WP_CLI::error( $e->getMessage() );
}
WP_CLI::success( 'Setup complete.' );
