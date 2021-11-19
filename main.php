<?php

class CAREER {
	public function __construct() {
		add_action('wp_enqueue_scripts', array( $this, 'e_add_scripts'), 40);
		add_action('init', array($this, 'e_create_post_type'));
		add_action('init', array($this, 'e_add_taxonomy_location'));
		add_action('init', array($this, 'e_add_taxonomy_level'));
		add_action('admin_menu', array($this, 'e_create_import_menu'));
		add_shortcode('e_career', array($this, 'e_shortcode_views'));
		add_action('wp_head', array( $this, 'e_load_ajax_url'));
		add_action('wp_ajax_e_load_job_item', array( $this, 'e_load_job_item'));
		add_action('wp_ajax_nopriv_e_load_job_item', array( $this, 'e_load_job_item'));
		add_action('wp_ajax_e_apply_form', array( $this, 'e_apply_form'));
		add_action('wp_ajax_nopriv_e_apply_form', array( $this, 'e_apply_form'));
	}

	/**
	 * ** Add script and style
	 **/
	public function e_add_scripts() {
		wp_enqueue_style( 'e_style',  CAREER_PLUGIN_ASSETS_URL  . '/css/style.css', array(), CAREER_VERSION);		
		wp_enqueue_script( 'e_script',  CAREER_PLUGIN_ASSETS_URL  . '/js/script.js', array('jquery'), CAREER_VERSION, true);
	}

	/**
	 * ** Create custom post type
	 **/
	public function e_create_post_type() {
		$labels = array(
			'name'                => __( 'Careers', 'e-career' ),
			'singular_name'       => __( 'Career', 'e-career' ),
			'menu_name'           => __( 'Careers', 'e-career' ),
			'name_admin_bar'      => __( 'Career',  'e-career' ),
			'add_new'             => __( 'Add New', 'e-career' ),
		);
	 
		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array('slug' => 'career'),
			'capability_type'    => 'post',
            'has_archive'        => true,
			'hierarchical'       => true,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields'),
		);
		register_post_type( 'career', $args );
	}


	function e_add_taxonomy_location() {
		$labels = array(
			'name' => 'Location',
			'singular' => 'Location',
			'menu_name' => 'Location'
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => true,
		);
		register_taxonomy('location-cat', 'career', $args);
	}

	function e_add_taxonomy_level() {
		$labels = array(
			'name' => 'Level',
			'singular' => 'Level',
			'menu_name' => 'Level'
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => true,
		);
		register_taxonomy('level-cat', 'career', $args);
	}


	/**
	 * ** Create Import Menu
	 **/
	public function e_create_import_menu() {
		add_submenu_page(
	        'edit.php?post_type=career',
	        __( 'Import Excel', 'e-career' ),
	        __( 'Import Excel', 'e-career' ),
	        'manage_options',
	        'import-data',
	        array($this, 'e_import_data')
	    );
	}

	public function e_import_data() {
		require_once( CAREER_PLUGIN_DIR . '/includes/import-data.php' );
	}

	/**
	 * ** Create Shortcode show FE
	 **/
	public function e_shortcode_views() {
		require_once( CAREER_PLUGIN_DIR . '/includes/front.php' );
	}

	/**
	 * ** Pagination
	 **/
	public function e_load_ajax_url() {
		echo '<script type="text/javascript">
			var e_ajaxurl = "' . admin_url('admin-ajax.php') . '";
		</script>';
	}

	function e_pagination($wp_query, $num = 1) {
	    $big = 999999999;
	    $pages = paginate_links(array(
	        'base' => str_replace($big, '%#%', get_pagenum_link($big)),
	        'format' => '?page=%#%',
	        'current' => max(1, $num),
	        'total' => $wp_query->max_num_pages,
	        'prev_next' => false,
	        'type' => 'array',
	        'prev_next' => TRUE,
	        'prev_text' => '&larr; Trang trước',
	        'next_text' => 'Trang sau &rarr;',
	            ));

	    if (is_array($pages)) {
	        $current_page = $num;
	        echo '<div id="wrap-pagination">';
	        echo '<ul class="pagination">';
	        foreach ($pages as $page) {
	        	if (strpos($page, 'current') !== false) {
	        		echo "<li class='active'>$page</li>";
	        	}else {
	        		echo "<li>$page</li>";
	        	}
	        }
	        echo '</ul>';
	        echo '</div>';
	    }
	}

	public function e_load_job_item() {
		if(!isset($_POST)) return;

		$args_career = array(
		    'post_type'  => 'career',
		    'post_status' => 'any',
		    'posts_per_page' => 5,
		    'orderby' => 'title',
	      	'order' => 'ASC',
	      	'paged' => $_POST['page']
		);
		if(!empty($_POST['id_location'])) {
			$args_career['tax_query'][] = array(
	            'taxonomy' => 'location-cat',
	            'field' => 'term_id',
	            'terms' => $_POST['id_location']
			);
		}
		if(!empty($_POST['id_level'])) {
			$args_career['tax_query'][] = array(
	            'taxonomy' => 'level-cat',
	            'field' => 'term_id',
	            'terms' => $_POST['id_level']
			);
		}
		$q_career = new WP_Query( $args_career );

		ob_start();
		?>

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

			<?php $this->e_pagination($q_career, $_POST['page']); ?>


		<?php
		$html = ob_get_clean();
		echo json_encode(
			array(
				'html' 	=> $html,
				'num_record' => $q_career->found_posts
			)
		);

		die();
	}

	/**
	 * ** Apply Form
	 **/
	public function e_apply_form() {
		if(!isset($_POST)) return;
		$name = $_POST['your_name'];
		$email = $_POST['your_email'];
		$phone = $_POST['your_phone'];
		$position = $_POST['your_position'];

		$to       = 'vantan939@gmail.com';
		$subject  = '[Form Apply] New Submission';
		$message = "
			<h4>Candidate Information</h4>
		  	<p>Name: {$name}</p>
		  	<p>Email: {$name}</p>
		  	<p>Phone Number: {$phone}</p>
		  	<p>Position: {$position}</p>
		";
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

		$result = wp_mail($to, $subject, $message, $headers);
		if(!$result) {   
	     	echo 0;   
		} else {
		    echo 1;
		}
		
		die();
	}


}

new CAREER();