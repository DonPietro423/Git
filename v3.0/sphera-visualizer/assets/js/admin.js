jQuery(function ($) {
  $('.sphera-color-picker').wpColorPicker();

  let mediaFrame;

  $('.sphera-media-button').on('click', function (e) {
    e.preventDefault();

    if (mediaFrame) {
      mediaFrame.open();
      return;
    }

    mediaFrame = wp.media({
      title: spheraAdmin.mediaTitle,
      button: {
        text: spheraAdmin.mediaButton
      },
      library: {
        type: ['model/gltf-binary', 'application/octet-stream', 'model/gltf+json']
      },
      multiple: false
    });

    mediaFrame.on('select', function () {
      const attachment = mediaFrame.state().get('selection').first().toJSON();
      $('#model_url').val(attachment.url).trigger('change');
    });

    mediaFrame.open();
  });
});