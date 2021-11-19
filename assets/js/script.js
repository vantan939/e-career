jQuery(document).ready(function($) {
	function change_data_pagination() {
		$('.list-job .pagination a').each(function() {
			var href = $(this).attr('href');
			var num_page = href.replace(/[^0-9]/g,'');
			$(this).attr('data-num', num_page).attr('href', '');
		});
	}
	change_data_pagination();

	function pagination() {
		$('.list-job .pagination a, .career-filter button').click(function() {
			var page = $(this).attr('data-num'),
				id_location = $('.choose-location').val(),
				id_level = $('.choose-level').val()

			$.ajax({
				url: e_ajaxurl,
				type: 'POST',
				data: {
					'action': 'e_load_job_item',
					'page' : page,
					'id_location': id_location,
					'id_level': id_level
				},
				dataType: 'JSON',
				success: function(data) {
					$('.list-job .col-md-12').html(data.html);
					$('.career-result-text b').text(data.num_record);
					change_data_pagination();
					pagination();
					open_apply();
				}
			});

			return false;
		});
	}
	pagination();

	function open_apply() {
		$('.job-item-apply .btn-primary:not(.disabled)').click(function() {
			var popup = $('.job-popup');
			popup.find('.job-desc-text').html($(this).next('.data-apply').find('.job-desc').html());
			popup.find('.job-desc-salary').html($(this).next('.data-apply').find('.job-salary').html());
			popup.find('.job-name').html($(this).next('.data-apply').find('.job-name').html());
			popup.find('.your-position').val($(this).next('.data-apply').find('.job-name').html());
			popup.find('form input:not(.your-position)').val('');
			popup.find('form .alert').remove();

			$('body').addClass('has-modal');
			$('.job-popup').fadeIn();			
		});
	}
	open_apply();

	function close_apply() {
		$('.job-popup .popup-content .close-icon').click(function() {
			$('body').removeClass('has-modal');
			$('.job-popup').hide();
		});
	}
	close_apply();

	function submit_apply() {
		$('.apply-form form').submit(function() {
			var formData = $(this).serialize();
			var _self = $(this);
			_self.find('.alert').remove();

			$.ajax({
				url: e_ajaxurl,
				type: 'POST',
				data: formData + '&action=e_apply_form',
				dataType: 'text',
				success: function(data) {
					_self.find('input:not(.your-position)').val('');
					
					if(data == 1) {
						_self.append('<div class="alert alert-success" role="alert">Email sent successfully!</div>');
					} else {
						_self.append('<div class="alert alert-danger" role="alert">Error. Please try again!</div>');
					}
				}
			});

			return false;
		});
	}
	submit_apply();

});