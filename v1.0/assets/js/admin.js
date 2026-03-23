jQuery(document).ready(function ($) {
	$('.p3dv-color-field').wpColorPicker();

	$(document).on('click', '.p3dv-upload-button', function (e) {
		e.preventDefault();

		const target = $(this).data('target');
		const frame = wp.media({
			title: 'Choisir un fichier',
			button: {
				text: 'Utiliser ce fichier'
			},
			multiple: false
		});

		frame.on('select', function () {
			const attachment = frame.state().get('selection').first().toJSON();
			$(target).val(attachment.url);
		});

		frame.open();
	});
});