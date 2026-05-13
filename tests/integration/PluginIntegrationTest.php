<?php
/**
 * Integration test for plugin loading.
 *
 * @package   Starisian\Sparxstar\BosonScaffold\Tests
 * @license   MIT https://opensource.org/licenses/MIT
 * @copyright Copyright (c) 2026 Starisian Technologies
 */

declare(strict_types=1);

namespace Starisian\Sparxstar\BosonScaffold\Tests;

use PHPUnit\Framework\TestCase;
use Starisian\Sparxstar\BosonScaffold\Plugin;
use Starisian\Sparxstar\BosonScaffold\SecureCustomFieldManager;
use Starisian\Sparxstar\BosonScaffold\RulesEngine;

/**
 * Integration tests for full plugin lifecycle.
 */
class PluginIntegrationTest extends TestCase {

    /**
     * Plugin::get_instance() returns the same singleton instance.
     */
    public function test_plugin_singleton(): void {
        $plugin1 = Plugin::get_instance();
        $plugin2 = Plugin::get_instance();
        $this->assertSame( $plugin1, $plugin2, 'Plugin should be a singleton' );
    }

    /**
     * Calling init() populates the SCF manager and rules engine.
     */
    public function test_plugin_initialization(): void {
        $plugin = Plugin::get_instance();
        $plugin->init();

        $scf_manager = $plugin->get_scf_manager();
        $this->assertInstanceOf( SecureCustomFieldManager::class, $scf_manager );

        $rules_engine = $plugin->get_rules_engine();
        $this->assertInstanceOf( RulesEngine::class, $rules_engine );
    }

    /**
     * Full workflow: init → load_options → enforce_rules — no exceptions thrown.
     */
    public function test_full_workflow(): void {
        $plugin = Plugin::get_instance();
        $plugin->init();

        $scf_manager  = $plugin->get_scf_manager();
        $rules_engine = $plugin->get_rules_engine();

        $this->assertNotNull( $scf_manager );
        $this->assertNotNull( $rules_engine );

        $scf_manager->load_options();
        $this->assertIsArray( $scf_manager->get_options() );

        $rules_engine->enforce_rules();
        $this->assertIsArray( $rules_engine->get_rules() );

        $this->assertTrue( true, 'Full workflow completed successfully' );
    }
}
