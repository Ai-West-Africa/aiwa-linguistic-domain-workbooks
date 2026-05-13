<?php
/**
 * Secure Custom Field Manager
 *
 * @package   Starisian\Sparxstar\BosonScaffold
 * @license   MIT https://opensource.org/licenses/MIT
 * @copyright Copyright (c) 2026 Starisian Technologies
 */

declare(strict_types=1);

namespace Starisian\Sparxstar\BosonScaffold;

/**
 * Manages Secure Custom Field options for the current site.
 *
 * Options are stored per-subsite in the site's own options table.
 * Rename the option key (spx_boson_options) when building your own project.
 */
class SecureCustomFieldManager {

    /**
     * Site-specific options key.
     *
     * @var string
     */
    private const OPTION_KEY = 'spx_boson_options';

    /**
     * Loaded SCF options.
     *
     * @var array<string, mixed>
     */
    private array $scf_options = array();

    /**
     * Plugin options (includes enabled flag, scf_options, rules).
     *
     * @var array<string, mixed>
     */
    private array $plugin_options = array();

    /**
     * Load SCF options from the database (site-specific, no network-level options).
     */
    public function load_options(): void {
        /** @var array<string, mixed>|false $stored */
        $stored = get_option( self::OPTION_KEY, false );

        $defaults = array(
            'enabled'     => true,
            'scf_options' => array(),
            'rules'       => array(),
        );

        if ( false === $stored || ! is_array( $stored ) ) {
            $this->plugin_options = $defaults;
            update_option( self::OPTION_KEY, $this->plugin_options );
        } else {
            $this->plugin_options = $stored;
        }

        $this->scf_options = isset( $this->plugin_options['scf_options'] ) && is_array( $this->plugin_options['scf_options'] )
            ? $this->plugin_options['scf_options']
            : array();

        /** @var array<string, mixed> $filtered */
        $filtered          = apply_filters( 'spx_boson_scf_options', $this->scf_options );
        $this->scf_options = $filtered;

        do_action( 'spx_boson_options_loaded', $this->scf_options );
    }

    /**
     * Get all SCF options.
     *
     * @return array<string, mixed>
     */
    public function get_options(): array {
        return $this->scf_options;
    }

    /**
     * Get a specific SCF option.
     *
     * @param string $key     Option key.
     * @param mixed  $default Default value if key is not set.
     * @return mixed
     */
    public function get_option( string $key, mixed $default = null ): mixed {
        return $this->scf_options[ $key ] ?? $default;
    }

    /**
     * Set SCF options and persist to the database.
     *
     * @param array<string, mixed> $options Options to save.
     * @return bool True on success.
     */
    public function set_options( array $options ): bool {
        $this->scf_options                    = $options;
        $this->plugin_options['scf_options'] = $options;
        return update_option( self::OPTION_KEY, $this->plugin_options );
    }

    /**
     * Check whether this plugin is enabled for the current site.
     *
     * @return bool
     */
    public function is_enabled(): bool {
        if ( array_key_exists( 'enabled', $this->plugin_options ) ) {
            return (bool) $this->plugin_options['enabled'];
        }
        return true;
    }

    /**
     * Get all raw plugin options.
     *
     * @return array<string, mixed>
     */
    public function get_plugin_options(): array {
        return $this->plugin_options;
    }

    /**
     * Merge and persist plugin options.
     *
     * @param array<string, mixed> $options Options to merge.
     * @return bool True on success.
     */
    public function update_plugin_options( array $options ): bool {
        $this->plugin_options = array_merge( $this->plugin_options, $options );
        return update_option( self::OPTION_KEY, $this->plugin_options );
    }
}
