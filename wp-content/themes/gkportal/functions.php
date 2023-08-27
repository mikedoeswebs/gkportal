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


function ps_acf_save_post($post_id) {
	// Don't do this on the ACF post type
	if (get_post_type($post_id) == 'acf') {
		return;
	}

	// Get the Fields
	$fields = get_field_objects($post_id);

	// Prevent Infinite Looping...
	remove_action('acf/save_post', 'my_acf_save_post');

	// Grab Post Data from the Form
	$post = array(
		'ID'           => $post_id,
		'post_type'    => 'post',
		'post_title'   => $fields['player_name']['value'],
		'post_content' => $fields['player_bio']['value'],
		'post_status'  => 'publish'
	);

	// Update the Post
	wp_update_post($post);

	// Continue save action
	add_action('acf/save_post', 'my_save_post');

	// Set the Return URL in Case of 'new' Post
	$_POST['return'] = add_query_arg('updated', 'true', get_permalink($post_id));
}
add_action('acf/save_post', 'ps_acf_save_post', 10, 1);
