<?php

if (!defined('ABSPATH')) {
    exit;
}

class Sphera_Helpers {
    public static function defaults(): array {
        return [
            'model_url'             => '',
            'background'            => '#dbe9f4',
            'accent'                => '#1F3CFF',
            'accent_dark'           => '#0A1A5C',
            'accent_light'          => '#4F7CFF',
            'iridescent_purple'     => '#7F5DAF',
            'iridescent_pink'       => '#E26BAE',
            'iridescent_cyan'       => '#49B1E3',
            'neutral_900'           => '#050B17',
            'neutral_700'           => '#1E2C44',
            'neutral_500'           => '#71809B',
            'neutral_300'           => '#C7D1E6',
            'neutral_100'           => '#F3F6FB',
            'auto_rotate'           => 1,
            'show_grid'             => 0,
            'exposure'              => 1.1,
            'environment_strength'  => 1.0,
            'height'                => 560,
            'border_radius'         => 24,
            'viewer_title'          => 'Visualizer 3D',
            'viewer_subtitle'       => 'SPHERA',
            'brand_mode'            => 'logotype',
            'intro_text'            => 'Explore your 3D asset in a premium interactive viewer.',
            'button_label'          => 'Reset view',
        ];
    }

    public static function settings(): array {
        $saved = get_option('sphera_settings', []);
        return wp_parse_args(is_array($saved) ? $saved : [], self::defaults());
    }

    public static function brand_assets(): array {
        return [
            'symbol'   => SPHERA_URL . 'assets/img/sphera-symbol.png',
            'wordmark' => SPHERA_URL . 'assets/img/sphera-wordmark.png',
            'logotype' => SPHERA_URL . 'assets/img/sphera-logotype.png',
        ];
    }

    public static function sanitize_hex(string $value, string $fallback): string {
        $sanitized = sanitize_hex_color($value);
        return $sanitized ? $sanitized : $fallback;
    }

    public static function allowed_brand_mode(string $value): string {
        $allowed = ['symbol', 'wordmark', 'logotype'];
        return in_array($value, $allowed, true) ? $value : 'logotype';
    }
}