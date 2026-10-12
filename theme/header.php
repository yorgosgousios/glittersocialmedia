<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#ecdafc">
	<script>document.documentElement.classList.add('js')</script>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'glitter' ); ?></a>

<header class="site-header" data-header>
	<div class="site-header__inner container container--wide">
		<?php glitter_logo(); ?>

		<nav class="site-nav" aria-label="<?php esc_attr_e( 'Primary', 'glitter' ); ?>">
			<?php
			wp_nav_menu(
				[
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'menu',
					'depth'          => 2,
					'fallback_cb'    => false,
				]
			);
			?>
		</nav>

		<div class="site-header__actions">
			<?php glitter_language_switcher(); ?>
			<a class="btn btn--small site-header__cta" href="<?php echo esc_url( glitter_page_url( 'epikoinonia' ) ); ?>"><?php esc_html_e( 'Let’s talk', 'glitter' ); ?></a>
			<button class="icon-btn nav-toggle" type="button" data-nav-open aria-controls="mobile-nav" aria-expanded="false" aria-label="<?php esc_attr_e( 'Open menu', 'glitter' ); ?>">
				<?php echo glitter_icon( 'menu' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</button>
		</div>
	</div>
</header>

<div class="offcanvas" id="mobile-nav" data-offcanvas data-lenis-prevent hidden>
	<div class="offcanvas__top">
		<?php glitter_logo(); ?>
		<button class="icon-btn" type="button" data-nav-close aria-label="<?php esc_attr_e( 'Close menu', 'glitter' ); ?>">
			<?php echo glitter_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</button>
	</div>
	<nav aria-label="<?php esc_attr_e( 'Mobile', 'glitter' ); ?>">
		<?php
		wp_nav_menu(
			[
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'offcanvas__menu',
				'depth'          => 2,
				'fallback_cb'    => false,
			]
		);
		?>
	</nav>
	<div class="offcanvas__bottom">
		<?php glitter_language_switcher(); ?>
		<?php glitter_social_links(); ?>
	</div>
</div>

<main id="main" class="site-main">
