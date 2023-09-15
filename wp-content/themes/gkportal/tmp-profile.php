<?php // Template Name: Profile ?>
<?php acf_form_head(); ?>
<?php get_header(); ?>
	<div class="container mx-auto my-8">
		<?php if (have_posts()) : ?>
			<?php while (have_posts()) : ?>
				<?php the_post(); ?>
				<?php if (is_user_logged_in()) : ?>
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<header class="entry-header mb-4">
							<h1 class="entry-title text-2xl lg:text-5xl font-extrabold leading-tight mb-1">Profile</h1>
						</header>
						<div class="entry-content">
							<?php the_content(); ?>
							<?php
								$user_player_profile_id = get_field('user_player_profile', 'user_' . get_current_user_id());
								if ($user_player_profile_id) {
									echo '<p>You can update your profile below. To change your account password, please <a href="/change-password/">click here</a>.</p>';
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
				<?php else : ?>
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<header class="entry-header mb-4">
							<h1 class="entry-title text-2xl lg:text-5xl font-extrabold leading-tight mb-1">Profile</h1>
						</header>
						<div class="entry-content">
							<div class="flex flex-col lg:flex-row lg:justify-between gap-2">
							<div class="login-form-wrapper w-[calc(50%-1em)] p-6 border">
								<h2 class="entry-title text-xl lg:text-3xl font-extrabold mb-4">Login</h2>
								<?php if (isset($_GET['login']) && $_GET['login'] == 'failed') : ?>
									<div id="message" class="py-3 px-4 bg-red-500/10 border border-red-500/20 mb-6">
										<p class="!mb-0"><strong>Login failed.</strong> Please check your details and try again. Alternatively, you can <a href="/reset-password/">reset your password</a>.</p>
									</div>
								<?php endif; ?>
								<?php if (isset($_GET['password']) && $_GET['password'] == 'changed') : ?>
									<div id="message" class="py-3 px-4 bg-green-500/10 border border-green-500/20">
										<p class="!mb-0">Password successfully updated. Please log in below.</p>
									</div>
								<?php endif; ?>
								<?php if (isset($_GET['loggedout']) && $_GET['loggedout'] == 'true') : ?>
									<div id="message" class="py-3 px-4 bg-green-500/10 border border-green-500/20 mb-6">
										<p class="!mb-0">You've successfully logged out.</p>
									</div>
								<?php endif; ?>
								<?php
									$args = array(
										'redirect' => get_permalink(get_the_ID()),
										'form_id' => 'custom_loginform',
										'label_username' => __('Username / Email Address'),
										'label_password' => __('Password'),
										'label_remember' => __('Remember Me'),
										'label_log_in' => __('Log In'),
										'remember' => true
									);
									wp_login_form($args);
								?>
								<p class="forgotten-password"><a href="/reset-password/">Forgotten password</a></p>
							</div>
							<div class="register-form-wrapper w-[calc(50%-0.5em)] p-6 border">
								<h2 class="entry-title text-xl lg:text-3xl font-extrabold mb-4">Register</h2>
								<?php echo do_shortcode('[register_form]'); ?>
							</div>
						</div>
					</article>
				<?php endif; ?>
			<?php endwhile; ?>
		<?php endif; ?>
	</div>
<?php get_footer();