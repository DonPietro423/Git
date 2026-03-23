=== SPHERA 3D Visualizer ===
Contributors: donpietro
Tags: 3d, viewer, glb, gltf, visualizer, model viewer, shortcode, design
Requires at least: 6.0
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

SPHERA 3D Visualizer is a premium-styled WordPress plugin that lets you display interactive 3D models with a polished branded interface.

== Description ==

SPHERA 3D Visualizer is a WordPress plugin designed to showcase 3D models in an elegant and immersive way.

Built with a refined visual identity inspired by the SPHERA brand system, the plugin combines a clean back-office experience with a premium front-end viewer. It is ideal for portfolios, product showcases, concept presentations, creative studios, and interactive digital experiences.

Main features:

- Interactive 3D viewer for GLB / GLTF models
- Dedicated WordPress admin page
- Customizable viewer branding
- Support for multiple logo variants
- Adjustable viewer height and border radius
- Auto-rotation toggle
- Optional grid display
- Exposure and environment intensity settings
- Customizable visual palette
- Front-end rendering via shortcode
- Fallback 3D object when no model is loaded
- SPHERA design system integration

The plugin is designed to offer a modern and polished user experience while staying simple to configure.

== Installation ==

1. Upload the `sphera-visualizer` folder to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen.
2. Activate the plugin through the `Plugins` screen in WordPress.
3. Go to the `SPHERA` admin menu.
4. Configure your viewer settings.
5. Add your 3D model URL or choose a model from the media library.
6. Place the shortcode `[sphera_viewer]` inside a page, post, or template.

== Frequently Asked Questions ==

= What file formats are supported? =

The plugin is designed to work with `.glb` and `.gltf` 3D models.

= Can I display the viewer anywhere? =

Yes. You can use the shortcode in posts, pages, or template areas that support shortcodes.

= Can I customize the branding? =

Yes. The plugin supports three branding modes:

- Symbol only
- Wordmark only
- Full logotype

= Can I change the colors? =

Yes. The plugin includes a visual settings panel that lets you adjust the SPHERA palette directly from the WordPress admin.

= What happens if no 3D model is selected? =

If no model is loaded, the plugin displays a fallback 3D object so the viewer remains functional and visually consistent.

= Does the plugin use external libraries? =

Yes. The current version loads Three.js via CDN for the front-end 3D rendering.

== Shortcode ==

Basic usage:

[sphera_viewer]

Advanced usage:

[sphera_viewer brand="symbol" height="700" auto_rotate="true" show_grid="false"]

Available shortcode attributes:

- `brand`  
  Accepted values: `symbol`, `wordmark`, `logotype`

- `height`  
  Viewer height in pixels

- `auto_rotate`  
  Accepted values: `true`, `false`

- `show_grid`  
  Accepted values: `true`, `false`

== Admin Settings ==

The plugin includes a dedicated settings page where you can configure:

- Viewer subtitle
- Viewer title
- Introductory text
- Reset button label
- Branding mode
- 3D model URL
- Viewer height
- Border radius
- Exposure
- Environment intensity
- Auto-rotation
- Grid visibility
- Main colors
- Iridescent accent colors
- Neutral UI colors

== Assets ==

To use the integrated SPHERA branding system, place the following files in:

`assets/img/`

Expected file names:

- `sphera-symbol.png`
- `sphera-wordmark.png`
- `sphera-logotype.png`

These assets are used in both the admin interface and the front-end viewer.

== Developer Notes ==

Plugin structure:

- `sphera-visualizer.php`  
  Main plugin bootstrap file

- `includes/class-sphera-plugin.php`  
  Core plugin initialization

- `includes/class-sphera-admin.php`  
  Admin settings page and configuration logic

- `includes/class-sphera-public.php`  
  Front-end shortcode rendering

- `includes/class-sphera-renderer.php`  
  Renderer helper structure

- `includes/class-sphera-helpers.php`  
  Shared defaults and utility methods

- `assets/css/admin.css`  
  Admin styling

- `assets/css/public.css`  
  Front-end styling

- `assets/js/admin.js`  
  Admin interactions and media handling

- `assets/js/public.js`  
  Front-end viewer logic

== Limitations ==

- The current version uses Three.js from a CDN
- No Gutenberg block is included yet
- No Elementor widget is included yet
- Local packaging of Three.js is not included in this version

== Roadmap ==

Future improvements may include:

- Local bundling of Three.js assets
- Gutenberg block integration
- Elementor widget support
- Preset manager
- Multiple viewer instances with advanced controls
- Improved model environment presets
- Better loading states and transitions

== Screenshots ==

1. SPHERA admin panel
2. Viewer settings panel
3. Branded front-end 3D viewer
4. Branding variants preview

== Changelog ==

= 1.0.0 =
* Initial release
* Added branded SPHERA admin panel
* Added shortcode-based 3D viewer
* Added support for GLB / GLTF model loading
* Added visual customization options
* Added branding mode selection
* Added fallback 3D object
* Added admin media selector for model URL

== Upgrade Notice ==

= 1.0.0 =
Initial public version of SPHERA 3D Visualizer.