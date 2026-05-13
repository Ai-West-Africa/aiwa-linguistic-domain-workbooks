<?php
/**
 * Main Plugin Class
 *
 * @package   Starisian\Sparxstar\BosonScaffold
 * @license   MIT https://opensource.org/licenses/MIT
 * @copyright Copyright (c) 2026 Starisian Technologies
 */

declare(strict_types=1);

namespace Starisian\Sparxstar\BosonScaffold;

/**
 * Main plugin orchestrator class (singleton).
 *
 * Rename this class and its namespace when building your own project.
 */
class Plugin {
    /**
     * Plugin instance.
     *
     * @var Plugin|null
     */
    private static ?Plugin $instance = null;

    /**
     * Secure Custom Field Manager.
     *
     * @var SecureCustomFieldManager|null
     */
    private ?SecureCustomFieldManager $scf_manager = null;

    /**
     * Rules Engine.
     *
     * @var RulesEngine|null
     */
    private ?RulesEngine $rules_engine = null;

    /**
     * Admin Manager.
     *
     * @var AdminManager|null
     */
    private ?AdminManager $admin_manager = null;

    /**
     * Get plugin instance.
     *
     * @return Plugin
     */
    public static function get_instance(): Plugin {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Private constructor (singleton).
     */
    private function __construct() {}

    /**
     * Initialize the plugin.
     */
    public function init(): void {
        $this->load_dependencies();

        $this->scf_manager   = new SecureCustomFieldManager();
        $this->rules_engine  = new RulesEngine( $this->scf_manager );
        $this->admin_manager = new AdminManager( $this->scf_manager );

        $this->setup_hooks();
    }

    /**
     * Load plugin class dependencies.
     */
    private function load_dependencies(): void {
        require_once SPX_BOSON_PLUGIN_DIR . 'src/class-secure-custom-field-manager.php';
        require_once SPX_BOSON_PLUGIN_DIR . 'src/class-rules-engine.php';
        require_once SPX_BOSON_PLUGIN_DIR . 'src/class-admin-manager.php';
        require_once SPX_BOSON_PLUGIN_DIR . 'src/sparxstar-access-manager.php';
    }

    /**
     * Register WordPress hooks.
     */
    private function setup_hooks(): void {
        add_action( 'init', array( $this->scf_manager, 'load_options' ), 5 );
        add_action( 'init', array( $this->rules_engine, 'enforce_rules' ), 10 );

        if ( is_admin() ) {
            add_action( 'admin_menu', array( $this->admin_manager, 'add_admin_menu' ) );
            add_action( 'admin_init', array( $this->admin_manager, 'register_settings' ) );
        }

        if ( is_multisite() ) {
            add_action( 'wpmu_new_blog', array( $this, 'activate_on_new_site' ), 10, 1 );
        }
    }

    /**
     * Activate plugin for a specific site.
     *
     * @param int|null $site_id Site ID, or null for single-site installs.
     */
    public static function activate_for_site( ?int $site_id = null ): void {
        if ( null !== $site_id ) {
            switch_to_blog( $site_id );
        }

        $default_options = array(
            'enabled'     => true,
            'scf_options' => array(),
            'rules'       => array(),
        );

        add_option( 'spx_boson_options', $default_options );

        if ( null !== $site_id ) {
            restore_current_blog();
        }
    }

    /**
     * Auto-activate for a newly created multisite blog.
     *
     * @param int $site_id The new site ID.
     */
    public function activate_on_new_site( int $site_id ): void {
        self::activate_for_site( $site_id );
    }

    /**
     * Deactivation cleanup.
     *
     * Note: settings are intentionally preserved on deactivation.
     */
    public static function deactivate(): void {}

    /**
     * Get the Secure Custom Field Manager instance.
     *
     * @return SecureCustomFieldManager|null
     */
    public function get_scf_manager(): ?SecureCustomFieldManager {
        return $this->scf_manager;
    }

    /**
     * Get the Rules Engine instance.
     *
     * @return RulesEngine|null
     */
    public function get_rules_engine(): ?RulesEngine {
        return $this->rules_engine;
    }
}
