<?php

if (!defined('ABSPATH')) {
    exit;
}

class Sphera_Admin {
    public function register_menu(): void {
        add_menu_page(
            __('SPHERA', 'sphera-visualizer'),
            __('SPHERA', 'sphera-visualizer'),
            'manage_options',
            'sphera-visualizer',
            [$this, 'render_admin_page'],
            'dashicons-format-image',
            56
        );
    }

    public function register_settings(): void {
        register_setting('sphera_settings_group', 'sphera_settings', [$this, 'sanitize_settings']);
    }

    public function sanitize_settings(array $input): array {
        $defaults = Sphera_Helpers::defaults();

        return [
            'model_url'             => esc_url_raw($input['model_url'] ?? ''),
            'background'            => Sphera_Helpers::sanitize_hex($input['background'] ?? $defaults['background'], $defaults['background']),
            'accent'                => Sphera_Helpers::sanitize_hex($input['accent'] ?? $defaults['accent'], $defaults['accent']),
            'accent_dark'           => Sphera_Helpers::sanitize_hex($input['accent_dark'] ?? $defaults['accent_dark'], $defaults['accent_dark']),
            'accent_light'          => Sphera_Helpers::sanitize_hex($input['accent_light'] ?? $defaults['accent_light'], $defaults['accent_light']),
            'iridescent_purple'     => Sphera_Helpers::sanitize_hex($input['iridescent_purple'] ?? $defaults['iridescent_purple'], $defaults['iridescent_purple']),
            'iridescent_pink'       => Sphera_Helpers::sanitize_hex($input['iridescent_pink'] ?? $defaults['iridescent_pink'], $defaults['iridescent_pink']),
            'iridescent_cyan'       => Sphera_Helpers::sanitize_hex($input['iridescent_cyan'] ?? $defaults['iridescent_cyan'], $defaults['iridescent_cyan']),
            'neutral_900'           => Sphera_Helpers::sanitize_hex($input['neutral_900'] ?? $defaults['neutral_900'], $defaults['neutral_900']),
            'neutral_700'           => Sphera_Helpers::sanitize_hex($input['neutral_700'] ?? $defaults['neutral_700'], $defaults['neutral_700']),
            'neutral_500'           => Sphera_Helpers::sanitize_hex($input['neutral_500'] ?? $defaults['neutral_500'], $defaults['neutral_500']),
            'neutral_300'           => Sphera_Helpers::sanitize_hex($input['neutral_300'] ?? $defaults['neutral_300'], $defaults['neutral_300']),
            'neutral_100'           => Sphera_Helpers::sanitize_hex($input['neutral_100'] ?? $defaults['neutral_100'], $defaults['neutral_100']),
            'auto_rotate'           => !empty($input['auto_rotate']) ? 1 : 0,
            'show_grid'             => !empty($input['show_grid']) ? 1 : 0,
            'exposure'              => max(0.1, min(3, floatval($input['exposure'] ?? $defaults['exposure']))),
            'environment_strength'  => max(0, min(5, floatval($input['environment_strength'] ?? $defaults['environment_strength']))),
            'height'                => max(320, min(1200, intval($input['height'] ?? $defaults['height']))),
            'border_radius'         => max(0, min(64, intval($input['border_radius'] ?? $defaults['border_radius']))),
            'viewer_title'          => sanitize_text_field($input['viewer_title'] ?? $defaults['viewer_title']),
            'viewer_subtitle'       => sanitize_text_field($input['viewer_subtitle'] ?? $defaults['viewer_subtitle']),
            'brand_mode'            => Sphera_Helpers::allowed_brand_mode($input['brand_mode'] ?? $defaults['brand_mode']),
            'intro_text'            => sanitize_text_field($input['intro_text'] ?? $defaults['intro_text']),
            'button_label'          => sanitize_text_field($input['button_label'] ?? $defaults['button_label']),
        ];
    }

    public function enqueue_assets(string $hook): void {
        if ($hook !== 'toplevel_page_sphera-visualizer') {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_style('sphera-admin', SPHERA_URL . 'assets/css/admin.css', [], SPHERA_VERSION);
        wp_enqueue_script('sphera-admin', SPHERA_URL . 'assets/js/admin.js', ['jquery', 'wp-color-picker'], SPHERA_VERSION, true);

        wp_localize_script('sphera-admin', 'spheraAdmin', [
            'mediaTitle'  => __('Choose a 3D model', 'sphera-visualizer'),
            'mediaButton' => __('Use this model', 'sphera-visualizer'),
        ]);
    }

    public function render_admin_page(): void {
        if (!current_user_can('manage_options')) {
            return;
        }

        $settings  = Sphera_Helpers::settings();
        $assets    = Sphera_Helpers::brand_assets();
        $symbol    = $assets['symbol'];
        $wordmark  = $assets['wordmark'];
        $logotype  = $assets['logotype'];
        ?>
        <div class="wrap sphera-admin-wrap">
            <div class="sphera-admin-header">
                <div class="sphera-admin-branding">
                    <img src="<?php echo esc_url($logotype); ?>" alt="SPHERA" class="sphera-admin-logo-main">
                    <div>
                        <h1>SPHERA 3D Visualizer</h1>
                        <p><?php esc_html_e('Configure the 3D viewer and the SPHERA visual identity.', 'sphera-visualizer'); ?></p>
                    </div>
                </div>
                <div class="sphera-badge">Premium Viewer</div>
            </div>

            <div class="sphera-admin-grid">
                <div class="sphera-card">
                    <form method="post" action="options.php">
                        <?php settings_fields('sphera_settings_group'); ?>

                        <div class="sphera-section-title">
                            <h2>Viewer content</h2>
                            <p>Textes, branding et modèle 3D.</p>
                        </div>

                        <div class="sphera-field-row">
                            <div class="sphera-field">
                                <label for="viewer_subtitle">Sous-titre</label>
                                <input type="text" id="viewer_subtitle" name="sphera_settings[viewer_subtitle]" value="<?php echo esc_attr($settings['viewer_subtitle']); ?>">
                            </div>
                            <div class="sphera-field">
                                <label for="viewer_title">Titre</label>
                                <input type="text" id="viewer_title" name="sphera_settings[viewer_title]" value="<?php echo esc_attr($settings['viewer_title']); ?>">
                            </div>
                        </div>

                        <div class="sphera-field">
                            <label for="intro_text">Texte d’intro</label>
                            <input type="text" id="intro_text" name="sphera_settings[intro_text]" value="<?php echo esc_attr($settings['intro_text']); ?>">
                        </div>

                        <div class="sphera-field-row">
                            <div class="sphera-field">
                                <label for="button_label">Libellé bouton</label>
                                <input type="text" id="button_label" name="sphera_settings[button_label]" value="<?php echo esc_attr($settings['button_label']); ?>">
                            </div>
                            <div class="sphera-field">
                                <label for="brand_mode">Variante de branding</label>
                                <select id="brand_mode" name="sphera_settings[brand_mode]">
                                    <option value="symbol" <?php selected($settings['brand_mode'], 'symbol'); ?>>Symbole seul</option>
                                    <option value="wordmark" <?php selected($settings['brand_mode'], 'wordmark'); ?>>Typo seule</option>
                                    <option value="logotype" <?php selected($settings['brand_mode'], 'logotype'); ?>>Logo complet</option>
                                </select>
                            </div>
                        </div>

                        <div class="sphera-field">
                            <label for="model_url">URL du modèle GLB / GLTF</label>
                            <div class="sphera-media-row">
                                <input type="url" id="model_url" name="sphera_settings[model_url]" value="<?php echo esc_attr($settings['model_url']); ?>" placeholder="https://.../model.glb">
                                <button type="button" class="button button-secondary sphera-media-button">Choisir un fichier</button>
                            </div>
                            <p class="description">Utilise un fichier `.glb` ou `.gltf` uploadé dans la médiathèque.</p>
                        </div>

                        <div class="sphera-section-title">
                            <h2>Viewer settings</h2>
                            <p>Comportement, dimensions et rendu.</p>
                        </div>

                        <div class="sphera-field-row">
                            <div class="sphera-field">
                                <label for="height">Hauteur du viewer</label>
                                <input type="number" id="height" name="sphera_settings[height]" value="<?php echo esc_attr($settings['height']); ?>" min="320" max="1200" step="10">
                            </div>
                            <div class="sphera-field">
                                <label for="border_radius">Rayon des angles</label>
                                <input type="number" id="border_radius" name="sphera_settings[border_radius]" value="<?php echo esc_attr($settings['border_radius']); ?>" min="0" max="64" step="1">
                            </div>
                        </div>

                        <div class="sphera-field-row">
                            <div class="sphera-field">
                                <label for="exposure">Exposure</label>
                                <input type="number" id="exposure" name="sphera_settings[exposure]" value="<?php echo esc_attr($settings['exposure']); ?>" min="0.1" max="3" step="0.1">
                            </div>
                            <div class="sphera-field">
                                <label for="environment_strength">Intensité environnement</label>
                                <input type="number" id="environment_strength" name="sphera_settings[environment_strength]" value="<?php echo esc_attr($settings['environment_strength']); ?>" min="0" max="5" step="0.1">
                            </div>
                        </div>

                        <div class="sphera-switches">
                            <label><input type="checkbox" name="sphera_settings[auto_rotate]" value="1" <?php checked($settings['auto_rotate'], 1); ?>> Rotation auto</label>
                            <label><input type="checkbox" name="sphera_settings[show_grid]" value="1" <?php checked($settings['show_grid'], 1); ?>> Afficher grille</label>
                        </div>

                        <div class="sphera-section-title">
                            <h2>Palette SPHERA</h2>
                            <p>Couleurs cœur, iridescence et neutres UI.</p>
                        </div>

                        <?php
                        $colors = [
                            'background'         => 'Fond principal',
                            'accent'             => 'Bleu roi',
                            'accent_dark'        => 'Bleu profond',
                            'accent_light'       => 'Bleu clair',
                            'iridescent_purple'  => 'Violet iridescent',
                            'iridescent_pink'    => 'Rose froid',
                            'iridescent_cyan'    => 'Cyan doux',
                            'neutral_900'        => 'Neutral 900',
                            'neutral_700'        => 'Neutral 700',
                            'neutral_500'        => 'Neutral 500',
                            'neutral_300'        => 'Neutral 300',
                            'neutral_100'        => 'Neutral 100',
                        ];

                        foreach ($colors as $key => $label) : ?>
                            <div class="sphera-field sphera-color-field">
                                <label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label>
                                <input
                                    class="sphera-color-picker"
                                    type="text"
                                    id="<?php echo esc_attr($key); ?>"
                                    name="sphera_settings[<?php echo esc_attr($key); ?>]"
                                    value="<?php echo esc_attr($settings[$key]); ?>"
                                >
                            </div>
                        <?php endforeach; ?>

                        <?php submit_button(__('Save settings', 'sphera-visualizer')); ?>
                    </form>
                </div>

                <div class="sphera-card sphera-preview-card">
                    <h2>Shortcode</h2>
                    <code>[sphera_viewer]</code>
                    <p>Version simple.</p>

                    <code>[sphera_viewer brand="symbol" height="640" auto_rotate="true"]</code>
                    <p>Version personnalisée.</p>

                    <h2>Brand assets</h2>
                    <div class="sphera-brand-variants">
                        <figure>
                            <img src="<?php echo esc_url($symbol); ?>" alt="SPHERA symbole">
                            <figcaption>Symbole</figcaption>
                        </figure>
                        <figure>
                            <img src="<?php echo esc_url($wordmark); ?>" alt="SPHERA wordmark">
                            <figcaption>Typo</figcaption>
                        </figure>
                        <figure>
                            <img src="<?php echo esc_url($logotype); ?>" alt="SPHERA logotype">
                            <figcaption>Logotype</figcaption>
                        </figure>
                    </div>

                    <h2>Visual preview</h2>
                    <div class="sphera-preview-orb"></div>
                    <div class="sphera-preview-gradient"></div>
                </div>
            </div>
        </div>
        <?php
    }
}