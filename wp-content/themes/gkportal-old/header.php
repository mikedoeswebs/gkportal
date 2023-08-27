<!doctype html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo('charset'); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<?php wp_body_open(); ?>
		<div id="page">
			<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e('Skip to content', 'theme_name'); ?></a>
			<main id="main">
