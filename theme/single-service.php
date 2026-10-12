<?php
/**
 * Single service: description, what's included, related work.
 */

get_header();

while ( have_posts() ) :
	the_post();
	$glitter_items = glitter_lines( get_the_ID(), 'deliverables' );
	glitter_page_header( get_the_title(), __( 'Service', 'glitter' ), has_excerpt() ? get_the_excerpt() : '' );
	?>
	<section class="section section--tight">
		<div class="container container--wide service-detail">
			<div class="entry-content service-detail__body"><?php the_content(); ?></div>
			<?php if ( $glitter_items ) : ?>
				<aside class="included reveal">
					<h2 class="included__title"><?php esc_html_e( 'What’s included', 'glitter' ); ?></h2>
					<ul>
						<?php foreach ( $glitter_items as $glitter_item ) : ?>
							<li><?php echo glitter_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( $glitter_item ); ?></li>
						<?php endforeach; ?>
					</ul>
					<a class="btn" href="<?php echo esc_url( glitter_page_url( 'epikoinonia' ) ); ?>"><?php esc_html_e( 'Ask about this', 'glitter' ); ?></a>
				</aside>
			<?php endif; ?>
		</div>
	</section>
	<?php
endwhile;

$glitter_work = glitter_query( 'case_study', 3 );
if ( $glitter_work->have_posts() ) :
	?>
	<section class="section work">
		<div class="container container--wide">
			<div class="section__head reveal">
				<h2 class="section__title"><?php echo glitter_highlight( __( 'Recent *work*', 'glitter' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in glitter_highlight() ?></h2>
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

<?php get_template_part( 'template-parts/cta' ); ?>

<?php
get_footer();
