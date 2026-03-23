<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class P3DV_Admin {

	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_filter( 'manage_viewer_3d_posts_columns', array( $this, 'set_custom_columns' ) );
		add_action( 'manage_viewer_3d_posts_custom_column', array( $this, 'render_custom_columns' ), 10, 2 );
	}

	public function register_post_type() {
		$labels = array(
			'name'               => __( '3D Viewers', 'plugin-3d-viewer' ),
			'singular_name'      => __( '3D Viewer', 'plugin-3d-viewer' ),
			'add_new'            => __( 'Ajouter', 'plugin-3d-viewer' ),
			'add_new_item'       => __( 'Ajouter un viewer 3D', 'plugin-3d-viewer' ),
			'edit_item'          => __( 'Modifier le viewer 3D', 'plugin-3d-viewer' ),
			'new_item'           => __( 'Nouveau viewer 3D', 'plugin-3d-viewer' ),
			'view_item'          => __( 'Voir le viewer 3D', 'plugin-3d-viewer' ),
			'search_items'       => __( 'Rechercher un viewer 3D', 'plugin-3d-viewer' ),
			'not_found'          => __( 'Aucun viewer trouvé', 'plugin-3d-viewer' ),
			'not_found_in_trash' => __( 'Aucun viewer dans la corbeille', 'plugin-3d-viewer' ),
			'menu_name'          => __( '3D Viewer', 'plugin-3d-viewer' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'menu_icon'          => 'dashicons-format-image',
			'supports'           => array( 'title' ),
			'has_archive'        => false,
			'rewrite'            => false,
			'show_in_rest'       => false,
		);

		register_post_type( 'viewer_3d', $args );
	}

	public function add_admin_menu() {
		add_submenu_page(
			'edit.php?post_type=viewer_3d',
			__( 'Réglages 3D Viewer', 'plugin-3d-viewer' ),
			__( 'Réglages', 'plugin-3d-viewer' ),
			'manage_options',
			'p3dv-settings',
			array( $this, 'render_settings_page' )
		);
	}

	public function register_settings() {
		register_setting(
			'p3dv_settings_group',
			'p3dv_settings',
			array( $this, 'sanitize_settings' )
		);

		add_settings_section(
			'p3dv_main_section',
			__( 'Paramètres généraux', 'plugin-3d-viewer' ),
			'__return_false',
			'p3dv-settings'
		);

		add_settings_field(
			'default_bg_color',
			__( 'Couleur de fond par défaut', 'plugin-3d-viewer' ),
			array( $this, 'render_color_field' ),
			'p3dv-settings',
			'p3dv_main_section',
			array(
				'label_for' => 'default_bg_color',
			)
		);

		add_settings_field(
			'default_height',
			__( 'Hauteur par défaut', 'plugin-3d-viewer' ),
			array( $this, 'render_height_field' ),
			'p3dv-settings',
			'p3dv_main_section',
			array(
				'label_for' => 'default_height',
			)
		);

		add_settings_field(
			'enable_autorotate',
			__( 'Autorotation activée par défaut', 'plugin-3d-viewer' ),
			array( $this, 'render_checkbox_field' ),
			'p3dv-settings',
			'p3dv_main_section',
			array(
				'label_for' => 'enable_autorotate',
			)
		);
	}

	public function sanitize_settings( $input ) {
		$output = array();

		$output['default_bg_color']  = isset( $input['default_bg_color'] ) ? sanitize_hex_color( $input['default_bg_color'] ) : '#f5f5f5';
		$output['default_height']    = isset( $input['default_height'] ) ? sanitize_text_field( $input['default_height'] ) : '500px';
		$output['enable_autorotate'] = ! empty( $input['enable_autorotate'] ) ? 1 : 0;

		return $output;
	}

	public function get_settings() {
		$defaults = array(
			'default_bg_color'  => '#f5f5f5',
			'default_height'    => '500px',
			'enable_autorotate' => 0,
		);

		return wp_parse_args( get_option( 'p3dv_settings', array() ), $defaults );
	}

	public function render_color_field() {
		$options = $this->get_settings();
		?>
		<input
			type="text"
			id="default_bg_color"
			name="p3dv_settings[default_bg_color]"
			value="<?php echo esc_attr( $options['default_bg_color'] ); ?>"
			class="p3dv-color-field"
		/>
		<?php
	}

	public function render_height_field() {
		$options = $this->get_settings();
		?>
		<input
			type="text"
			id="default_height"
			name="p3dv_settings[default_height]"
			value="<?php echo esc_attr( $options['default_height'] ); ?>"
			placeholder="500px"
		/>
		<p class="description">Ex: 400px, 70vh</p>
		<?php
	}

	public function render_checkbox_field() {
		$options = $this->get_settings();
		?>
		<label for="enable_autorotate">
			<input
				type="checkbox"
				id="enable_autorotate"
				name="p3dv_settings[enable_autorotate]"
				value="1"
				<?php checked( 1, $options['enable_autorotate'] ); ?>
			/>
			Activer l'autorotation par défaut
		</label>
		<?php
	}

	public function render_settings_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Réglages du plugin 3D Viewer', 'plugin-3d-viewer' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'p3dv_settings_group' );
				do_settings_sections( 'p3dv-settings' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public function enqueue_admin_assets( $hook ) {
		$screen = get_current_screen();

		if ( ! $screen ) {
			return;
		}

		if ( 'viewer_3d' !== $screen->post_type && 'viewer_3d_page_p3dv-settings' !== $screen->id ) {
			return;
		}

		wp_enqueue_style(
			'p3dv-admin-css',
			P3DV_URL . 'assets/css/admin.css',
			array(),
			P3DV_VERSION
		);

		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		wp_enqueue_script(
			'p3dv-admin-js',
			P3DV_URL . 'assets/js/admin.js',
			array( 'jquery', 'wp-color-picker' ),
			P3DV_VERSION,
			true
		);
	}

	public function set_custom_columns( $columns ) {
		$new_columns = array();
		$new_columns['cb']          = $columns['cb'];
		$new_columns['title']       = __( 'Titre', 'plugin-3d-viewer' );
		$new_columns['model_file']  = __( 'Modèle', 'plugin-3d-viewer' );
		$new_columns['shortcode']   = __( 'Shortcode', 'plugin-3d-viewer' );
		$new_columns['date']        = __( 'Date', 'plugin-3d-viewer' );

		return $new_columns;
	}

	public function render_custom_columns( $column, $post_id ) {
		if ( 'model_file' === $column ) {
			$model_url = get_post_meta( $post_id, '_p3dv_model_url', true );
			echo $model_url ? esc_html( basename( $model_url ) ) : '—';
		}

		if ( 'shortcode' === $column ) {
			echo '<code>[3d_viewer id="' . absint( $post_id ) . '"]</code>';
		}
	}
}