<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once SPHERA_PATH . 'includes/class-sphera-admin.php';
require_once SPHERA_PATH . 'includes/class-sphera-public.php';
require_once SPHERA_PATH . 'includes/class-sphera-renderer.php';

class Sphera_Plugin {
    public static function activate(): void {
        if (!get_option('sphera_settings')) {
            add_option('sphera_settings', Sphera_Helpers::defaults());
        }
    }

    public static function deactivate(): void {
        // Rien ici pour l’instant.
    }

    public function run(): void {
        $admin  = new Sphera_Admin();
        $public = new Sphera_Public();

        add_action('plugins_loaded', [$this, 'load_textdomain']);

        add_action('admin_menu', [$admin, 'register_menu']);
        add_action('admin_init', [$admin, 'register_settings']);
        add_action('admin_enqueue_scripts', [$admin, 'enqueue_assets']);

        add_action('wp_enqueue_scripts', [$public, 'enqueue_assets']);
        add_shortcode('sphera_viewer', [$public, 'render_shortcode']);

        add_filter('plugin_action_links_' . SPHERA_BASENAME, [$this, 'plugin_action_links']);
    }

    public function load_textdomain(): void {
        load_plugin_textdomain('sphera-visualizer', false, dirname(SPHERA_BASENAME) . '/languages');
    }

    public function plugin_action_links(array $links): array {
        $settings_link = '<a href="' . esc_url(admin_url('admin.php?page=sphera-visualizer')) . '">' . esc_html__('Settings', 'sphera-visualizer') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }
}