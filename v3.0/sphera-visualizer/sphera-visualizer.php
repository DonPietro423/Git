<?php
/**
 * Plugin Name: SPHERA 3D Visualizer
 * Plugin URI: https://example.com
 * Description: Visualizer 3D WordPress avec back-office premium, branding SPHERA et rendu front via shortcode.
 * Version: 1.0.0
 * Author: Don Pietro
 * Author URI: https://example.com
 * Text Domain: sphera-visualizer
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('SPHERA_VERSION', '1.0.0');
define('SPHERA_PATH', plugin_dir_path(__FILE__));
define('SPHERA_URL', plugin_dir_url(__FILE__));
define('SPHERA_BASENAME', plugin_basename(__FILE__));

add_filter('upload_mimes', function ($mimes) {
    $mimes['glb']  = 'model/gltf-binary';
    $mimes['gltf'] = 'model/gltf+json';
    return $mimes;
});

add_filter('wp_check_filetype_and_ext', function ($data, $file, $filename, $mimes) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if ($ext === 'glb') {
        return [
            'ext'             => 'glb',
            'type'            => 'model/gltf-binary',
            'proper_filename' => $filename,
        ];
    }

    if ($ext === 'gltf') {
        return [
            'ext'             => 'gltf',
            'type'            => 'model/gltf+json',
            'proper_filename' => $filename,
        ];
    }

    return $data;
}, 10, 4);

require_once SPHERA_PATH . 'includes/class-sphera-helpers.php';
require_once SPHERA_PATH . 'includes/class-sphera-plugin.php';

function sphera_run_plugin(): void {
    $plugin = new Sphera_Plugin();
    $plugin->run();
}

register_activation_hook(__FILE__, ['Sphera_Plugin', 'activate']);
register_deactivation_hook(__FILE__, ['Sphera_Plugin', 'deactivate']);

sphera_run_plugin();