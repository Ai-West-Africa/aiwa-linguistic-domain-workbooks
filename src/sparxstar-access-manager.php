<?php
/**
 * Frontend Access Control Module
 *
 * Complete access control enforcement (Query, REST, AJAX, and Template).
 * This module integrates with Advanced Custom Fields (ACF) for configuration.
 *
 * @package   Starisian\Sparxstar\BosonScaffold
 * @license   MIT https://opensource.org/licenses/MIT
 * @copyright Copyright (c) 2026 Starisian Technologies
 */

declare(strict_types=1);

namespace Starisian\Sparxstar\BosonScaffold;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* --------------------------------------------------------------------------
 * PART 1: FIELD REGISTRATION
 * -------------------------------------------------------------------------- */
add_action(
    'acf/init',
    static function (): void {
        if ( ! function_exists( 'acf_add_options_page' ) ) {
            return;
        }

        acf_add_options_page(
            array(
                'page_title' => 'Site Access',
                'menu_slug'  => 'spx-site-access',
                'menu_title' => 'Frontend Restrictions',
                'menu_icon'  => 'dashicons-shield',
                'redirect'   => false,
                'autoload'   => true,
                'capability' => 'manage_options',
            )
        );
    }
);

add_action(
    'acf/include_fields',
    static function (): void {
        if ( ! function_exists( 'acf_add_local_field_group' ) ) {
            return;
        }

        acf_add_local_field_group(
            array(
                'key'      => 'group_spx_site_access',
                'title'    => 'Site Access',
                'location' => array(
                    array(
                        array(
                            'param'    => 'options_page',
                            'operator' => '==',
                            'value'    => 'spx-site-access',
                        ),
                    ),
                ),
                'fields'   => array(
                    array(
                        'key'           => 'field_spx_frontend_enabled',
                        'label'         => 'Frontend Post Type Restrictions Enabled',
                        'name'          => 'spx_frontend_restrictions_enabled',
                        'type'          => 'true_false',
                        'ui'            => 1,
                        'default_value' => 1,
                    ),
                    array(
                        'key'               => 'field_spx_default_redirect',
                        'label'             => 'Default Redirection Target URL',
                        'name'              => 'spx_default_redirection_target_url',
                        'type'              => 'url',
                        'required'          => 1,
                        'default_value'     => home_url(),
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field'    => 'field_spx_frontend_enabled',
                                    'operator' => '==',
                                    'value'    => '1',
                                ),
                            ),
                        ),
                    ),
                    array(
                        'key'           => 'field_spx_admin_restricted',
                        'label'         => 'Restrict WP-Admin',
                        'name'          => 'spx_restrict_wp_admin',
                        'type'          => 'true_false',
                        'ui'            => 1,
                        'default_value' => 1,
                    ),
                    array(
                        'key'               => 'field_spx_admin_redirect',
                        'label'             => 'Redirect URL After Login',
                        'name'              => 'spx_redirect_url_after_login',
                        'type'              => 'url',
                        'default_value'     => home_url( '/star-dashboard/' ),
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field'    => 'field_spx_admin_restricted',
                                    'operator' => '==',
                                    'value'    => '1',
                                ),
                            ),
                        ),
                    ),
                    array(
                        'key'               => 'field_spx_admin_roles',
                        'label'             => 'Permitted User Groups',
                        'name'              => 'spx_permitted_user_groups',
                        'type'              => 'select',
                        'multiple'          => 1,
                        'return_format'     => 'value',
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field'    => 'field_spx_admin_restricted',
                                    'operator' => '==',
                                    'value'    => '1',
                                ),
                            ),
                        ),
                    ),
                    array(
                        'key'               => 'field_spx_admin_users',
                        'label'             => 'Permitted User Access',
                        'name'              => 'spx_permitted_user_access',
                        'type'              => 'user',
                        'multiple'          => 1,
                        'return_format'     => 'array',
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field'    => 'field_spx_admin_restricted',
                                    'operator' => '==',
                                    'value'    => '1',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        acf_add_local_field_group(
            array(
                'key'      => 'group_spx_frontend',
                'title'    => 'Frontend Restrictions',
                'location' => array(
                    array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'post' ) ),
                    array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'page' ) ),
                ),
                'position' => 'side',
                'fields'   => array(
                    array(
                        'key'           => 'field_spx_restrict',
                        'label'         => 'Restrict Frontend Access',
                        'name'          => 'spx_frontend_restriction',
                        'type'          => 'true_false',
                        'default_value' => 0,
                    ),
                    array(
                        'key'               => 'field_spx_state',
                        'label'             => 'Allow If',
                        'name'              => 'spx_frontend_allow_if',
                        'type'              => 'select',
                        'choices'           => array(
                            'logged_in'  => 'Logged In',
                            'logged_out' => 'Logged Out',
                            'all'        => 'All',
                        ),
                        'default_value'     => 'logged_in',
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field'    => 'field_spx_restrict',
                                    'operator' => '==',
                                    'value'    => '1',
                                ),
                            ),
                        ),
                    ),
                    array(
                        'key'               => 'field_spx_roles',
                        'label'             => 'Allowed Roles',
                        'name'              => 'spx_frontend_allowed_roles',
                        'type'              => 'select',
                        'multiple'          => 1,
                        'ui'                => 1,
                        'conditional_logic' => array(
                            array(
                                array( 'field' => 'field_spx_restrict', 'operator' => '==', 'value' => '1' ),
                                array( 'field' => 'field_spx_state', 'operator' => '==', 'value' => 'logged_in' ),
                            ),
                            array(
                                array( 'field' => 'field_spx_restrict', 'operator' => '==', 'value' => '1' ),
                                array( 'field' => 'field_spx_state', 'operator' => '==', 'value' => 'logged_out' ),
                            ),
                        ),
                    ),
                    array(
                        'key'               => 'field_spx_users',
                        'label'             => 'Allowed Users',
                        'name'              => 'spx_frontend_allowed_users',
                        'type'              => 'user',
                        'multiple'          => 1,
                        'return_format'     => 'array',
                        'conditional_logic' => array(
                            array(
                                array( 'field' => 'field_spx_restrict', 'operator' => '==', 'value' => '1' ),
                                array( 'field' => 'field_spx_state', 'operator' => '==', 'value' => 'logged_in' ),
                            ),
                        ),
                    ),
                    array(
                        'key'               => 'field_spx_redirect',
                        'label'             => 'Redirect URL',
                        'name'              => 'spx_frontend_redirect_url',
                        'type'              => 'url',
                        'instructions'      => 'Leave empty to use Default.',
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field'    => 'field_spx_restrict',
                                    'operator' => '==',
                                    'value'    => '1',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );
    }
);

/* --------------------------------------------------------------------------
 * PART 2: SECURITY ENFORCEMENT
 * -------------------------------------------------------------------------- */

/**
 * Frontend access control enforcement class.
 *
 * Hooks into WordPress query, REST, AJAX, template, and admin flows to
 * enforce ACF-based access restrictions defined in the Site Access options page.
 */
class FrontendAccess {

    /**
     * Register all enforcement hooks.
     */
    public function __construct() {
        add_filter( 'acf/load_field/name=spx_frontend_allowed_roles', array( $this, 'load_roles' ) );
        add_filter( 'acf/load_field/name=spx_permitted_user_groups', array( $this, 'load_roles' ) );

        add_action( 'init', array( $this, 'secure_ajax' ), 1 );

        add_action( 'pre_get_posts', array( $this, 'filter_main_query' ) );
        add_filter( 'rest_post_query', array( $this, 'filter_rest_query' ), 10, 2 );
        add_filter( 'rest_page_query', array( $this, 'filter_rest_query' ), 10, 2 );

        add_action( 'template_redirect', array( $this, 'enforce_single_view' ), 1 );
        add_filter( 'rest_prepare_post', array( $this, 'secure_single_rest' ), 10, 3 );
        add_filter( 'rest_prepare_page', array( $this, 'secure_single_rest' ), 10, 3 );

        add_filter( 'the_content', array( $this, 'secure_content_output' ) );
        add_filter( 'the_excerpt', array( $this, 'secure_content_output' ) );

        add_action( 'admin_init', array( $this, 'enforce_admin' ) );
        add_filter( 'login_redirect', array( $this, 'login_redirect' ), 10, 3 );
    }

    /* ----------------------------------------------------------------
     * 1. UNIVERSAL AJAX GUARD
     * ---------------------------------------------------------------- */

    /**
     * Block AJAX requests for restricted posts from non-admin users.
     */
    public function secure_ajax(): void {
        if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
            return;
        }
        if ( current_user_can( 'manage_options' ) || is_super_admin() ) {
            return;
        }

        $post_id = 0;
        // phpcs:disable WordPress.Security.NonceVerification.Recommended
        if ( isset( $_REQUEST['post_id'] ) ) {
            $post_id = absint( $_REQUEST['post_id'] );
        } elseif ( isset( $_REQUEST['id'] ) ) {
            $post_id = absint( $_REQUEST['id'] );
        } elseif ( isset( $_REQUEST['pid'] ) ) {
            $post_id = absint( $_REQUEST['pid'] );
        }
        // phpcs:enable WordPress.Security.NonceVerification.Recommended

        if ( ! $post_id ) {
            return;
        }

        $post = get_post( $post_id );
        if ( ! $post instanceof \WP_Post ) {
            return;
        }

        if ( ! $this->is_allowed( $post ) ) {
            wp_send_json_error( array( 'message' => 'Access denied' ), 403 );
        }
    }

    /* ----------------------------------------------------------------
     * 2. QUERY & LIST FILTERING
     * ---------------------------------------------------------------- */

    /**
     * Filter main WordPress query to exclude restricted posts.
     *
     * @param \WP_Query $query The current query object.
     */
    public function filter_main_query( \WP_Query $query ): void {
        if ( is_admin() || current_user_can( 'manage_options' ) || ( is_multisite() && is_super_admin() ) ) {
            return;
        }

        if ( ! $query->is_main_query() && ! $query->is_search() && ! $query->is_feed() && ! $query->is_archive() ) {
            return;
        }

        $this->apply_exclusion_query( $query );
    }

    /**
     * Append meta query exclusion to REST API query args.
     *
     * @param array<string, mixed>      $args    Existing query args.
     * @param \WP_REST_Request<mixed[]> $request The REST request.
     * @return array<string, mixed>
     */
    public function filter_rest_query( array $args, \WP_REST_Request $request ): array { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
        if ( current_user_can( 'manage_options' ) || ( is_multisite() && is_super_admin() ) ) {
            return $args;
        }

        $meta_query   = isset( $args['meta_query'] ) && is_array( $args['meta_query'] ) ? $args['meta_query'] : array();
        $meta_query[] = array(
            'relation' => 'OR',
            array(
                'key'     => 'spx_frontend_restriction',
                'compare' => 'NOT EXISTS',
            ),
            array(
                'key'     => 'spx_frontend_restriction',
                'value'   => '1',
                'compare' => '!=',
            ),
        );

        $args['meta_query'] = $meta_query;
        return $args;
    }

    /**
     * Mutate a WP_Query object to exclude restricted posts via meta query.
     *
     * @param \WP_Query $query The query to modify.
     */
    private function apply_exclusion_query( \WP_Query $query ): void {
        $meta_query   = $query->get( 'meta_query' );
        $meta_query   = is_array( $meta_query ) ? $meta_query : array();
        $meta_query[] = array(
            'relation' => 'OR',
            array(
                'key'     => 'spx_frontend_restriction',
                'compare' => 'NOT EXISTS',
            ),
            array(
                'key'     => 'spx_frontend_restriction',
                'value'   => '1',
                'compare' => '!=',
            ),
        );
        $query->set( 'meta_query', $meta_query );
    }

    /* ----------------------------------------------------------------
     * 3. SINGLE VIEW ENFORCEMENT
     * ---------------------------------------------------------------- */

    /**
     * Redirect visitors away from restricted single-post pages.
     */
    public function enforce_single_view(): void {
        if ( is_admin() ) {
            return;
        }

        global $post;
        if ( ! $post instanceof \WP_Post ) {
            return;
        }

        if ( $this->is_allowed( $post ) ) {
            return;
        }

        $redirect = function_exists( 'get_field' )
            ? ( get_field( 'spx_frontend_redirect_url', $post->ID )
                ?: get_field( 'spx_default_redirection_target_url', 'option' )
                ?: home_url() )
            : home_url();

        if ( $this->is_current_url( (string) $redirect ) ) {
            return;
        }

        wp_safe_redirect( (string) $redirect );
        exit;
    }

    /**
     * Return a 403 error for restricted posts in single REST API responses.
     *
     * @param \WP_REST_Response         $response Current response.
     * @param \WP_Post                  $post     The requested post.
     * @param \WP_REST_Request<mixed[]> $request  The REST request.
     * @return \WP_REST_Response|\WP_Error
     */
    public function secure_single_rest( \WP_REST_Response $response, \WP_Post $post, \WP_REST_Request $request ): \WP_REST_Response|\WP_Error { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
        if ( ! $this->is_allowed( $post ) ) {
            return new \WP_Error(
                'rest_forbidden',
                __( 'You do not have permission to view this content.', 'sparxstar-boson' ),
                array( 'status' => 403 )
            );
        }
        return $response;
    }

    /* ----------------------------------------------------------------
     * 4. OUTPUT SCRUBBING
     * ---------------------------------------------------------------- */

    /**
     * Strip content for restricted posts as a last-resort fallback.
     *
     * @param string $content The post content or excerpt.
     * @return string
     */
    public function secure_content_output( string $content ): string {
        if ( is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
            return $content;
        }

        global $post;
        if ( ! $post instanceof \WP_Post ) {
            return $content;
        }

        if ( ! $this->is_allowed( $post ) ) {
            /** @var string $placeholder */
            $placeholder = apply_filters( 'spx_boson_restricted_placeholder', '' );
            return $placeholder;
        }

        return $content;
    }

    /* ----------------------------------------------------------------
     * CORE LOGIC & UTILITIES
     * ---------------------------------------------------------------- */

    /**
     * Determine whether the current user may access a post.
     *
     * @param \WP_Post $post The post to check.
     * @return bool True if access is permitted.
     */
    private function is_allowed( \WP_Post $post ): bool {
        if ( ! function_exists( 'get_field' ) ) {
            return true;
        }

        if ( ! get_field( 'spx_frontend_restrictions_enabled', 'option' ) ) {
            return true;
        }

        if ( (int) get_field( 'spx_frontend_restriction', $post->ID ) !== 1 ) {
            return true;
        }

        $user = wp_get_current_user();
        if (
            $user instanceof \WP_User
            && (
                user_can( $user, 'manage_options' )
                || ( function_exists( 'is_super_admin' ) && is_super_admin( (int) $user->ID ) )
            )
        ) {
            return true;
        }

        $state = (string) get_field( 'spx_frontend_allow_if', $post->ID );
        $roles = (array) get_field( 'spx_frontend_allowed_roles', $post->ID );
        $users = (array) get_field( 'spx_frontend_allowed_users', $post->ID );

        $logged_in = is_user_logged_in();

        if ( 'logged_in' === $state && ! $logged_in ) {
            return false;
        }
        if ( 'logged_out' === $state && $logged_in ) {
            return false;
        }

        if ( ! $logged_in ) {
            return empty( $roles ) && empty( $users );
        }

        foreach ( $users as $u ) {
            $uid = is_array( $u ) ? (int) $u['ID'] : (int) $u;
            if ( $uid === (int) $user->ID ) {
                return true;
            }
        }

        if ( in_array( 'author_only', $roles, true ) && (int) $post->post_author === (int) $user->ID ) {
            return true;
        }

        $roles_clean = array_diff( $roles, array( 'author_only' ) );
        if ( ! empty( $roles_clean ) ) {
            return (bool) array_intersect( $roles_clean, (array) $user->roles );
        }

        return empty( $users ) && empty( $roles_clean );
    }

    /**
     * Enforce admin area restrictions based on ACF settings.
     */
    public function enforce_admin(): void {
        if ( ! function_exists( 'get_field' ) ) {
            return;
        }
        if ( ! get_field( 'spx_restrict_wp_admin', 'option' ) ) {
            return;
        }
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            return;
        }

        $user = wp_get_current_user();
        if (
            $user instanceof \WP_User
            && (
                user_can( $user, 'manage_options' )
                || ( is_multisite() && is_super_admin( (int) $user->ID ) )
            )
        ) {
            return;
        }

        $roles = (array) get_field( 'spx_permitted_user_groups', 'option' );
        $users = (array) get_field( 'spx_permitted_user_access', 'option' );

        foreach ( $users as $u ) {
            $uid = is_array( $u ) ? (int) $u['ID'] : (int) $u;
            if ( $uid === (int) $user->ID ) {
                return;
            }
        }

        if ( ! empty( $roles ) && array_intersect( $roles, (array) $user->roles ) ) {
            return;
        }

        wp_safe_redirect( (string) apply_filters( 'spx_boson_admin_redirect_url', home_url() ) );
        exit;
    }

    /**
     * Populate ACF role-select fields with registered WordPress roles.
     *
     * @param array<string, mixed> $field The ACF field definition.
     * @return array<string, mixed>
     */
    public function load_roles( array $field ): array {
        global $wp_roles;
        if ( ! isset( $wp_roles ) ) {
            $wp_roles = wp_roles();
        }
        $field['choices'] = array();
        foreach ( $wp_roles->roles as $key => $role ) {
            $field['choices'][ $key ] = translate_user_role( $role['name'] );
        }
        if ( 'spx_frontend_allowed_roles' === $field['name'] ) {
            $field['choices']['author_only'] = 'Author Only';
        }
        return $field;
    }

    /**
     * Redirect non-admin users to the configured dashboard after login.
     *
     * @param string                    $redirect_to The redirect destination URL.
     * @param string                    $request     The requested redirect destination.
     * @param \WP_User|\WP_Error        $user        The authenticated user or error.
     * @return string
     */
    public function login_redirect( string $redirect_to, string $request, \WP_User|\WP_Error $user ): string { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
        if ( ! $user instanceof \WP_User ) {
            return $redirect_to;
        }
        $is_admin_user = $user instanceof \WP_User
            && (
                user_can( $user, 'manage_options' )
                || ( is_multisite() && is_super_admin( (int) $user->ID ) )
            );
        if ( function_exists( 'get_field' )
            && get_field( 'spx_restrict_wp_admin', 'option' )
            && ! $is_admin_user
        ) {
            $dash = get_field( 'spx_redirect_url_after_login', 'option' );
            if ( $dash ) {
                return (string) $dash;
            }
        }
        return $redirect_to;
    }

    /**
     * Check whether a URL matches the current page URL (protocol-agnostic).
     *
     * @param string $redirect_url URL to compare against.
     * @return bool
     */
    private function is_current_url( string $redirect_url ): bool {
        $protocol = is_ssl() ? 'https' : 'http';
        $host     = isset( $_SERVER['HTTP_HOST'] )
            ? sanitize_text_field( wp_unslash( (string) $_SERVER['HTTP_HOST'] ) )
            : '';
        $uri      = isset( $_SERVER['REQUEST_URI'] )
            ? sanitize_text_field( wp_unslash( (string) $_SERVER['REQUEST_URI'] ) )
            : '';
        $current  = $protocol . '://' . $host . $uri;

        return rtrim( preg_replace( '(^https?://)', '', $current ) ?? '', '/' )
            === rtrim( preg_replace( '(^https?://)', '', $redirect_url ) ?? '', '/' );
    }
}

new FrontendAccess();
