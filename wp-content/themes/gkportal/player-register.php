<?php /* Template Name: Player Register */ ?>
<?php acf_form_head(); ?>
<?php get_header(); ?>
	<div class="container mx-auto my-8">
		<?php if (have_posts()) : ?>
			<?php while (have_posts()) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header class="entry-header mb-4">
						<?php the_title(sprintf('<h1 class="entry-title text-2xl lg:text-5xl font-extrabold leading-tight mb-1"><a href="%s" rel="bookmark">', esc_url(get_permalink())), '</a></h1>'); ?>
						<time datetime="<?php echo get_the_date('c'); ?>" itemprop="datePublished" class="text-sm text-gray-700"><?php echo get_the_date(); ?></time>
					</header>
					<div class="entry-content">
						<?php the_content(); ?>
						<?php if (isset($_GET['updated']) && $_GET['updated'] == 'true') : ?>
							<p>Thank you for your submission. Your profile will be reviewed by the team and published accordingly.</p>
						<?php else : ?>
							<?php
								acf_form(array(
									'post_id'       => 'new_post',
									'post_title'    => false,
									'post_content'  => false,
									'new_post'      => array(
										'post_type'     => 'player',
										'post_status'   => 'pending'
									)
								));
							?>
						<?php endif; ?>
					</div>
			<?php endwhile; ?>
		<?php endif; ?>
	</div>
<?php get_footer();