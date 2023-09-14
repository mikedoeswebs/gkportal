<?php // Template Name: Profile ?>
<?php acf_form_head(); ?>
<?php get_header(); ?>
	<div class="container mx-auto my-8">
		<?php if (have_posts()) : ?>
			<?php while (have_posts()) : ?>
				<?php the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header class="entry-header mb-4">
						<?php the_title(sprintf('<h1 class="entry-title text-2xl lg:text-5xl font-extrabold leading-tight mb-1"><a href="%s" rel="bookmark">', esc_url(get_permalink())), '</a></h1>'); ?>
					</header>
					<div class="entry-content">
						<?php the_content(); ?>
						<?php
							$user_player_profile_id = get_field('user_player_profile', 'user_' . get_current_user_id());
							if ($user_player_profile_id) {
								echo '<p>You can update your profile below.</p>';
								acf_form(
									array(
										'post_id' => $user_player_profile_id,
										'submit_value' => 'Update Profile',
										'updated_message' => 'Your profile has been updated.',
									)
								);
							} else {
								echo '<p>You can create your profile below to start attracting interest.</p>
								<p><strong>Please note:</strong> On submission, your profile will be manually reviewed by our team before being approved to appear on the website.</p>';
								acf_form(
									array(
										'post_id' => 'new_post',
										'new_post' => array(
											'post_type' => 'player',
											'post_status' => 'pending',
										),
										'submit_value' => 'Register',
									)
								);
							}
						?>
					</div>
				</article>
			<?php endwhile; ?>
		<?php endif; ?>
	</div>
<?php get_footer();