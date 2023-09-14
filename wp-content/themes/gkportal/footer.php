				</main>
				<?php do_action('tailpress_content_end'); ?>
			</div>
			<?php do_action('tailpress_content_after'); ?>
			<footer id="colophon" class="site-footer bg-gray-50 py-12" role="contentinfo">
				<?php do_action('tailpress_footer'); ?>
				<div class="container mx-auto text-center text-gray-500">
					<span class="block">&copy; <?php echo date_i18n('Y');?> <?php echo get_bloginfo('name'); ?></span>
					<span class="block text-xs opacity-50 mt-1">Player profiles are user-submitted, and, although regularly checked, <?php echo get_bloginfo('name'); ?> cannot be held responsible for their content or accuracy.</span>
				</div>
			</footer>
		</div>
		<?php wp_footer(); ?>
	</body>
</html>
