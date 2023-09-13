<?php get_header(); ?>
	<div class="container mx-auto my-8">
		<header class="entry-header mb-8 border-b pb-8">
			<h1 class="entry-title text-2xl lg:text-5xl font-extrabold mb-0 flex items-center justify-center">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="42" height="42"><path d="M18.031 16.6168L22.3137 20.8995L20.8995 22.3137L16.6168 18.031C15.0769 19.263 13.124 20 11 20C6.032 20 2 15.968 2 11C2 6.032 6.032 2 11 2C15.968 2 20 6.032 20 11C20 13.124 19.263 15.0769 18.031 16.6168ZM16.0247 15.8748C17.2475 14.6146 18 12.8956 18 11C18 7.1325 14.8675 4 11 4C7.1325 4 4 7.1325 4 11C4 14.8675 7.1325 18 11 18C12.8956 18 14.6146 17.2475 15.8748 16.0247L16.0247 15.8748ZM12.1779 7.17624C11.4834 7.48982 11 8.18846 11 9C11 10.1046 11.8954 11 13 11C13.8115 11 14.5102 10.5166 14.8238 9.82212C14.9383 10.1945 15 10.59 15 11C15 13.2091 13.2091 15 11 15C8.79086 15 7 13.2091 7 11C7 8.79086 8.79086 7 11 7C11.41 7 11.8055 7.06167 12.1779 7.17624Z"></path></svg>
				<span class="ml-4">Find a Player</span>
			</h1>
		</header>
		<div class="entry-content flex flex-col lg:flex-row lg:items-start relative">
			<?php if (have_posts()) : ?>
				<div class="lg:w-1/4 sticky top-0 lg:top-4 z-10 bg-gray-100 lg:bg-transparent -mx-4 lg:mx-0 mb-4 lg:mb-0">
					<a class="lg:hidden font-bold !m-0 px-4 h-12 !text-dark !no-underline flex items-center justify-between " href="#" aria-label="Toggle filters" id="player-filter-toggle">
						<span>Filter Players</span>
						<svg viewBox="0 0 20 20" class="inline-block w-6 h-6" version="1.1"
							 xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
							<g stroke="none" stroke-width="1" fill="currentColor" fill-rule="evenodd">
								<g id="icon-shape">
									<path d="M0,3 L20,3 L20,5 L0,5 L0,3 Z M0,9 L20,9 L20,11 L0,11 L0,9 Z M0,15 L20,15 L20,17 L0,17 L0,15 Z" id="Combined-Shape"></path>
								</g>
							</g>
						</svg>
					</a>
					<div id="player-filters" class="hidden lg:block px-4 lg:px-0 overflow-auto h-[calc(100vh-3rem)]">
						<p class="text-xl font-bold hidden lg:block">Filter Players</p>
						<div>
							<?php echo facetwp_display('selections'); ?>
							<a href="javascript:;" class="text-xs flex justify-between" onclick="FWP.reset()">Clear All Filters</a>
						</div>
						<div>
							<strong class="mb-1 block">Status</strong>
							<div class="text-sm">
								<?php echo facetwp_display('facet', 'status'); ?>
							</div>
						</div>
						<div>
							<strong class="mb-1 block">Position</strong>
							<div class="text-sm">
								<?php echo facetwp_display('facet', 'position'); ?>
							</div>
						</div>
						<div>
							<strong class="mb-1 block">Looking for</strong>
							<div class="text-sm">
								<?php echo facetwp_display('facet', 'step'); ?>
							</div>
						</div>
					</div>
				</div>
				<div class="lg:w-3/4">
					<div class="w-full flex flex-wrap -mx-4">
						<?php while (have_posts()) : the_post(); ?>
							<div class="w-full md:w-1/2 lg:w-1/3">
								<div class="mx-4 mb-8 relative">
									<div class="mb-2 relative">
										<?php if (get_field('player_current_status')) : ?>
											<?php if (get_field('player_current_status') == 'looking') : ?>
												<span class="absolute top-2 right-2 text-white text-xs rounded px-3 inline-block py-2 bg-green-500 leading-none">Looking For Club</span>
											<?php else : ?>
												<span class="absolute top-2 right-2 text-white text-xs rounded px-3 inline-block py-2 bg-red-500 leading-none">Not Looking For Club</span>
											<?php endif; ?>
										<?php endif; ?>
										<?php if (has_post_thumbnail()) : ?>
											<?php the_post_thumbnail('medium', false, ['class' => 'w-full']); ?>
										<?php else : ?>
											<canvas height="300" width="250" class="bg-gray-200 w-full"></canvas>
										<?php endif; ?>
									</div>
									<h2 class="font-extrabold mb-3">
										<?php if (get_field('player_first_name') || get_field('player_last_name')) : ?>
											<?php if (get_field('player_first_name')) : ?>
												<?php echo get_field('player_first_name'); ?>
											<?php endif; ?>
											<?php if (get_field('player_last_name')) : ?>
												<?php echo get_field('player_last_name'); ?>
											<?php endif; ?>
										<?php else : ?>
											<?php the_title(); ?>
										<?php endif; ?>
									</h2>
									<?php if (!empty(get_field('player_position'))) : ?>
										<p>
											<?php $i = 0; foreach (get_field('player_position') as $position) : $i++; ?>
												<span class="capitalize"><?php echo $position; ?><?php if ($i < count(get_field('player_position'))) {echo ', ';} ?></span>
											<?php endforeach; ?>
										</p>
									<?php endif; ?>
									<a href="<?php the_permalink(); ?>" class="absolute inset-0"></a>
								</div>
							</div>
						<?php endwhile; ?>
					</div>
					<div class="flex items-center justify-between">
						<div>
							Sort By: <?php echo facetwp_display('facet', 'sort_'); ?>
						</div>
						<div class="flex">
							<?php echo facetwp_display('facet', 'count'); ?>
							<?php echo facetwp_display('facet', 'pager_'); ?>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
<?php get_footer();