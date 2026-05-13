<?php
/**
 * PHPUnit bootstrap file.
 *
 * @package   Starisian\Sparxstar\BosonScaffold\Tests
 * @license   MIT https://opensource.org/licenses/MIT
 * @copyright Copyright (c) 2026 Starisian Technologies
 */

declare(strict_types=1);

// Load Composer autoloader.
if ( file_exists( dirname( __DIR__ ) . '/vendor/autoload.php' ) ) {
    require_once dirname( __DIR__ ) . '/vendor/autoload.php';
}

// Define test sentinel constant.
define( 'SPX_BOSON_TESTS', true );

// Mock WordPress functions for unit testing when WordPress is not loaded.
if ( ! function_exists( 'add_action' ) ) {
    function add_action( string $hook, callable|array|string $callback, int $priority = 10, int $accepted_args = 1 ): bool { // phpcs:ignore
        return true;
    }
}

if ( ! function_exists( 'add_filter' ) ) {
    function add_filter( string $hook, callable|array|string $callback, int $priority = 10, int $accepted_args = 1 ): bool { // phpcs:ignore
        return true;
    }
}

if ( ! function_exists( 'apply_filters' ) ) {
    function apply_filters( string $hook, mixed $value, mixed ...$args ): mixed { // phpcs:ignore
        return $value;
    }
}

if ( ! function_exists( 'do_action' ) ) {
    function do_action( string $hook, mixed ...$args ): void { // phpcs:ignore
    }
}

if ( ! function_exists( 'get_option' ) ) {
    function get_option( string $option, mixed $default = false ): mixed { // phpcs:ignore
        return $default;
    }
}

if ( ! function_exists( 'update_option' ) ) {
    function update_option( string $option, mixed $value, bool|string $autoload = true ): bool { // phpcs:ignore
        return true;
    }
}

if ( ! function_exists( 'add_option' ) ) {
    function add_option( string $option, mixed $value = '', string $deprecated = '', bool $autoload = true ): bool { // phpcs:ignore
        return true;
    }
}

if ( ! function_exists( 'is_multisite' ) ) {
    function is_multisite(): bool {
        return false;
    }
}

if ( ! function_exists( 'get_current_blog_id' ) ) {
    function get_current_blog_id(): int {
        return 1;
    }
}

if ( ! function_exists( 'switch_to_blog' ) ) {
    function switch_to_blog( int $new_blog_id ): bool { // phpcs:ignore
        return true;
    }
}

if ( ! function_exists( 'restore_current_blog' ) ) {
    function restore_current_blog(): bool {
        return true;
    }
}

if ( ! function_exists( 'is_admin' ) ) {
    function is_admin(): bool {
        return false;
    }
}

if ( ! function_exists( 'is_network_admin' ) ) {
    function is_network_admin(): bool {
        return false;
    }
}

if ( ! function_exists( '__' ) ) {
    function __( string $text, string $domain = 'default' ): string { // phpcs:ignore
        return $text;
    }
}

if ( ! function_exists( 'esc_html__' ) ) {
    function esc_html__( string $text, string $domain = 'default' ): string { // phpcs:ignore
        return htmlspecialchars( $text );
    }
}

if ( ! function_exists( 'esc_html' ) ) {
    function esc_html( string $text ): string {
        return htmlspecialchars( $text );
    }
}

if ( ! function_exists( 'esc_attr' ) ) {
    function esc_attr( string $text ): string {
        return htmlspecialchars( $text, ENT_QUOTES );
    }
}

if ( ! function_exists( 'esc_textarea' ) ) {
    function esc_textarea( string $text ): string {
        return htmlspecialchars( $text );
    }
}

if ( ! function_exists( 'wp_json_encode' ) ) {
    function wp_json_encode( mixed $data, int $options = 0, int $depth = 512 ): string|false { // phpcs:ignore
        return json_encode( $data, $options, $depth );
    }
}

if ( ! function_exists( 'register_activation_hook' ) ) {
    function register_activation_hook( string $file, callable $callback ): void { // phpcs:ignore
    }
}

if ( ! function_exists( 'register_deactivation_hook' ) ) {
    function register_deactivation_hook( string $file, callable $callback ): void { // phpcs:ignore
    }
}

if ( ! function_exists( 'plugin_dir_path' ) ) {
    function plugin_dir_path( string $file ): string {
        return dirname( $file ) . '/';
    }
}

if ( ! function_exists( 'plugin_dir_url' ) ) {
    function plugin_dir_url( string $file ): string {
        return 'https://example.com/wp-content/plugins/' . basename( dirname( $file ) ) . '/';
    }
}

if ( ! function_exists( 'plugin_basename' ) ) {
    function plugin_basename( string $file ): string {
        return basename( dirname( $file ) ) . '/' . basename( $file );
    }
}

if ( ! function_exists( 'current_user_can' ) ) {
    function current_user_can( string $capability, mixed ...$args ): bool { // phpcs:ignore
        return true;
    }
}

if ( ! function_exists( 'add_options_page' ) ) {
    function add_options_page( string $page_title, string $menu_title, string $capability, string $menu_slug, callable $callback ): string|false { // phpcs:ignore
        return false;
    }
}

if ( ! function_exists( 'register_setting' ) ) {
    function register_setting( string $option_group, string $option_name, array $args = array() ): void { // phpcs:ignore
    }
}

if ( ! function_exists( 'add_settings_section' ) ) {
    function add_settings_section( string $id, string $title, callable $callback, string $page ): void { // phpcs:ignore
    }
}

if ( ! function_exists( 'add_settings_field' ) ) {
    function add_settings_field( string $id, string $title, callable $callback, string $page, string $section = 'default', array $args = array() ): void { // phpcs:ignore
    }
}

if ( ! function_exists( 'settings_fields' ) ) {
    function settings_fields( string $option_group ): void { // phpcs:ignore
    }
}

if ( ! function_exists( 'do_settings_sections' ) ) {
    function do_settings_sections( string $page ): void { // phpcs:ignore
    }
}

if ( ! function_exists( 'submit_button' ) ) {
    function submit_button( string $text = '', string $type = 'primary', string $name = 'submit', bool $wrap = true, mixed $other_attributes = null ): void { // phpcs:ignore
    }
}

if ( ! function_exists( 'get_admin_page_title' ) ) {
    function get_admin_page_title(): string {
        return 'Admin Page';
    }
}

if ( ! function_exists( 'checked' ) ) {
    function checked( mixed $checked, mixed $current = true, bool $echo = true ): string { // phpcs:ignore
        $result = ( $checked == $current ) ? ' checked="checked"' : ''; // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
        if ( $echo ) {
            echo $result; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
        return $result;
    }
}

if ( ! function_exists( 'add_settings_error' ) ) {
    function add_settings_error( string $setting, string $code, string $message, string $type = 'error' ): void { // phpcs:ignore
    }
}

// Define plugin constants for testing.
if ( ! defined( 'SPX_BOSON_PLUGIN_DIR' ) ) {
    define( 'SPX_BOSON_PLUGIN_DIR', dirname( __DIR__ ) . '/' );
}

// Load plugin classes.
require_once SPX_BOSON_PLUGIN_DIR . 'src/class-secure-custom-field-manager.php';
require_once SPX_BOSON_PLUGIN_DIR . 'src/class-rules-engine.php';
require_once SPX_BOSON_PLUGIN_DIR . 'src/class-admin-manager.php';
require_once SPX_BOSON_PLUGIN_DIR . 'src/class-plugin.php';
