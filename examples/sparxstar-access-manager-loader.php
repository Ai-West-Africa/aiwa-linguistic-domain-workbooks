<?php
/**
 * SPARXSTAR Boson Scaffold — MU-Plugin Loader
 *
 * Place this file at: wp-content/mu-plugins/sparxstar-boson-loader.php
 *
 * It loads the Boson Scaffold from its subdirectory so WordPress MU-plugin
 * autoloading works correctly. Rename this file when building your own project.
 *
 * @package   Starisian\Sparxstar\BosonScaffold
 * @license   MIT https://opensource.org/licenses/MIT
 * @copyright Copyright (c) 2026 Starisian Technologies
 */

declare(strict_types=1);

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

$spx_boson_plugin_file = WPMU_PLUGIN_DIR . '/sparxstar-boson-scaffold/sparxstar-access-manager.php';

if ( file_exists( $spx_boson_plugin_file ) ) {
    require_once $spx_boson_plugin_file;
} else {
    error_log( 'SPARXSTAR Boson Scaffold: plugin file not found at: ' . $spx_boson_plugin_file ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
}

unset( $spx_boson_plugin_file );
