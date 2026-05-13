<?php
/**
 * Example: Custom Integration with SPARXSTAR Boson Scaffold
 *
 * Demonstrates how to integrate custom functionality using the scaffold's
 * hooks and filters. Place this in your theme's functions.php or as a
 * separate plugin file.
 *
 * RENAME all spx_boson_ prefixes to your own product prefix before
 * shipping your own project.
 *
 * @package Examples
 * @license MIT https://opensource.org/licenses/MIT
 * @copyright Copyright (c) 2026 Starisian Technologies
 */

declare(strict_types=1);

/**
 * Example 1: Add custom SCF options programmatically.
 *
 * @param array<string, mixed> $options Current SCF options.
 * @return array<string, mixed>
 */
function spx_boson_example_custom_scf_options( array $options ): array {
    $options['api_access'] = array(
        'enabled'   => true,
        'api_key'   => get_option( 'my_custom_api_key' ),
        'endpoints' => array( '/api/v1/protected', '/api/v1/admin' ),
    );

    $options['user_restrictions'] = array(
        'allowed_roles' => array( 'administrator', 'editor' ),
        'denied_roles'  => array( 'subscriber' ),
    );

    return $options;
}
add_filter( 'spx_boson_scf_options', 'spx_boson_example_custom_scf_options' );

/**
 * Example 2: Add custom runtime rules.
 *
 * @param array<int, array<string, mixed>> $rules Current rules.
 * @return array<int, array<string, mixed>>
 */
function spx_boson_example_custom_rules( array $rules ): array {
    $rules[] = array(
        'type'                 => 'custom_api_access',
        'enabled'              => true,
        'endpoint_prefix'      => '/api/v1/',
        'required_capability'  => 'edit_posts',
    );

    $rules[] = array(
        'type'         => 'custom_content_restriction',
        'enabled'      => true,
        'post_types'   => array( 'page', 'post' ),
        'minimum_role' => 'editor',
    );

    return $rules;
}
add_filter( 'spx_boson_rules', 'spx_boson_example_custom_rules' );

/**
 * Example 3: Handle custom rule types.
 *
 * @param bool                 $handled Whether the rule has been handled.
 * @param array<string, mixed> $rule    The rule configuration.
 * @return bool
 */
function spx_boson_example_handle_custom_rule( bool $handled, array $rule ): bool {
    if ( 'custom_api_access' === $rule['type'] && $rule['enabled'] ) {
        add_filter(
            'rest_pre_dispatch',
            static function ( mixed $result, \WP_REST_Server $server, \WP_REST_Request $request ) use ( $rule ): mixed { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassBeforeLastUsed
                $route = $request->get_route();
                if ( str_starts_with( $route, (string) $rule['endpoint_prefix'] ) ) {
                    if ( ! current_user_can( (string) $rule['required_capability'] ) ) {
                        return new \WP_Error(
                            'rest_forbidden',
                            __( 'You do not have permission to access this endpoint.', 'sparxstar-boson' ),
                            array( 'status' => 403 )
                        );
                    }
                }
                return $result;
            },
            10,
            3
        );
        return true;
    }

    if ( 'custom_content_restriction' === $rule['type'] && $rule['enabled'] ) {
        add_action(
            'template_redirect',
            static function () use ( $rule ): void {
                /** @var array<int, string> $post_types */
                $post_types = (array) $rule['post_types'];
                if ( is_singular( $post_types ) ) {
                    $user          = wp_get_current_user();
                    $allowed_roles = array( (string) $rule['minimum_role'], 'administrator' );
                    if ( ! array_intersect( $allowed_roles, (array) $user->roles ) ) {
                        wp_redirect( wp_login_url( (string) get_permalink() ) ); // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
                        exit;
                    }
                }
            }
        );
        return true;
    }

    return $handled;
}
add_filter( 'spx_boson_handle_rule', 'spx_boson_example_handle_custom_rule', 10, 2 );

/**
 * Example 4: React to options being loaded.
 *
 * @param array<string, mixed> $options The loaded SCF options.
 */
function spx_boson_example_options_loaded( array $options ): void {
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( 'Boson Scaffold options loaded for site ' . get_current_blog_id() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
    }

    if ( isset( $options['api_access']['enabled'] ) && $options['api_access']['enabled'] ) {
        do_action( 'spx_boson_api_init', $options['api_access'] );
    }
}
add_action( 'spx_boson_options_loaded', 'spx_boson_example_options_loaded' );

/**
 * Example 5: React to rules being enforced.
 *
 * @param array<int, array<string, mixed>> $rules The enforced rules.
 */
function spx_boson_example_rules_enforced( array $rules ): void {
    $enabled_count = count(
        array_filter(
            $rules,
            static fn( array $rule ): bool => isset( $rule['enabled'] ) && (bool) $rule['enabled']
        )
    );

    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( "Boson Scaffold enforced {$enabled_count} rules on site " . get_current_blog_id() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
    }
}
add_action( 'spx_boson_rules_enforced', 'spx_boson_example_rules_enforced' );

/**
 * Example 6: Programmatically update SCF options for the current site.
 */
function spx_boson_example_update_scf_options(): void {
    $plugin      = \Starisian\Sparxstar\BosonScaffold\Plugin::get_instance();
    $scf_manager = $plugin->get_scf_manager();

    if ( null === $scf_manager ) {
        return;
    }

    $options                                  = $scf_manager->get_plugin_options();
    $options['scf_options']['my_custom_field'] = 'my_custom_value';
    $options['scf_options']['security_level']  = 'high';

    $scf_manager->update_plugin_options( $options );
}

/**
 * Example 7: Conditionally run code only when scaffold is enabled for this site.
 */
function spx_boson_example_custom_functionality(): void {
    $plugin      = \Starisian\Sparxstar\BosonScaffold\Plugin::get_instance();
    $scf_manager = $plugin->get_scf_manager();

    if ( null === $scf_manager || ! $scf_manager->is_enabled() ) {
        return;
    }

    $secure_field = $scf_manager->get_option( 'secure_field_name', 'default_value' );

    if ( 'restricted' === $secure_field ) {
        // Apply restrictions.
    }
}

/**
 * Example 8: Update options across all sites in a multisite network.
 */
function spx_boson_example_multisite_update(): void {
    if ( ! is_multisite() ) {
        return;
    }

    $sites = get_sites( array( 'number' => 1000 ) );

    foreach ( $sites as $site ) {
        switch_to_blog( (int) $site->blog_id );

        $plugin      = \Starisian\Sparxstar\BosonScaffold\Plugin::get_instance();
        $scf_manager = $plugin->get_scf_manager();

        if ( null !== $scf_manager && $scf_manager->is_enabled() ) {
            $options                             = $scf_manager->get_plugin_options();
            $options['scf_options']['updated']   = current_time( 'mysql' );
            $scf_manager->update_plugin_options( $options );
        }

        restore_current_blog();
    }
}

/**
 * Example 9: Display an admin notice based on an SCF option.
 */
function spx_boson_example_custom_admin_notice(): void {
    $plugin      = \Starisian\Sparxstar\BosonScaffold\Plugin::get_instance();
    $scf_manager = $plugin->get_scf_manager();

    if ( null === $scf_manager || ! $scf_manager->is_enabled() ) {
        return;
    }

    $security_level = $scf_manager->get_option( 'security_level' );

    if ( 'high' === $security_level ) {
        echo '<div class="notice notice-info is-dismissible">';
        echo '<p><strong>' . esc_html__( 'Security Notice:', 'sparxstar-boson' ) . '</strong> ' . esc_html__( 'High security mode is enabled for this site.', 'sparxstar-boson' ) . '</p>';
        echo '</div>';
    }
}
add_action( 'admin_notices', 'spx_boson_example_custom_admin_notice' );

/**
 * Example 10: Hook into specific rule types.
 *
 * @param array<string, mixed> $rule The rule configuration.
 */
function spx_boson_example_access_control_handler( array $rule ): void {
    if ( isset( $rule['condition'] ) && 'user_role' === $rule['condition'] ) {
        $required_role = (string) $rule['value'];

        add_filter(
            'user_has_cap',
            static function ( array $allcaps, array $caps, array $args, \WP_User $user ) use ( $required_role ): array { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
                return $allcaps;
            },
            10,
            4
        );
    }
}
add_action( 'spx_boson_access_control_rule', 'spx_boson_example_access_control_handler' );
