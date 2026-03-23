<?php

if (!defined('ABSPATH')) {
    exit;
}

class Sphera_Public {
    public function enqueue_assets(): void {
        wp_register_style('sphera-public', SPHERA_URL . 'assets/css/public.css', [], SPHERA_VERSION);
        wp_register_script('sphera-public', SPHERA_URL . 'assets/js/public.js', [], SPHERA_VERSION, true);
    }

    public function render_shortcode(array $atts = []): string {
        $settings = Sphera_Helpers::settings();

        $atts = shortcode_atts([
            'brand'       => $settings['brand_mode'],
            'height'      => $settings['height'],
            'auto_rotate' => $settings['auto_rotate'] ? 'true' : 'false',
            'show_grid'   => $settings['show_grid'] ? 'true' : 'false',
        ], $atts, 'sphera_viewer');

        $brand_mode = Sphera_Helpers::allowed_brand_mode((string) $atts['brand']);
        $height     = max(320, min(1200, intval($atts['height'])));
        $autoRotate = filter_var($atts['auto_rotate'], FILTER_VALIDATE_BOOLEAN);
        $showGrid   = filter_var($atts['show_grid'], FILTER_VALIDATE_BOOLEAN);

        $id = 'sphera-viewer-' . wp_generate_uuid4();
        $brand_assets = Sphera_Helpers::brand_assets();
        $brand_asset  = $brand_assets[$brand_mode] ?? $brand_assets['logotype'];

        wp_enqueue_style('sphera-public');
        wp_enqueue_script('sphera-public');

        wp_add_inline_script('sphera-public', 'window.SPHERA_VIEWERS = window.SPHERA_VIEWERS || []; window.SPHERA_VIEWERS.push(' . wp_json_encode([
            'id'                  => $id,
            'modelUrl'            => $settings['model_url'],
            'autoRotate'          => $autoRotate,
            'showGrid'            => $showGrid,
            'exposure'            => floatval($settings['exposure']),
            'environmentStrength' => floatval($settings['environment_strength']),
            'accent'              => $settings['accent'],
            'accentLight'         => $settings['accent_light'],
        ]) . ');', 'before');

        $style = sprintf(
            '--sphera-bg:%s;--sphera-accent:%s;--sphera-accent-dark:%s;--sphera-accent-light:%s;--sphera-neutral-900:%s;--sphera-neutral-700:%s;--sphera-radius:%dpx;min-height:%dpx;',
            esc_attr($settings['background']),
            esc_attr($settings['accent']),
            esc_attr($settings['accent_dark']),
            esc_attr($settings['accent_light']),
            esc_attr($settings['neutral_900']),
            esc_attr($settings['neutral_700']),
            intval($settings['border_radius']),
            intval($height)
        );

        ob_start();
        ?>
        <section class="sphera-viewer-shell" style="<?php echo esc_attr($style); ?>">
            <div class="sphera-viewer-topbar">
                <div class="sphera-viewer-branding">
                    <img src="<?php echo esc_url($brand_asset); ?>" alt="SPHERA" class="sphera-viewer-brand-asset sphera-viewer-brand-<?php echo esc_attr($brand_mode); ?>">
                    <div class="sphera-viewer-copy">
                        <span class="sphera-kicker"><?php echo esc_html($settings['viewer_subtitle']); ?></span>
                        <h2><?php echo esc_html($settings['viewer_title']); ?></h2>
                        <p><?php echo esc_html($settings['intro_text']); ?></p>
                    </div>
                </div>

                <button type="button" class="sphera-reset-btn" data-target="<?php echo esc_attr($id); ?>">
                    <?php echo esc_html($settings['button_label']); ?>
                </button>
            </div>

            <div id="<?php echo esc_attr($id); ?>" class="sphera-viewer-canvas" aria-label="SPHERA 3D Viewer"></div>
        </section>
        <?php

        return ob_get_clean();
    }
}