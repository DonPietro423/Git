<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class P3DV_Meta_Boxes {

	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ) );
	}

	public function register_meta_boxes() {
		add_meta_box(
			'p3dv_viewer_settings',
			__( 'Paramètres du Viewer 3D', 'plugin-3d-viewer' ),
			array( $this, 'render_viewer_settings_box' ),
			'viewer_3d',
			'normal',
			'high'
		);
	}

	public function render_viewer_settings_box( $post ) {
		wp_nonce_field( 'p3dv_save_meta_boxes', 'p3dv_meta_nonce' );

		$model_url   = get_post_meta( $post->ID, '_p3dv_model_url', true );
		$poster_url  = get_post_meta( $post->ID, '_p3dv_poster_url', true );
		$bg_color    = get_post_meta( $post->ID, '_p3dv_bg_color', true );
		$height      = get_post_meta( $post->ID, '_p3dv_height', true );
		$autoplay    = get_post_meta( $post->ID, '_p3dv_autoplay', true );
		$autorotate  = get_post_meta( $post->ID, '_p3dv_autorotate', true );
		?>
		<div class="p3dv-admin-fields">

			<p>
				<label for="p3dv_model_url"><strong>Fichier modèle 3D (.glb, .gltf)</strong></label><br>
				<input type="text" id="p3dv_model_url" name="p3dv_model_url" value="<?php echo esc_attr( $model_url ); ?>" class="widefat" />
				<button type="button" class="button p3dv-upload-button" data-target="#p3dv_model_url">Choisir un fichier</button>
			</p>

			<p>
				<label for="p3dv_poster_url"><strong>Image poster</strong></label><br>
				<input type="text" id="p3dv_poster_url" name="p3dv_poster_url" value="<?php echo esc_attr( $poster_url ); ?>" class="widefat" />
				<button type="button" class="button p3dv-upload-button" data-target="#p3dv_poster_url">Choisir une image</button>
			</p>

			<p>
				<label for="p3dv_bg_color"><strong>Couleur de fond</strong></label><br>
				<input type="text" id="p3dv_bg_color" name="p3dv_bg_color" value="<?php echo esc_attr( $bg_color ? $bg_color : '#ffffff' ); ?>" class="p3dv-color-field" />
			</p>

			<p>
				<label for="p3dv_height"><strong>Hauteur du viewer</strong></label><br>
				<input type="text" id="p3dv_height" name="p3dv_height" value="<?php echo esc_attr( $height ? $height : '500px' ); ?>" class="regular-text" placeholder="500px" />
			</p>

			<p>
				<label>
					<input type="checkbox" name="p3dv_autoplay" value="1" <?php checked( $autoplay, 1 ); ?> />
					Activer l'autoplay
				</label>
			</p>

			<p>
				<label>
					<input type="checkbox" name="p3dv_autorotate" value="1" <?php checked( $autorotate, 1 ); ?> />
					Activer l'autorotation
				</label>
			</p>

			<hr>

			<p>
				<strong>Shortcode :</strong><br>
				<code>[3d_viewer id="<?php echo absint( $post->ID ); ?>"]</code>
			</p>

		</div>
		<?php
	}

	public function save_meta_boxes( $post_id ) {
		if ( ! isset( $_POST['p3dv_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['p3dv_meta_nonce'] ) ), 'p3dv_save_meta_boxes' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! isset( $_POST['post_type'] ) || 'viewer_3d' !== $_POST['post_type'] ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$model_url  = isset( $_POST['p3dv_model_url'] ) ? esc_url_raw( wp_unslash( $_POST['p3dv_model_url'] ) ) : '';
		$poster_url = isset( $_POST['p3dv_poster_url'] ) ? esc_url_raw( wp_unslash( $_POST['p3dv_poster_url'] ) ) : '';
		$bg_color   = isset( $_POST['p3dv_bg_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['p3dv_bg_color'] ) ) : '';
		$height     = isset( $_POST['p3dv_height'] ) ? sanitize_text_field( wp_unslash( $_POST['p3dv_height'] ) ) : '';
		$autoplay   = isset( $_POST['p3dv_autoplay'] ) ? 1 : 0;
		$autorotate = isset( $_POST['p3dv_autorotate'] ) ? 1 : 0;

		update_post_meta( $post_id, '_p3dv_model_url', $model_url );
		update_post_meta( $post_id, '_p3dv_poster_url', $poster_url );
		update_post_meta( $post_id, '_p3dv_bg_color', $bg_color );
		update_post_meta( $post_id, '_p3dv_height', $height );
		update_post_meta( $post_id, '_p3dv_autoplay', $autoplay );
		update_post_meta( $post_id, '_p3dv_autorotate', $autorotate );
	}
}