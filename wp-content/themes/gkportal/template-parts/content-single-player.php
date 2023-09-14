<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-content">
		<div>
			<div class="flex justify-between mb-4 lg:mb-6">
				<div>
					<div class="flex items-center mb-4">
						<?php if (get_field('player_name')) : ?>
							<h1 class="entry-title text-2xl lg:text-5xl font-extrabold"><?php echo get_field('player_name'); ?></h1>
						<?php else : ?>
							<h1 class="entry-title text-2xl lg:text-5xl font-extrabold mb-0">
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
							</h1>
						<?php endif; ?>
						<?php if (get_field('player_current_status')) : ?>
							<?php if (get_field('player_current_status') == 'looking') : ?>
								<span class="text-white text-xs rounded px-3 inline-block py-2 bg-green-500 ml-4 leading-none">Looking For Club</span>
							<?php else : ?>
								<span class="text-white text-xs rounded px-3 inline-block py-2 bg-red-500 ml-4 leading-none">Not Looking For Club</span>
							<?php endif; ?>
						<?php endif; ?>
					</div>
					<?php if (!empty(get_field('player_position'))) : ?>
						<p>
							<span class="block mb-1 uppercase text-sm font-bold tracking-wide">Position<?php echo count(get_field('player_position')) > 1 ? 's' : ''; ?></span>
							<?php $i = 0; foreach (get_field('player_position') as $position) : $i++; ?>
								<span class="capitalize"><?php echo $position['label']; ?><?php if ($i < count(get_field('player_position'))) {echo ', ';} ?></span>
							<?php endforeach; ?>
						</p>
					<?php endif; ?>

					<?php if (get_field('player_preferred_level')) : ?>
						<p>
							<span class="block mb-1 uppercase text-sm font-bold tracking-wide">Level</span>
							<?php foreach (get_field('player_preferred_level') as $level) : ?>
								<span class="text-white rounded px-3 leading-tight inline-block py-2 bg-blue-500 mr-1"><?php echo $level['label']; ?></span>
							<?php endforeach; ?>
						</p>
					<?php endif; ?>

					<?php if (have_rows('player_social_media')) : ?>
						<p>
							<span class="block mb-1 uppercase text-sm font-bold tracking-wide">Social Media Profiles</span>
							<?php $i=0; while (have_rows('player_social_media')) : the_row(); $i++; ?>
								<?php if (get_sub_field('player_social_media_profile')) : ?>
									<span>
										<?php if ($i > 1) : ?>
											<span class="mx-1">|</span>
										<?php endif; ?>
										<?php
											$social_link = get_sub_field('player_social_media_profile');
											$parsed_url = parse_url($social_link);
											if (!empty($parsed_url['host'])) {
												$stripped_url = str_replace('www.', '', $parsed_url['host']);
												$stripped_url = str_replace('.com', '', $stripped_url);
												$url = $stripped_url;
											} else {
												$url = $parsed_url;
											}
										?>
										<a href="<?php echo get_sub_field('player_social_media_profile'); ?>" target="_blank" class="capitalize"><?php echo $url; ?></a>
									</span>
								<?php endif; ?>
							<?php endwhile; ?>
						</p>
					<?php endif; ?>
				</div>
				<div>
					<div class="inline-block">
						<?php if (have_rows('player_media')) : ?>
							<?php while (have_rows('player_media')) : the_row(); ?>
								<?php $photos = get_sub_field('media_photos'); ?>
								<?php if (!empty($photos)) : ?>
									<?php foreach ($photos as $photo) : ?>
										<img src="<?php echo wp_get_attachment_image_url($photo, 'medium'); ?>" alt="<?php the_title(); ?>" />
									<?php break; endforeach; ?>
								<?php else : ?>
									<canvas height="300" width="250" class="bg-gray-200"></canvas>
								<?php endif; ?>
							<?php endwhile; ?>
						<?php else : ?>
							<canvas height="300" width="250" class="bg-gray-200"></canvas>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<hr>

			<?php if (get_field('player_bio')) : ?>
				<h2 class="font-extrabold text-xl lg:text-4xl mb-4 mt-6">Bio</h2>
				<?php echo get_field('player_bio'); ?>
			<?php endif; ?>

			<?php if (have_rows('player_career')) : ?>
				<h2 class="font-extrabold text-xl lg:text-4xl mb-4 mt-6">Career</h2>
				<div class="player-career">
					<table class="w-full">
						<thead>
							<tr>
								<th class="text-left border py-2 px-4">Club</th>
								<th class="text-left border py-2 px-4">Division</th>
								<th class="text-left border py-2 px-4">Year From</th>
								<th class="text-left border py-2 px-4">Year To</th>
								<th class="text-left border py-2 px-4">Details</th>
							</tr>
						</thead>
						<tbody>
							<?php while (have_rows('player_career')) : the_row(); ?>
								<tr>
									<td class="border py-2 px-4">
										<?php if (get_sub_field('club')) : ?>
											<?php echo get_sub_field('club'); ?>
										<?php endif; ?>
									</td>
									<td class="border py-2 px-4">
										<?php if (get_sub_field('division')) : ?>
											<?php echo get_sub_field('division'); ?>
										<?php endif; ?>
									</td>
									<td class="border py-2 px-4">
										<?php if (get_sub_field('year_from')) : ?>
											<?php echo get_sub_field('year_from'); ?>
										<?php endif; ?>
									</td>
									<td class="border py-2 px-4">
										<?php if (get_sub_field('year_to')) : ?>
											<?php echo get_sub_field('year_to'); ?>
										<?php else : ?>
											Present
										<?php endif; ?>
									</td>
									<td class="border py-2 px-4">
										<?php if (get_sub_field('details')) : ?>
											<?php echo get_sub_field('details'); ?>
										<?php endif; ?>
									</td>
								</tr>
							<?php endwhile; ?>
						</tbody>
					</table>
				</div>
			<?php endif; ?>

			<?php if (have_rows('player_media')) : ?>
				<?php while (have_rows('player_media')) : the_row(); ?>
					<?php $videos = get_sub_field('media_videos'); ?>
					<?php if (!empty($videos)) : ?>
						<h2 class="font-extrabold text-xl lg:text-4xl mb-4 mt-6">Videos</h2>
						<?php foreach ($videos as $video) : ?>
							<p><a href="<?php echo $video['video_url']; ?>" target="_blank" class="inline-flex items-center bg-blue-500 hover:bg-blue-700 !text-white !no-underline py-3 px-4 rounded transition"><?php echo $video['video_title']; ?><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" height="16" width="16" class="inline ml-2"><path d="M10 6V8H5V19H16V14H18V20C18 20.5523 17.5523 21 17 21H4C3.44772 21 3 20.5523 3 20V7C3 6.44772 3.44772 6 4 6H10ZM21 3V11H19L18.9999 6.413L11.2071 14.2071L9.79289 12.7929L17.5849 5H13V3H21Z"></path></svg></a></p>
						<?php endforeach; ?>
					<?php endif; ?>
					<?php $photos = get_sub_field('media_photos'); ?>
					<?php if (!empty($photos)) : ?>
						<h2 class="font-extrabold text-xl lg:text-4xl mb-4 mt-6">Photos</h2>
						<div class="flex flex-wrap -mx-2">
							<?php foreach ($photos as $photo) : ?>
								<div class="mx-2">
									<div class="w-full flex justify-center p-4 bg-gray-100 h-full">
										<img src="<?php echo wp_get_attachment_image_url($photo, 'thumbnail'); ?>" alt="<?php the_title(); ?>" />
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				<?php endwhile; ?>
			<?php endif; ?>

			<?php if (current_user_can('administrator')) : ?>
				<span class="mt-10 inline-block bg-red-200 px-4 py-2 text-xs uppercase font-bold">Visible only to admin users</span>
				<div class="bg-red-100 p-6 relative">
					<?php if (get_field('player_email_address') || get_field('player_phone_number')) : ?>
						<h2 class="font-extrabold text-xl lg:text-4xl mb-2">Contact Details</h2>
						<p class="!mb-8">
							<?php if (get_field('player_email_address')) : ?>
								<span><a href="mailto:<?php echo get_field('player_email_address'); ?>">Email</a></span>
							<?php endif; ?>
							<?php if (get_field('player_phone_number')) : ?>
								<span>| <a href="tel:<?php echo get_field('player_phone_number'); ?>">Phone</a></span>
							<?php endif; ?>
						</p>
					<?php endif; ?>
					<?php if (have_rows('player_references')) : ?>
						<h2 class="font-extrabold text-xl lg:text-4xl mb-2">References</h2>
						<div class="player-references">
							<table class="bg-white w-full">
								<thead>
									<tr>
										<th class="text-left border py-2 px-4">Name</th>
										<th class="text-left border py-2 px-4">Email</th>
										<th class="text-left border py-2 px-4">Phone Number</th>
										<th class="text-left border py-2 px-4">Details</th>
									</tr>
								</thead>
								<tbody>
									<?php while (have_rows('player_references')) : the_row(); ?>
										<tr>
											<td class="border py-2 px-4">
												<?php if (get_sub_field('reference_name')) : ?>
													<?php echo get_sub_field('reference_name'); ?>
												<?php endif; ?>
											</td>
											<td class="border py-2 px-4">
												<?php if (get_sub_field('reference_email')) : ?>
													<a class="text-primary underline hover:no-underline" href="mailto:<?php echo get_sub_field('reference_email'); ?>">Email</a>
												<?php endif; ?>
											</td>
											<td class="border py-2 px-4">
												<?php if (get_sub_field('reference_phone_number')) : ?>
													<a class="text-primary underline hover:no-underline" href="tel:<?php echo get_sub_field('reference_phone_number'); ?>">Phone</a>
												<?php endif; ?>
											</td>
											<td class="border py-2 px-4">
												<?php if (get_sub_field('reference_details')) : ?>
													<?php echo get_sub_field('reference_details'); ?>
												<?php endif; ?>
											</td>
										</tr>
									<?php endwhile; ?>
								</tbody>
							</table>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

		</div>
	</div>
</article>
