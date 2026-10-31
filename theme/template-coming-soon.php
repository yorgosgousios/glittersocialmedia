<?php
/**
 * Coming soon holding page, served by inc/coming-soon.php (not a page template).
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#ecdafc">
	<meta name="robots" content="noindex, nofollow">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'coming-soon' ); ?>>
<main id="main" class="coming-soon__main">
	<div class="hero__art" aria-hidden="true">
		<span class="hero__glow"></span>
		<span class="hero__string"></span>
		<?php echo glitter_sticker( 'disco', 'hero__ball' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php echo glitter_sticker( 'sparkle', 'hero__glint hero__glint--1' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php echo glitter_sticker( 'sparkle', 'hero__glint hero__glint--2' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</div>
	<?php $glitter_email = glitter_contact_email(); ?>
	<p class="coming-soon__text">
		<?php esc_html_e( 'Our new website is almost ready. Until then, say hi:', 'glitter' ); ?>
		<a href="mailto:<?php echo esc_attr( antispambot( $glitter_email ) ); ?>"><?php echo esc_html( antispambot( $glitter_email ) ); ?></a>
	</p>
	<a class="btn btn--line btn--small" href="<?php echo esc_url( wp_login_url( home_url( add_query_arg( [] ) ) ) ); ?>"><?php esc_html_e( 'Log in', 'glitter' ); ?></a>
</main>
<?php wp_footer(); ?>
</body>
</html>
