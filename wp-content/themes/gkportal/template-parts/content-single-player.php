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
								<span class="capitalize"><?php echo $position; ?><?php if ($i < count(get_field('player_position'))) {echo ', ';} ?></span>
							<?php endforeach; ?>
						</p>
					<?php endif; ?>

					<?php if (get_field('player_preferred_level')) : ?>
						<p>
							<span class="block mb-1 uppercase text-sm font-bold tracking-wide">Level</span>
							<?php foreach (get_field('player_preferred_level') as $level) : ?>
								<span class="text-white rounded px-3 leading-tight inline-block py-2 bg-primary mr-1"><?php echo $level['label']; ?></span>
							<?php endforeach; ?>
						</p>
					<?php endif; ?>

					<?php if (have_rows('player_social_media')) : ?>
						<p>
							<span class="block mb-1 uppercase text-sm font-bold tracking-wide">Social Media Profiles</span>
							<?php $i=0; while (have_rows('player_social_media')) : the_row(); $i++; ?>
								<?php if (get_sub_field('player_social_media_profile')) : ?>
									<span><?php if ($i > 1) {echo ' | ';} ?><a href="<?php echo get_sub_field('player_social_media_profile'); ?>" target="_blank">Click Here</a></span>
								<?php endif; ?>
							<?php endwhile; ?>
						</p>
					<?php endif; ?>

					<?php if (get_field('player_email_address') || get_field('player_phone_number')) : ?>
						<p>
							<span class="block mb-1 uppercase text-sm font-bold tracking-wide">Contact</span>
							<?php if (get_field('player_email_address')) : ?>
								<span><a href="mailto:<?php echo get_field('player_email_address'); ?>">Email</a></span>
							<?php endif; ?>
							<?php if (get_field('player_phone_number')) : ?>
								<span>| <a href="tel:<?php echo get_field('player_phone_number'); ?>">Phone</a></span>
							<?php endif; ?>
						</p>
					<?php endif; ?>
				</div>
				<div>
					<div class="inline-block">
						<?php if (has_post_thumbnail()) : ?>
							<?php the_post_thumbnail('medium'); ?>
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
					<table>
						<thead>
							<tr>
								<th class="text-left border p-2">Club</th>
								<th class="text-left border p-2">Division</th>
								<th class="text-left border p-2">Year From</th>
								<th class="text-left border p-2">Year To</th>
								<th class="text-left border p-2">Details</th>
							</tr>
						</thead>
						<tbody>
							<?php while (have_rows('player_career')) : the_row(); ?>
								<tr>
									<td class="border p-2">
										<?php if (get_sub_field('club')) : ?>
											<?php echo get_sub_field('club'); ?>
										<?php endif; ?>
									</td>
									<td class="border p-2">
										<?php if (get_sub_field('division')) : ?>
											<?php echo get_sub_field('division'); ?>
										<?php endif; ?>
									</td>
									<td class="border p-2">
										<?php if (get_sub_field('year_from')) : ?>
											<?php echo get_sub_field('year_from'); ?>
										<?php endif; ?>
									</td>
									<td class="border p-2">
										<?php if (get_sub_field('year_to')) : ?>
											<?php echo get_sub_field('year_to'); ?>
										<?php else : ?>
											Present
										<?php endif; ?>
									</td>
									<td class="border p-2">
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

			<?php if (have_rows('player_references')) : ?>
				<h2 class="font-extrabold text-xl lg:text-4xl mb-4 mt-6">References</h2>
				<div class="player-references">
					<table>
						<thead>
							<tr>
								<th class="text-left border p-2">Name</th>
								<th class="text-left border p-2">Email</th>
								<th class="text-left border p-2">Phone Number</th>
								<th class="text-left border p-2">Details</th>
							</tr>
						</thead>
						<tbody>
							<?php while (have_rows('player_references')) : the_row(); ?>
								<tr>
									<td class="border p-2">
										<?php if (get_sub_field('reference_name')) : ?>
											<?php echo get_sub_field('reference_name'); ?>
										<?php endif; ?>
									</td>
									<td class="border p-2">
										<?php if (get_sub_field('reference_email')) : ?>
											<a class="text-primary underline hover:no-underline" href="mailto:<?php echo get_sub_field('reference_email'); ?>">Email</a>
										<?php endif; ?>
									</td>
									<td class="border p-2">
										<?php if (get_sub_field('reference_phone_number')) : ?>
											<a class="text-primary underline hover:no-underline" href="tel:<?php echo get_sub_field('reference_phone_number'); ?>">Phone</a>
										<?php endif; ?>
									</td>
									<td class="border p-2">
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

			<?php if (have_rows('player_media')) : ?>
				<?php while (have_rows('player_media')) : the_row(); ?>
					<?php $videos = get_sub_field('media_videos'); ?>
					<?php if (!empty($videos)) : ?>
						<h2 class="font-extrabold text-xl lg:text-4xl mb-4 mt-6">Videos</h2>
						<?php foreach ($videos as $video) : ?>
							<p><a href="<?php echo $video['video_url']; ?>" target="_blank"><?php echo $video['video_title']; ?></a></p>
						<?php endforeach; ?>
					<?php endif; ?>
					<?php $photos = get_sub_field('media_photos'); ?>
					<?php if (!empty($photos)) : ?>
						<h2 class="font-extrabold text-xl lg:text-4xl mb-4 mt-6">Photos</h2>
						<?php foreach ($photos as $photo) : ?>
							<img src="<?php echo wp_get_attachment_image_url($photo, 'medium'); ?>" alt="<?php the_title(); ?>" />
						<?php endforeach; ?>
					<?php endif; ?>
				<?php endwhile; ?>
			<?php endif; ?>
		</div>
	</div>
</article>
