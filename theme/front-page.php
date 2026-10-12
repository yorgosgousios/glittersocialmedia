<?php
/**
 * Home: hero, ticker, intro (Home page content), services list, work, clients, testimonials, CTA.
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<section class="hero">
		<div class="hero__inner container container--wide">
			<div class="hero__text">
				<p class="eyebrow hero__eyebrow"><?php esc_html_e( 'Social media agency', 'glitter' ); ?></p>
				<h1 class="hero__title">
					<?php echo glitter_highlight( __( 'Social media strategies that *sparkle*', 'glitter' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in glitter_highlight() ?>
				</h1>
				<div class="hero__bottom">
					<?php if ( has_excerpt() ) : ?>
						<p class="hero__lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
					<?php endif; ?>
					<div class="hero__actions">
						<a class="btn btn--big" href="<?php echo esc_url( glitter_page_url( 'epikoinonia' ) ); ?>"><?php esc_html_e( 'Start a project', 'glitter' ); ?> <?php echo glitter_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
						<a class="btn btn--line btn--big" href="<?php echo esc_url( glitter_archive_url( 'case_study' ) ); ?>"><?php esc_html_e( 'See our work', 'glitter' ); ?></a>
					</div>
				</div>
			</div>
			<div class="hero__art" aria-hidden="true">
				<span class="hero__glow"></span>
				<span class="hero__string"></span>
				<?php echo glitter_sticker( 'disco', 'hero__ball' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php echo glitter_sticker( 'sparkle', 'hero__glint hero__glint--1' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php echo glitter_sticker( 'sparkle', 'hero__glint hero__glint--2' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		</div>
	</section>

	<?php get_template_part( 'template-parts/marquee' ); ?>

	<?php if ( trim( get_the_content() ) ) : ?>
		<section class="section intro">
			<div class="container container--wide intro__grid">
				<p class="eyebrow reveal"><?php esc_html_e( 'Hi, we’re Glitter', 'glitter' ); ?></p>
				<div class="entry-content intro__text reveal">
					<?php the_content(); ?>
					<p><a class="link-arrow" href="<?php echo esc_url( glitter_page_url( 'sxetika' ) ); ?>"><?php esc_html_e( 'Meet the team', 'glitter' ); ?></a></p>
				</div>
			</div>
		</section>
	<?php endif; ?>
	<?php
endwhile;

$glitter_services = glitter_query( 'service', 6 );
if ( $glitter_services->have_posts() ) :
	?>
	<section class="section section--tint services">
		<div class="container container--wide">
			<div class="section__head reveal">
				<p class="eyebrow"><?php esc_html_e( 'What we do', 'glitter' ); ?></p>
				<h2 class="section__title"><?php echo glitter_highlight( __( 'Everything your feed *needs*', 'glitter' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in glitter_highlight() ?></h2>
				<a class="link-arrow" href="<?php echo esc_url( glitter_archive_url( 'service' ) ); ?>"><?php esc_html_e( 'All services', 'glitter' ); ?></a>
			</div>
			<ul class="service-list">
				<?php
				while ( $glitter_services->have_posts() ) :
					$glitter_services->the_post();
					get_template_part( 'template-parts/service-row', null, [ 'index' => $glitter_services->current_post ] );
				endwhile;
				wp_reset_postdata();
				?>
			</ul>
		</div>
	</section>
	<?php
endif;

$glitter_work = glitter_query( 'case_study', 3 );
if ( $glitter_work->have_posts() ) :
	?>
	<section class="section work">
		<div class="container container--wide">
			<div class="section__head reveal">
				<p class="eyebrow"><?php esc_html_e( 'Selected work', 'glitter' ); ?></p>
				<h2 class="section__title"><?php echo glitter_highlight( __( 'Brands we made *shine*', 'glitter' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in glitter_highlight() ?></h2>
				<a class="link-arrow" href="<?php echo esc_url( glitter_archive_url( 'case_study' ) ); ?>"><?php esc_html_e( 'All projects', 'glitter' ); ?></a>
			</div>
			<ul class="work-grid">
				<?php
				while ( $glitter_work->have_posts() ) :
					$glitter_work->the_post();
					get_template_part( 'template-parts/work-card' );
				endwhile;
				wp_reset_postdata();
				?>
			</ul>
		</div>
	</section>
<?php endif; ?>

<?php get_template_part( 'template-parts/clients' ); ?>
<?php get_template_part( 'template-parts/testimonials' ); ?>
<?php get_template_part( 'template-parts/cta' ); ?>

<?php
get_footer();
