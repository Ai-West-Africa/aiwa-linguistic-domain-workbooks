<?php
/**
 * Test for Rules Engine
 *
 * @package   Starisian\Sparxstar\BosonScaffold\Tests
 * @license   MIT https://opensource.org/licenses/MIT
 * @copyright Copyright (c) 2026 Starisian Technologies
 */

declare(strict_types=1);

namespace Starisian\Sparxstar\BosonScaffold\Tests;

use PHPUnit\Framework\TestCase;
use Starisian\Sparxstar\BosonScaffold\SecureCustomFieldManager;
use Starisian\Sparxstar\BosonScaffold\RulesEngine;

/**
 * Tests for RulesEngine.
 */
class RulesEngineTest extends TestCase {

    /**
     * Rules engine can be instantiated.
     */
    public function test_engine_can_be_instantiated(): void {
        $scf_manager = new SecureCustomFieldManager();
        $engine      = new RulesEngine( $scf_manager );
        $this->assertInstanceOf( RulesEngine::class, $engine );
    }

    /**
     * get_rules() returns an array.
     */
    public function test_get_rules_returns_array(): void {
        $scf_manager = new SecureCustomFieldManager();
        $engine      = new RulesEngine( $scf_manager );
        $rules       = $engine->get_rules();
        $this->assertIsArray( $rules );
    }

    /**
     * enforce_rules() executes without exception.
     */
    public function test_enforce_rules_executes(): void {
        $scf_manager = new SecureCustomFieldManager();
        $engine      = new RulesEngine( $scf_manager );
        $engine->enforce_rules();
        $this->assertTrue( true );
    }
}
