<?php get_header(); ?>
	<div class="container my-8 mx-auto">
		<?php if (have_posts()) : ?>
			<?php while (have_posts()) : the_post(); ?>
				<?php get_template_part('template-parts/content', 'single'); ?>
				<?php if (comments_open() || get_comments_number()) {
					comments_template();
				} ?>
			<?php endwhile; ?>
		<?php endif; ?>
	</div>
<?php get_footer();