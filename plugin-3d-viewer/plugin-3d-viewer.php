<?php
/**
 * Plugin Name: Plugin 3D Viewer
 * Description: Plugin WordPress pour afficher des modèles 3D avec interface d'administration.
 * Version: 1.0.0
 * Author: Don Pietro
 * Text Domain: plugin-3d-viewer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'P3DV_VERSION', '1.0.0' );
define( 'P3DV_PATH', plugin_dir_path( __FILE__ ) );
define( 'P3DV_URL', plugin_dir_url( __FILE__ ) );

require_once P3DV_PATH . 'includes/class-admin.php';
require_once P3DV_PATH . 'includes/class-front.php';
require_once P3DV_PATH . 'includes/class-meta-boxes.php';

final class Plugin_3D_Viewer {

	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );
	}

	public function init_plugin() {
		new P3DV_Admin();
		new P3DV_Front();
		new P3DV_Meta_Boxes();
	}
}

new Plugin_3D_Viewer();