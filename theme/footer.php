</main>

<footer class="site-footer">
	<div class="container container--wide">
		<div class="site-footer__top">
			<div class="site-footer__hello">
				<p class="eyebrow eyebrow--light"><?php esc_html_e( 'Say hi', 'glitter' ); ?></p>
				<?php $glitter_email = glitter_contact_email(); ?>
				<a class="site-footer__email" href="mailto:<?php echo esc_attr( antispambot( $glitter_email ) ); ?>"><?php echo esc_html( antispambot( $glitter_email ) ); ?></a>
				<?php glitter_social_links( 'social--big' ); ?>
			</div>
			<nav class="site-footer__nav" aria-label="<?php esc_attr_e( 'Footer', 'glitter' ); ?>">
				<?php
				wp_nav_menu(
					[
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'footer-menu',
						'depth'          => 1,
						'fallback_cb'    => false,
					]
				);
				?>
			</nav>
		</div>

		<p class="site-footer__wordmark" aria-hidden="true">glitter<?php echo glitter_sticker( 'sparkle', 'site-footer__spark' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>

		<div class="site-footer__bottom">
			<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'Strategies that sparkle.', 'glitter' ); ?></p>
			<?php
			wp_nav_menu(
				[
					'theme_location' => 'footer',
					'container'      => false,
					'menu_class'     => 'legal-menu',
					'depth'          => 1,
					'fallback_cb'    => false,
				]
			);
			?>
			<?php glitter_language_switcher(); ?>
		</div>
	</div>
</footer>

<button class="to-top icon-btn" type="button" data-to-top aria-label="<?php esc_attr_e( 'Back to top', 'glitter' ); ?>">
	<?php echo glitter_icon( 'arrow-up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
</button>

<?php wp_footer(); ?>
</body>
</html>
