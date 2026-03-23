<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class P3DV_Front {

	public function __construct() {
		add_shortcode( '3d_viewer', array( $this, 'render_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_assets' ) );
	}

	public function enqueue_front_assets() {
		wp_enqueue_style(
			'p3dv-front-css',
			P3DV_URL . 'assets/css/front.css',
			array(),
			P3DV_VERSION
		);

		wp_enqueue_script(
			'p3dv-front-js',
			P3DV_URL . 'assets/js/front.js',
			array(),
			P3DV_VERSION,
			true
		);
	}

	public function render_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'id' => 0,
			),
			$atts,
			'3d_viewer'
		);

		$post_id = absint( $atts['id'] );

		if ( ! $post_id || 'viewer_3d' !== get_post_type( $post_id ) ) {
			return '<p>Viewer 3D introuvable.</p>';
		}

		$model_url  = get_post_meta( $post_id, '_p3dv_model_url', true );
		$poster_url = get_post_meta( $post_id, '_p3dv_poster_url', true );
		$bg_color   = get_post_meta( $post_id, '_p3dv_bg_color', true );
		$height     = get_post_meta( $post_id, '_p3dv_height', true );
		$autoplay   = get_post_meta( $post_id, '_p3dv_autoplay', true );
		$autorotate = get_post_meta( $post_id, '_p3dv_autorotate', true );

		if ( ! $model_url ) {
			return '<p>Aucun modèle 3D défini.</p>';
		}

		ob_start();
		include P3DV_PATH . 'templates/viewer.php';
		return ob_get_clean();
	}
}