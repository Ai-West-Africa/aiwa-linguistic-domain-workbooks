<?php
/**
 * Rules Engine
 *
 * @package   Starisian\Sparxstar\BosonScaffold
 * @license   MIT https://opensource.org/licenses/MIT
 * @copyright Copyright (c) 2026 Starisian Technologies
 */

declare(strict_types=1);

namespace Starisian\Sparxstar\BosonScaffold;

/**
 * Enforces runtime rules based on SCF options.
 *
 * Extend or replace the built-in rule types via the spx_boson_handle_rule filter.
 * Rename all spx_boson_ hooks when building your own project.
 */
class RulesEngine {

    /**
     * SCF Manager instance.
     *
     * @var SecureCustomFieldManager
     */
    private SecureCustomFieldManager $scf_manager;

    /**
     * Rules loaded from options.
     *
     * @var array<int, array<string, mixed>>
     */
    private array $rules = array();

    /**
     * Constructor.
     *
     * @param SecureCustomFieldManager $scf_manager SCF Manager instance.
     */
    public function __construct( SecureCustomFieldManager $scf_manager ) {
        $this->scf_manager = $scf_manager;
    }

    /**
     * Enforce all configured rules.
     *
     * Fires the spx_boson_rules_enforced action after all rules are processed.
     */
    public function enforce_rules(): void {
        if ( ! $this->scf_manager->is_enabled() ) {
            return;
        }

        $plugin_options = $this->scf_manager->get_plugin_options();
        $this->rules    = isset( $plugin_options['rules'] ) && is_array( $plugin_options['rules'] )
            ? $plugin_options['rules']
            : array();

        /** @var array<int, array<string, mixed>> $filtered */
        $filtered    = apply_filters( 'spx_boson_rules', $this->rules );
        $this->rules = $filtered;

        foreach ( $this->rules as $rule ) {
            $this->apply_rule( $rule );
        }

        do_action( 'spx_boson_rules_enforced', $this->rules );
    }

    /**
     * Apply a single rule.
     *
     * @param array<string, mixed> $rule Rule configuration.
     */
    private function apply_rule( array $rule ): void {
        if ( ! isset( $rule['type'], $rule['enabled'] ) || ! $rule['enabled'] ) {
            return;
        }

        /** @var bool $handled */
        $handled = apply_filters( 'spx_boson_handle_rule', false, $rule );

        if ( $handled ) {
            return;
        }

        switch ( (string) $rule['type'] ) {
            case 'access_control':
                $this->apply_access_control_rule( $rule );
                break;
            case 'field_validation':
                $this->apply_field_validation_rule( $rule );
                break;
            case 'content_restriction':
                $this->apply_content_restriction_rule( $rule );
                break;
            default:
                do_action( 'spx_boson_unknown_rule_type', (string) $rule['type'], $rule );
                break;
        }
    }

    /**
     * Apply an access control rule.
     *
     * @param array<string, mixed> $rule Rule configuration.
     */
    private function apply_access_control_rule( array $rule ): void {
        do_action( 'spx_boson_access_control_rule', $rule );
    }

    /**
     * Apply a field validation rule.
     *
     * @param array<string, mixed> $rule Rule configuration.
     */
    private function apply_field_validation_rule( array $rule ): void {
        do_action( 'spx_boson_field_validation_rule', $rule );
    }

    /**
     * Apply a content restriction rule.
     *
     * @param array<string, mixed> $rule Rule configuration.
     */
    private function apply_content_restriction_rule( array $rule ): void {
        do_action( 'spx_boson_content_restriction_rule', $rule );
    }

    /**
     * Get all configured rules.
     *
     * @return array<int, array<string, mixed>>
     */
    public function get_rules(): array {
        return $this->rules;
    }
}
