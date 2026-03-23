<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div
	class="p3dv-viewer-wrap"
	style="background-color: <?php echo esc_attr( $bg_color ? $bg_color : '#f5f5f5' ); ?>; height: <?php echo esc_attr( $height ? $height : '500px' ); ?>;"
	data-model-url="<?php echo esc_url( $model_url ); ?>"
	data-poster-url="<?php echo esc_url( $poster_url ); ?>"
	data-autoplay="<?php echo esc_attr( $autoplay ? 'true' : 'false' ); ?>"
	data-autorotate="<?php echo esc_attr( $autorotate ? 'true' : 'false' ); ?>"
>
	<div class="p3dv-viewer-placeholder">
		Viewer 3D prêt. Branche ton moteur ici.
	</div>
</div>