<?php
/**
 * Admin Manager
 *
 * @package   Starisian\Sparxstar\BosonScaffold
 * @license   MIT https://opensource.org/licenses/MIT
 * @copyright Copyright (c) 2026 Starisian Technologies
 */

declare(strict_types=1);

namespace Starisian\Sparxstar\BosonScaffold;

/**
 * Manages the admin interface for subsite-specific settings.
 *
 * Each subsite has its own settings page. There are deliberately no
 * network-level settings — all configuration is per-subsite.
 *
 * Rename all spx_boson_ slugs, option keys, and text-domain references
 * when building your own project.
 */
class AdminManager {

    /**
     * Settings option group and page slug.
     *
     * @var string
     */
    private const OPTION_GROUP = 'spx_boson_options';

    /**
     * Admin page menu slug.
     *
     * @var string
     */
    private const MENU_SLUG = 'spx-boson';

    /**
     * SCF Manager instance.
     *
     * @var SecureCustomFieldManager
     */
    private SecureCustomFieldManager $scf_manager;

    /**
     * Constructor.
     *
     * @param SecureCustomFieldManager $scf_manager SCF Manager instance.
     */
    public function __construct( SecureCustomFieldManager $scf_manager ) {
        $this->scf_manager = $scf_manager;
    }

    /**
     * Add the subsite settings page to the admin menu.
     */
    public function add_admin_menu(): void {
        if ( is_multisite() && is_network_admin() ) {
            return;
        }

        add_options_page(
            __( 'SPARXSTAR Boson Scaffold', 'sparxstar-boson' ),
            __( 'Boson Scaffold', 'sparxstar-boson' ),
            'manage_options',
            self::MENU_SLUG,
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * Register settings sections and fields for the subsite admin page.
     */
    public function register_settings(): void {
        if ( is_multisite() && is_network_admin() ) {
            return;
        }

        register_setting(
            self::OPTION_GROUP,
            'spx_boson_options',
            array(
                'sanitize_callback' => array( $this, 'sanitize_options' ),
            )
        );

        add_settings_section(
            'spx_boson_general',
            __( 'General Settings', 'sparxstar-boson' ),
            array( $this, 'render_general_section' ),
            self::MENU_SLUG
        );

        add_settings_field(
            'enabled',
            __( 'Enable Scaffold', 'sparxstar-boson' ),
            array( $this, 'render_enabled_field' ),
            self::MENU_SLUG,
            'spx_boson_general'
        );

        add_settings_section(
            'spx_boson_scf',
            __( 'Secure Custom Field Options', 'sparxstar-boson' ),
            array( $this, 'render_scf_section' ),
            self::MENU_SLUG
        );

        add_settings_field(
            'scf_options',
            __( 'SCF Configuration', 'sparxstar-boson' ),
            array( $this, 'render_scf_options_field' ),
            self::MENU_SLUG,
            'spx_boson_scf'
        );

        add_settings_section(
            'spx_boson_rules',
            __( 'Runtime Rules', 'sparxstar-boson' ),
            array( $this, 'render_rules_section' ),
            self::MENU_SLUG
        );

        add_settings_field(
            'rules',
            __( 'Rules Configuration', 'sparxstar-boson' ),
            array( $this, 'render_rules_field' ),
            self::MENU_SLUG,
            'spx_boson_rules'
        );
    }

    /**
     * Render the settings page.
     */
    public function render_settings_page(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( is_multisite() ) {
            $site_id = get_current_blog_id();
            echo '<div class="notice notice-info"><p>';
            printf(
                /* translators: %d: Site ID */
                esc_html__( 'These settings apply to this subsite only (ID: %d). Each subsite has its own independent configuration.', 'sparxstar-boson' ),
                (int) $site_id
            );
            echo '</p></div>';
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields( self::OPTION_GROUP );
                do_settings_sections( self::MENU_SLUG );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Render the General Settings section description.
     */
    public function render_general_section(): void {
        echo '<p>' . esc_html__( 'Configure general settings for this site.', 'sparxstar-boson' ) . '</p>';
    }

    /**
     * Render the SCF Options section description.
     */
    public function render_scf_section(): void {
        echo '<p>' . esc_html__( 'Configure Secure Custom Field options that will be loaded and enforced for this site.', 'sparxstar-boson' ) . '</p>';
    }

    /**
     * Render the Rules section description.
     */
    public function render_rules_section(): void {
        echo '<p>' . esc_html__( 'Define runtime rules to be enforced based on SCF options.', 'sparxstar-boson' ) . '</p>';
    }

    /**
     * Render the enabled/disabled toggle field.
     */
    public function render_enabled_field(): void {
        $options = get_option( 'spx_boson_options', array() );
        $enabled = isset( $options['enabled'] ) ? (bool) $options['enabled'] : true;
        ?>
        <label>
            <input type="checkbox" name="spx_boson_options[enabled]" value="1" <?php checked( $enabled, true ); ?> />
            <?php esc_html_e( 'Enable this scaffold for the current site', 'sparxstar-boson' ); ?>
        </label>
        <?php
    }

    /**
     * Render the SCF options JSON textarea.
     */
    public function render_scf_options_field(): void {
        $options     = get_option( 'spx_boson_options', array() );
        $scf_options = isset( $options['scf_options'] ) && is_array( $options['scf_options'] ) ? $options['scf_options'] : array();
        $scf_json    = wp_json_encode( $scf_options, JSON_PRETTY_PRINT );
        ?>
        <textarea name="spx_boson_options[scf_options]" rows="10" class="large-text code"><?php echo esc_textarea( (string) $scf_json ); ?></textarea>
        <p class="description"><?php esc_html_e( 'Enter SCF options in JSON format. Example: {"field_name": "value", "access_level": "admin"}', 'sparxstar-boson' ); ?></p>
        <?php
    }

    /**
     * Render the runtime rules JSON textarea.
     */
    public function render_rules_field(): void {
        $options    = get_option( 'spx_boson_options', array() );
        $rules      = isset( $options['rules'] ) && is_array( $options['rules'] ) ? $options['rules'] : array();
        $rules_json = wp_json_encode( $rules, JSON_PRETTY_PRINT );
        ?>
        <textarea name="spx_boson_options[rules]" rows="10" class="large-text code"><?php echo esc_textarea( (string) $rules_json ); ?></textarea>
        <p class="description">
            <?php esc_html_e( 'Enter rules in JSON format. Example:', 'sparxstar-boson' ); ?>
            <br>
            <code>[{"type": "access_control", "enabled": true, "condition": "user_role", "value": "editor"}]</code>
        </p>
        <?php
    }

    /**
     * Sanitize, validate, and return plugin options from the settings form.
     *
     * Follows the Sanitize → Validate → Escape order.
     *
     * @param array<string, mixed> $input Raw input from the settings form.
     * @return array<string, mixed> Sanitized options.
     */
    public function sanitize_options( array $input ): array {
        $sanitized = array();

        $sanitized['enabled'] = isset( $input['enabled'] ) && (bool) $input['enabled'];

        if ( isset( $input['scf_options'] ) ) {
            $raw = $input['scf_options'];
            if ( is_string( $raw ) && '' === trim( $raw ) ) {
                $sanitized['scf_options'] = array();
            } else {
                $decoded = json_decode( (string) $raw, true );
                if ( JSON_ERROR_NONE === json_last_error() && is_array( $decoded ) ) {
                    $sanitized['scf_options'] = $decoded;
                } else {
                    add_settings_error(
                        self::OPTION_GROUP,
                        'spx_boson_invalid_scf_json',
                        __( 'SCF options must be a valid JSON object or array.', 'sparxstar-boson' )
                    );
                    $sanitized['scf_options'] = array();
                }
            }
        }

        if ( isset( $input['rules'] ) ) {
            $raw = $input['rules'];
            if ( is_string( $raw ) && '' === trim( $raw ) ) {
                $sanitized['rules'] = array();
            } else {
                $decoded = json_decode( (string) $raw, true );
                if ( JSON_ERROR_NONE === json_last_error() && is_array( $decoded ) ) {
                    $sanitized['rules'] = $decoded;
                } else {
                    add_settings_error(
                        self::OPTION_GROUP,
                        'spx_boson_invalid_rules_json',
                        __( 'Rules must be a valid JSON array.', 'sparxstar-boson' )
                    );
                    $sanitized['rules'] = array();
                }
            }
        }

        return $sanitized;
    }
}
