<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$errors = [];
$success = [];
$location = [];
$level = [];

if(isset($_POST['e-submit'])) {
	if(empty($_FILES['file_upload']['name'])) {
		$errors[] = 'Please choose file!';
	}else {
		$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
		$spreadsheet = $reader->load($_FILES['file_upload']['tmp_name']);
		$worksheet = $spreadsheet->getActiveSheet()->toArray();
		$i = 0; foreach($worksheet as $sheet) {
			if($i > 0) {
				$location[] = $sheet[1];
				$level[] = $sheet[2];
			}
		$i++; }

		$location = array_unique($location);
		$level = array_unique($level);
		
		// Insert categories to DB
		foreach($location as $lc) {
			wp_insert_term(
		        $lc,
		        'location-cat'
		    );
		}

		foreach($level as $lv) {
			wp_insert_term(
		        $lv,
		        'level-cat'
		    );
		}

		// Insert to custom post
		$j = 0; foreach($worksheet as $sheet) {
			if($j > 0) {
				$post_id = wp_insert_post(array (
				   'post_type' => 'career',
				   'post_title' => $sheet[0],
				   'post_content' => $sheet[4],
				   'post_status' => ($sheet[3] == 'Open') ? 'publish' : 'draft'
				));

				update_post_meta( $post_id, 'salary', $sheet[5] );

				$location_terms = get_terms(array(
				    'taxonomy' => 'location-cat',
				    'hide_empty' => false
				));
				foreach($location_terms as $term) {
					if($term->name == $sheet[1]) {
						wp_set_post_terms($post_id, $term->term_id, 'location-cat');
					}
				}

				$level_terms = get_terms(array(
				    'taxonomy' => 'level-cat',
				    'hide_empty' => false
				));
				foreach($level_terms as $term) {
					if($term->name == $sheet[2]) {
						wp_set_post_terms($post_id, $term->term_id, 'level-cat');
					}
				}

			}
		$j++; }

		$success[] = 'Import successfully!';
	}

}


?>

<div class="e-import-data">
	<h1>Import Excel</h1>
	<p><b>Please choose excel to upload</b></p>

	<form action="" method="post" enctype="multipart/form-data">
		<input type="file" name="file_upload" />

		<?php if(!empty($errors)): ?>
			<div class="notice notice-error">
				<?php foreach($errors as $error): ?>
					<p><?php echo $error; ?></p>
				<?php endforeach; ?>
		    </div>
		<?php endif; ?>

		<button class="button button-primary" name="e-submit">Upload</button>

		<?php if(!empty($success)): ?>
			<div class="notice notice-success">
				<?php foreach($success as $s): ?>
					<p><?php echo $s; ?></p>
				<?php endforeach; ?>
		    </div>
		<?php endif; ?>
	</form>
</div>


<style type="text/css">
	.e-import-data button.button {
		margin-top: 30px;
		display: block;
		clear: both;
	}
	.e-import-data .notice {
		margin-left: 0;
		max-width: 500px;
		margin-top: 10px;
	}
</style>