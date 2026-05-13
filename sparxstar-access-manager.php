<?php
/**
 * Plugin Name: SPARXSTAR Boson Scaffold
 * Plugin URI:  https://github.com/Starisian-Technologies/sparxstar-boson-scaffold
 * Description: Named WordPress Multisite MU-plugin scaffold. Rename this plugin when building your own project.
 * Version:     1.0.0
 * Author:      Starisian Technologies
 * Author URI:  https://starisian.tech
 * License:     MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: sparxstar-boson
 * Domain Path: /languages
 * Network:     true
 * Requires at least: 6.8
 * Requires PHP: 8.2
 *
 * @package Starisian\Sparxstar\BosonScaffold
 * @license MIT https://opensource.org/licenses/MIT
 * @copyright Copyright (c) 2026 Starisian Technologies
 *
 * SPARXSTAR™ and Starisian Technologies™ are trademarks of Starisian Technologies.
 * WordPress is a trademark of Automattic Inc. Starisian Technologies is not
 * affiliated with or endorsed by Automattic Inc.
 */

declare(strict_types=1);

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Define plugin constants — rename these when building your own project.
define( 'SPX_BOSON_VERSION', '1.0.0' );
define( 'SPX_BOSON_PLUGIN_FILE', __FILE__ );
define( 'SPX_BOSON_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SPX_BOSON_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SPX_BOSON_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Load Composer autoloader if available.
if ( file_exists( SPX_BOSON_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
    require_once SPX_BOSON_PLUGIN_DIR . 'vendor/autoload.php';
}

// Load the plugin class.
require_once SPX_BOSON_PLUGIN_DIR . 'src/class-plugin.php';

/**
 * Initialize the plugin.
 */
function spx_boson_init(): void {
    $plugin = Starisian\Sparxstar\BosonScaffold\Plugin::get_instance();
    $plugin->init();
}

// Initialize on plugins_loaded hook to ensure all plugins are loaded.
add_action( 'plugins_loaded', 'spx_boson_init', 10 );

/**
 * Activation hook — runs when plugin is activated as a regular plugin.
 *
 * Note: This does NOT run for MU-plugins. For MU-plugin installs, defaults
 * are initialized on first load via load_options() in SecureCustomFieldManager.
 *
 * @param bool $network_wide Whether the plugin is being activated network-wide.
 */
function spx_boson_activate( bool $network_wide = false ): void {
    if ( is_multisite() && $network_wide ) {
        // Network-wide activation: initialize all sites.
        $sites = get_sites( array( 'number' => 1000 ) );
        foreach ( $sites as $site ) {
            Starisian\Sparxstar\BosonScaffold\Plugin::activate_for_site( (int) $site->blog_id );
        }
    } elseif ( is_multisite() ) {
        // Single subsite activation.
        Starisian\Sparxstar\BosonScaffold\Plugin::activate_for_site( get_current_blog_id() );
    } else {
        // Single site activation.
        Starisian\Sparxstar\BosonScaffold\Plugin::activate_for_site( null );
    }
}
register_activation_hook( __FILE__, 'spx_boson_activate' );

/**
 * Deactivation hook.
 */
function spx_boson_deactivate(): void {
    Starisian\Sparxstar\BosonScaffold\Plugin::deactivate();
}
register_deactivation_hook( __FILE__, 'spx_boson_deactivate' );
