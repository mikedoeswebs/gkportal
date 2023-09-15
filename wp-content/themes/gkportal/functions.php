<?php

/**
 * Theme setup.
 */
function tailpress_setup() {
	add_theme_support('title-tag');

	register_nav_menus(
		array(
			'primary' => __('Primary Menu', 'tailpress'),
		)
	);

	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		)
	);

    add_theme_support('custom-logo');
	add_theme_support('post-thumbnails');

	add_theme_support('align-wide');
	add_theme_support('wp-block-styles');

	add_theme_support('editor-styles');
	add_editor_style('css/editor-style.css');
}

add_action('after_setup_theme', 'tailpress_setup');

/**
 * Enqueue theme assets.
 */
function tailpress_enqueue_scripts() {
	$theme = wp_get_theme();
	wp_enqueue_style('tailpress', tailpress_asset('css/app.css'), array(), $theme->get('Version'));
	wp_enqueue_script('tailpress', tailpress_asset('js/app.js'), array(), $theme->get('Version'), array('in_footer' => true));
}

add_action('wp_enqueue_scripts', 'tailpress_enqueue_scripts');

/**
 * Get asset path.
 *
 * @param string  $path Path to asset.
 *
 * @return string
 */
function tailpress_asset($path) {
	if (wp_get_environment_type() === 'production') {
		return get_stylesheet_directory_uri() . '/' . $path;
	}
	return add_query_arg('time', time(),  get_stylesheet_directory_uri() . '/' . $path);
}

/**
 * Adds option 'li_class' to 'wp_nav_menu'.
 *
 * @param string  $classes String of classes.
 * @param mixed   $item The current item.
 * @param WP_Term $args Holds the nav menu arguments.
 *
 * @return array
 */
function tailpress_nav_menu_add_li_class($classes, $item, $args, $depth) {
	if (isset($args->li_class)) {
		$classes[] = $args->li_class;
	}
	if (isset($args->{"li_class_$depth"})) {
		$classes[] = $args->{"li_class_$depth"};
	}
	return $classes;
}
add_filter('nav_menu_css_class', 'tailpress_nav_menu_add_li_class', 10, 4);

/**
 * Adds option 'submenu_class' to 'wp_nav_menu'.
 *
 * @param string  $classes String of classes.
 * @param mixed   $item The current item.
 * @param WP_Term $args Holds the nav menu arguments.
 *
 * @return array
 */
function tailpress_nav_menu_add_submenu_class($classes, $args, $depth) {
	if (isset($args->submenu_class)) {
		$classes[] = $args->submenu_class;
	}
	if (isset($args->{"submenu_class_$depth"})) {
		$classes[] = $args->{"submenu_class_$depth"};
	}
	return $classes;
}
add_filter('nav_menu_submenu_css_class', 'tailpress_nav_menu_add_submenu_class', 10, 3);


/**
 * Stop non-admins accessing dashboard
 */
function redirect_subscribers_from_dashboard() {
	if (is_user_logged_in() && is_admin() && !current_user_can('administrator') && !(defined('DOING_AJAX') && DOING_AJAX)) {
		wp_redirect(home_url());
		exit;
	}
}
add_action('init', 'redirect_subscribers_from_dashboard');


/**
 * Hide admin bar for non-admins
 */
function hide_admin_bar_for_non_admins() {
	if (!current_user_can('administrator') && !(defined('DOING_AJAX') && DOING_AJAX)) {
		add_filter('show_admin_bar', '__return_false');
	}
}
add_action('init', 'hide_admin_bar_for_non_admins');


/**
 * Update user field on profile creation
 */
if (!is_admin()) {
	add_action('acf/save_post', 'set_player_post_title_on_save', 20);
}
function set_player_post_title_on_save($post_id){
	$user_id = get_post_field('post_author', $post_id);
	update_field('user_player_profile', $post_id, 'user_' . $user_id);

	$new_title = get_field('player_first_name', $post_id) . ' ' . get_field('player_last_name', $post_id);
	$new_post = array(
		'ID'           => $post_id,
		'post_title'   => $new_title,
	);
	remove_action('acf/save_post', 'set_player_post_title_on_save', 20);
	wp_update_post($new_post);
	add_action('acf/save_post', 'set_player_post_title_on_save', 20);
}


/// LOGIN STUFF


/**
 * Redirect "Staff Member" users to the "My Account" page (if it exists)
 */
function login_redirect_based_on_roles($user_login, $user){
	$account_page = get_page_by_path('profile' , OBJECT);
	if (isset($account_page) && in_array('subscriber', $user->roles)) {
		exit(wp_redirect(get_permalink($account_page->ID)));
	}
}
add_action('wp_login', 'login_redirect_based_on_roles', 10, 2);


/**
 * Handles login failure to prevent user landing on default WordPress login form
 */
function login_failed($username){
	$referrer = wp_get_referer();
	if ($referrer && ! strstr($referrer, 'wp-login') && ! strstr($referrer,'wp-admin')) {
		wp_redirect(add_query_arg('login', 'failed', $referrer));
		exit;
	}
}
add_action('wp_login_failed', 'login_failed');

function authenticate_username_password($user, $username, $password){
	if (is_a($user, 'WP_User')) {
		return $user;
	}
	if (empty($username) || empty($password)){
		$error = new WP_Error();
		$user  = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid username or incorrect password.'));
		return $error;
	}
}
add_filter('authenticate', 'authenticate_username_password', 30, 3);


/**
 * Renders the first stage of the password reset process
 */
function lost_password_form($attributes, $content = null){
	ob_start();
	$default_attributes = array('show_title' => false);
	$attributes = shortcode_atts($default_attributes, $attributes);

	if (is_user_logged_in()) {
		return __('<div class="py-3 px-4 bg-blue-500/10 border border-blue-500/20"><p class="!mb-0">You are already signed in.</p></div>', 'player-portal');
	} else if (!isset($_REQUEST['login'])) {
		if (isset($_REQUEST['errors'])) {
			echo '<div class="py-3 px-4 bg-red-500/10 border border-red-500/20"><p class="!mb-0">';
				switch($_REQUEST['errors']){
					case 'empty_username':
						_e(' You need to enter your email address to continue.', 'personalize-login');
						break;
					case 'invalid_email':
					case 'invalidcombo':
						_e(' There are no users registered with this email address.', 'personalize-login');
				}
			echo '</p></div>';
		}
		if (isset($_REQUEST['checkemail'])) {
			echo '<div class="py-3 px-4 bg-green-500/10 border border-green-500/20"><p class="!mb-0">';
				switch($_REQUEST['checkemail']){
					case 'confirm':
						_e('Password reset instructions have been emailed to you.', 'player-portal');
				}
			echo '</p></div>';
		} ?>
		<div id="password-lost-form" class="widecolumn">
			<p>Submit the form below and we'll send you an email allowing you to reset your password.</p>
			<form id="lostpasswordform" action="<?php echo wp_lostpassword_url(); ?>" method="post">
				<p class="form-row">
					<label class="block font-bold mb-2" for="user_login"><?php _e('Username or Email Address', 'player-portal'); ?></label>
					<input type="text" name="user_login" id="user_login" class="py-3 px-4 border w-full">
				</p>
				<p class="lostpassword-submit">
					<input type="submit" name="submit" class="lostpassword-button inline-flex items-center bg-blue-500 hover:bg-blue-700 text-white no-underline py-3 px-4 rounded transition-all cursor-pointer" value="<?php _e('Request Password Reset', 'player-portal'); ?>"/>
				</p>
				<p><a href="/profile/">Back to login</a></p>
			</form>
		</div>
		<?php
	}
	$reset_password_form = ob_get_clean();
	return $reset_password_form;
}

add_shortcode('reset_password_form', 'lost_password_form');


/**
 * Directs user accordingly when trying to reset password
 */
function do_password_lost(){
	if ('POST' == $_SERVER['REQUEST_METHOD']) {
		$errors = retrieve_password();
		if (is_wp_error($errors)) {
			$redirect_url = home_url('reset-password');
			$redirect_url = add_query_arg('errors', join(',', $errors->get_error_codes()), $redirect_url);
		} else {
			$redirect_url = home_url('reset-password');
			$redirect_url = add_query_arg('checkemail', 'confirm', $redirect_url);
		}
		wp_redirect($redirect_url);
		exit;
	}
}
add_action('login_form_lostpassword', 'do_password_lost');


/**
 * Renders the second stage of the password reset process
 */
function render_password_reset_form($attributes, $content = null){
	ob_start();
	$reset_errors = new WP_Error;
	$default_attributes = array('show_title' => false);
	$attributes = shortcode_atts($default_attributes, $attributes);

	if (is_user_logged_in()) {
		return __('You are already signed in.', 'player-portal');
	} else {
		if (isset($_REQUEST['login'])) {
			if (isset($_REQUEST['key'])) {
				$attributes['login'] = $_REQUEST['login'];
				$attributes['key'] = $_REQUEST['key'];
				?>
				<div id="password-reset-form" class="widecolumn">
					<form name="resetpassform" id="resetpassform" action="<?php echo site_url('wp-login.php?action=resetpass'); ?>" method="post" autocomplete="off">
						<input type="hidden" id="user_login" name="rp_login" value="<?php echo esc_attr($attributes['login']); ?>" autocomplete="off"/>
						<input type="hidden" name="rp_key" value="<?php echo esc_attr($attributes['key']); ?>"/>
						<?php if (isset($_REQUEST['error'])) : ?>
							<?php $error_codes = explode(',', $_REQUEST['error']); ?>

							<?php foreach ($error_codes as $code) : ?>
								<?php if ($code == 'password_reset_mismatch') : ?>
									<div class="py-3 px-4 bg-red-500/10 border border-red-500/20">
										<p><strong>Error:</strong> Passwords do not match</p>
									</div>
								<?php endif; ?>
							<?php endforeach; ?>
						<?php endif; ?>
						<p>Enter your new password below to complete your password reset.</p>
						<p class="form-row">
							<label for="pass1"><?php _e('New password', 'player-portal') ?></label>
							<input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off"/>
						</p>
						<p class="form-row">
							<label for="pass2"><?php _e('Repeat new password', 'player-portal') ?></label>
							<input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off"/>
						</p>
						<p class="resetpass-submit">
							<input type="submit" name="submit" id="resetpass-button" class="button" value="<?php _e('Reset Password', 'player-portal'); ?>"/>
						</p>
					</form>
				</div>
				<?php
			} else {
				return __('<div class="py-3 px-4 bg-red-500/10 border border-red-500/20"><p>Invalid password reset link.</p></div>', 'player-portal');
			}
		}
	}
	$password_reset_form = ob_get_clean();
	return $password_reset_form;
}
add_shortcode('custom-password-reset-form', 'render_password_reset_form');


/**
 * Handles the second stage of the password reset process
 */
function do_password_reset(){
	if ('POST' == $_SERVER['REQUEST_METHOD']) {
		$rp_key = $_REQUEST['rp_key'];
		$rp_login = $_REQUEST['rp_login'];
		$user = check_password_reset_key($rp_key, $rp_login);
		if (!$user || is_wp_error($user)) {
			if ($user && $user->get_error_code() === 'expired_key') {
				wp_redirect(home_url('profile/?login=expiredkey'));
			} else {
				wp_redirect(home_url('profile/?login=invalidkey'));
			}
			exit;
		}

		if (isset($_POST['pass1'])) {
			if ($_POST['pass1'] != $_POST['pass2']) {
				$redirect_url = home_url('reset-password');
				$redirect_url = add_query_arg('key', $rp_key, $redirect_url);
				$redirect_url = add_query_arg('login', $rp_login, $redirect_url);
				$redirect_url = add_query_arg('error', 'password_reset_mismatch', $redirect_url);
				wp_redirect($redirect_url);
				exit;
			}
			if (empty($_POST['pass1'])) {
				$redirect_url = home_url('reset-password');
				$redirect_url = add_query_arg('key', $rp_key, $redirect_url);
				$redirect_url = add_query_arg('login', $rp_login, $redirect_url);
				$redirect_url = add_query_arg('error', 'password_reset_empty', $redirect_url);
				wp_redirect($redirect_url);
				exit;
			}
			reset_password($user, $_POST['pass1']);
			wp_redirect(home_url('profile/?password=changed'));
		} else {
			echo "Invalid request.";
		}

		exit;
	}
}
add_action('login_form_rp', 'do_password_reset');
add_action('login_form_resetpass', 'do_password_reset');


/**
 * Handles the redirect following the password reset process
 *
 * @author		mikedoeswebs
 */
function redirect_to_custom_password_reset(){
	if ('GET' == $_SERVER['REQUEST_METHOD'] && isset($_REQUEST['key']) && !empty($_REQUEST['key'])) {
		$user = check_password_reset_key($_REQUEST['key'], $_REQUEST['login']);
		if (!$user || is_wp_error($user)) {
			if ($user && $user->get_error_code() === 'expired_key') {
				wp_redirect(home_url('reset-password?login=expiredkey'));
			} else {
				wp_redirect(home_url('reset-password?login=invalidkey'));
			}
			exit;
		}
		$redirect_url = home_url('reset-password');
		$redirect_url = add_query_arg('login', esc_attr($_REQUEST['login']), $redirect_url);
		$redirect_url = add_query_arg('key', esc_attr($_REQUEST['key']), $redirect_url);

		wp_redirect($redirect_url);
		exit;
	}
}
add_action('login_form_rp', 'redirect_to_custom_password_reset');
add_action('login_form_resetpass', 'redirect_to_custom_password_reset');


/**
 * Renders the change password form (not to be confused with the "Reset password" form!)
 */
function change_password_form(){
	ob_start();
	if (is_user_logged_in()) {
		global $password_errors, $password_success;

		if (!empty($password_errors)) : ?>
			<div class="py-3 px-4 bg-red-500/10 border border-red-500/20 mb-4">
				<?php echo $password_errors; ?>
			</div>
		<?php endif; ?>

		<?php if (!empty($password_success)) : ?>
			<div class="py-3 px-4 bg-green-500/10 border border-green-500/20 mb-4">
				<?php echo $password_success; ?>
			</div>
		<?php endif; ?>

		<form method="post" class="wc-change-pwd-form">
			<p class="form-row">
				<label class="block font-bold mb-2" for="user_oldpassword">Old Password</label>
				<input type="password" name="old_password" id="user_oldpassword" class="p-4 border w-full" />
			</p>
			<p class="form-row">
				<label class="block font-bold mb-2" for="user_password">New Password</label>
				<input type="password" name="user_password" id="user_password" class="p-4 border w-full" />
			</p>
			<p class="form-row">
				<label class="block font-bold mb-2" for="user_cpassword">Confirm Password</label>
				<input type="password" name="user_cpassword" id="user_cpassword" class="p-4 border w-full" />
			</p>
			<?php
				ob_start();
				do_action('password_reset');
				echo ob_get_clean();
			?>
			<p class="log_user">
				<?php wp_nonce_field('changePassword', 'formType'); ?>
				<button type="submit" class="register_user inline-flex items-center bg-blue-500 hover:bg-blue-700 text-white no-underline py-3 px-4 rounded transition-all cursor-pointer">Update Password</button>
			</p>
			<p><a href="/profile/">Back to my profile</a></p>
		</form>
	<?php }
	$change_password_form = ob_get_clean();
	return $change_password_form;
}
add_shortcode('change_password_form', 'change_password_form');


/**
 * Handles the change password process
 */
function change_password_callback(){
	if (isset($_POST['formType']) && wp_verify_nonce($_POST['formType'], 'changePassword')) {
		global $password_errors, $password_success;

		$user = wp_get_current_user();

		$password_errors = '';
		$password_success = '';
		$old_password = trim($_POST['old_password']);
		$new_password = trim($_POST['user_password']);
		$new_password_confirm = trim($_POST['user_cpassword']);

		if ($old_password == '' || $new_password == '' || $new_password_confirm == '') {
			$password_errors .= '<strong>Error: </strong> Please fill in all fields.,';
		}
		if (!wp_check_password($old_password, $user->data->user_pass, $user->ID)) {
			$password_errors .= '<strong>Error: </strong> Current password incorrect.,';
		}
		if ($new_password != $new_password_confirm) {
			$password_errors .= '<strong>Error: </strong> Password do not match.,';
		}
		if (strlen($new_password) < 4) {
			$password_errors .= '<strong>Error: </strong> Please enter a password that is at least 4 characters.,';
		}
		$password_errors = trim($password_errors, ',');
		$password_errors = str_replace(",", "<br/>", $password_errors);

		if (empty($password_errors)) {
			wp_set_password($new_password, $user->ID);
			// do_action('wp_login', $user->user_login, $user);
			wp_set_current_user($user->ID);
			wp_set_auth_cookie($user->ID);
			$password_success = 'Your password has been successfully updated.';
		}
	}
}
add_action('wp',  'change_password_callback');


/**
 * Handles the redirect after a user logs out
 */
function auto_redirect_after_logout(){
	wp_safe_redirect(home_url('profile'));
	exit;
}
add_action('wp_logout', 'auto_redirect_after_logout');


/**
 * Rewrite the contents of the email sent to new Staff Member WordPress users to make it a bit more user-friendly
 * Also rewrites the subject heading of the email and sets the email to HTML as opposed to plain text
 */
function new_user_email($wp_new_user_notification_email, $user, $blogname){
	// We only want to change the email content for new Staff Member users
	if (in_array('staff_member', $user->roles)):

		// break the default message string into variables where possible. This should include the user activation key ('key') as a specific array index
		parse_str($wp_new_user_notification_email['message'], $msg_vars);

		// We must only proceed if we were able to successfully extract the user activation key out of the default message string
		if (isset($msg_vars['key'])):
			$wp_new_user_notification_email['headers'] = array('Content-Type: text/html', 'Reply-To: admin@playerportal.com');
			$username = $user->get('user_login');
			$wp_new_user_notification_email['subject'] = __('Your Player Portal website user account', 'player-portal');
			$pw_reset_url = get_site_url() . '/wp-login.php?action=rp&key=' . $msg_vars['key'] . '&login=' . $username;
			ob_start();
			?>
			<p>A website user account has been created for you at <?php echo get_site_url(); ?></p>
			<p>Your username is: <?php echo $username; ?></p>
			<p>
				To set a password for the account please click the link below and follow the onscreen instructions:<br>
				<?php echo $pw_reset_url; ?>
			</p>
			<p>
				Once you have set a password for your account you can login to the website using your username (or email address) and password at any time using the link below:<br>
				<a href="<?php echo get_site_url(); ?>" target="_blank"><?php echo get_site_url(); ?></a>
			</p>
			<?php
			$wp_new_user_notification_email['message'] = ob_get_clean();
		endif;
	endif;
	return $wp_new_user_notification_email;
}
add_filter('wp_new_user_notification_email', 'new_user_email', 10, 3);


add_action('wp_ajax_register_user_front_end', 'register_user_front_end', 0);
add_action('wp_ajax_nopriv_register_user_front_end', 'register_user_front_end');
function register_user_front_end() {
	$new_user_name = stripcslashes($_POST['new_user_name']);
	$new_user_email = stripcslashes($_POST['new_user_email']);
	$new_user_password = $_POST['new_user_password'];
	$user_nice_name = strtolower($_POST['new_user_email']);
	$user_data = array(
		'user_login' => $new_user_name,
		'user_email' => $new_user_email,
		'user_pass' => $new_user_password,
		'user_nicename' => $user_nice_name,
		'display_name' => $new_user_first_name,
		'role' => 'subscriber'
	);
	if (!is_email($new_user_email)) {
		echo '<p class="py-3 px-4 bg-red-500/10 border border-red-500/20">A valid email address is required</p>';
	} else if (!is_email($new_user_password)) {
		echo '<p class="py-3 px-4 bg-red-500/10 border border-red-500/20">Please enter a password</p>';
	} else {
		$user_id = wp_insert_user($user_data);
		if (!is_wp_error($user_id)) {
			echo '<p class="py-3 px-4 bg-green-500/10 border border-green-500/20">Your account has been created. You can now log in.</p>';
		} else {
			if (isset($user_id->errors['empty_user_login']) || isset($user_id->errors['empty_user_email'])) {
				echo '<p class="py-3 px-4 bg-red-500/10 border border-red-500/20">Username is required</p>';
			} elseif (isset($user_id->errors['existing_user_login'])) {
				echo '<p class="py-3 px-4 bg-red-500/10 border border-red-500/20">Sorry, that username already exixts.</p>';
			} else {
				echo '<p class="py-3 px-4 bg-red-500/10 border border-red-500/20">An error occured during registration.</p>';
			}
		}
	}
	die;
}
function register_form_shortcode($atts){
	if (!get_option('users_can_register')) {
		return '<p>Registration is disabled.</p>';
	}
	$user_login = '';
	$user_email = '';
	$redirect_to = apply_filters('registration_redirect', '');
	ob_start(); ?>
	<div class="register-message" style="display:none"></div>
	<form action="#" method="POST" name="register-form" class="register-form">
		<fieldset>
			<p>
				<label class="block mb-2 font-bold">Username</label>
				<input type="text"  name="new_user_name" id="new-username" class="p-4 border w-full" required>
			</p>
			<p>
				<label class="block mb-2 font-bold">Email Address</label>
				<input type="email"  name="new_user_email" id="new-useremail" class="p-4 border w-full" required>
			</p>
			<p>
				<label class="block mb-2 font-bold">Password</label>
				<input type="password"  name="new_user_password" id="new-userpassword" class="p-4 border w-full" required>
			</p>
			<input type="submit" class="inline-flex items-center bg-blue-500 hover:bg-blue-700 text-white no-underline py-3 px-4 rounded transition-all cursor-pointer" id="register-button" value="Register" >
		</fieldset>
	</form>

	<script type="text/javascript">
		jQuery('#register-button').on('click',function(e){
			e.preventDefault();
			var newUserName = jQuery('#new-username').val();
			var newUserEmail = jQuery('#new-useremail').val();
			var newUserPassword = jQuery('#new-userpassword').val();
			jQuery.ajax({
				type:"POST",
				url:"<?php echo admin_url('admin-ajax.php'); ?>",
				data: {
					action: "register_user_front_end",
					new_user_name : newUserName,
					new_user_email : newUserEmail,
					new_user_password : newUserPassword
				},
				success: function(results){
					jQuery('.register-message').html(results).show();
				},
				error: function(results) {

				}
			});
		});
	</script>
	<?php return ob_get_clean();
}

add_shortcode('register_form', 'register_form_shortcode');