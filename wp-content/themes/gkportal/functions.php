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
	wp_enqueue_script('tailpress', tailpress_asset('js/app.js'), array(), $theme->get('Version'));
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