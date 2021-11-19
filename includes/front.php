<?php
	$args_career = array(
	    'post_type'  => 'career',
	    'post_status' => 'any',
	    'suppress_filters' => false,
	    'posts_per_page' => 5,
	    'orderby' => 'title',
      	'order' => 'ASC'
	);
	$q_career = new WP_Query( $args_career );

	$location_terms = get_terms(array(
	    'taxonomy' => 'location-cat',
	    'hide_empty' => false
	));

	$level_terms = get_terms(array(
	    'taxonomy' => 'level-cat',
	    'hide_empty' => false
	));
?>

<section class="e-career">
	<div class="career-filter">
		<form method="post">
			<div class="col-md-4">
				<select class="form-control choose-location">
					<option value="">Vị trí Job</option>
					<?php foreach($location_terms as $term): ?>
						<option value="<?php echo $term->term_id; ?>"> <?php echo $term->name; ?> </option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col-md-4">
				<select class="form-control choose-level">
					<option value="">Level</option>
					<?php foreach($level_terms as $term): ?>
						<option value="<?php echo $term->term_id; ?>"> <?php echo $term->name; ?> </option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col-md-4">
				<button class="btn btn-primary">Tìm kiếm</button>
			</div>
		</form>
	</div>

	<div class="career-result-text text-center">
		<div class="col-md-12">
			<p>Có <b><?php echo $q_career->found_posts; ?></b> kết quả được tìm thấy</p>
		</div>
	</div>

	<?php if($q_career->have_posts()) : ?>
		<div class="list-job">
			<div class="col-md-12">
				<?php while( $q_career->have_posts() ) : $q_career->the_post(); ?>
					<?php
						$location = wp_get_post_terms(get_the_ID(), array('location-cat'));
						$location = implode(' / ', array_column($location, 'name'));
						$level = wp_get_post_terms(get_the_ID(), array('level-cat'));
						$level = implode(' / ', array_column($level, 'name'));
						$status = get_post_status() == 'publish' ? 'Open' : 'Closed';
					?>
					<div class="job-item">
						<div class="job-item-info">
							<p class="name-job"><?php the_title(); ?></p>
							<p>Vị trí job: <?php echo $location; ?> </p>
							<p>Level: <?php echo $level; ?> </p>
							<p>Tình trạng job: <?php echo $status; ?> </p>
						</div>
						<div class="job-item-apply">
							<?php if(get_post_status() == 'publish'): ?>
								<button class="btn btn-primary">Apply</button>
							<?php else: ?>
								<button class="btn btn-primary disabled">Apply</button>						
							<?php endif; ?>
							<div class="data-apply" style="display: none;">
								<div class="job-name"><?php the_title(); ?></div>
								<div class="job-desc"><?php the_content(); ?></div>
								<div class="job-salary"><?php echo get_post_meta(get_the_ID(), 'salary', true); ?></div>
							</div>
						</div>
					</div>
				<?php endwhile; ?>

				<?php (new CAREER())->e_pagination($q_career); ?>

			</div>
		</div>
	<?php endif; wp_reset_postdata(); ?>
</section>

<div class="job-popup" style="display: none;">
	<div class="popup-content">
		<div class="close-icon">x</div>

		<div class="job-desc">
			<h3 class="job-name"></h3>
			<h4>Description</h4>
			<div class="job-desc-text"></div>
			<h4>Salaray</h4>
			<div class="job-desc-salary"></div>			
		</div>

		<div class="apply-form">
			<h4>Apply Now</h4>
			<p>Please fill out the form</p>
			<form method="post" autocomplete="off">
				<div class="form-group">
					<label>Your name*</label>
					<input type="text" name="your_name" class="form-control" required />
				</div>
				<div class="form-group">
					<label>Your email*</label>
					<input type="email" name="your_email" class="form-control" required />
				</div>
				<div class="form-group">
					<label>Phone Number*</label>
					<input type="text" name="your_phone" class="form-control" required />
				</div>
				<div class="form-group">
					<input type="hidden" name="your_position" class="form-control your-position" value="Wordpress" />
				</div>
				<div class="form-group">
					<button class="btn btn-primary">Apply</button>
				</div>
			</form>
		</div>
	</div>
</div>