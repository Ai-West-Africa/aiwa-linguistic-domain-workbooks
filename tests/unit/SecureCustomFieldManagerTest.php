<?php
/**
 * Test for Secure Custom Field Manager
 *
 * @package   Starisian\Sparxstar\BosonScaffold\Tests
 * @license   MIT https://opensource.org/licenses/MIT
 * @copyright Copyright (c) 2026 Starisian Technologies
 */

declare(strict_types=1);

namespace Starisian\Sparxstar\BosonScaffold\Tests;

use PHPUnit\Framework\TestCase;
use Starisian\Sparxstar\BosonScaffold\SecureCustomFieldManager;

/**
 * Tests for SecureCustomFieldManager.
 */
class SecureCustomFieldManagerTest extends TestCase {

    /**
     * Manager can be instantiated.
     */
    public function test_manager_can_be_instantiated(): void {
        $manager = new SecureCustomFieldManager();
        $this->assertInstanceOf( SecureCustomFieldManager::class, $manager );
    }

    /**
     * get_options() returns an array.
     */
    public function test_get_options_returns_array(): void {
        $manager = new SecureCustomFieldManager();
        $options = $manager->get_options();
        $this->assertIsArray( $options );
    }

    /**
     * get_option() returns the supplied default when key is absent.
     */
    public function test_get_option_with_default(): void {
        $manager = new SecureCustomFieldManager();
        $value   = $manager->get_option( 'nonexistent_key', 'default_value' );
        $this->assertEquals( 'default_value', $value );
    }

    /**
     * Plugin defaults to enabled when no option is stored.
     */
    public function test_is_enabled_default(): void {
        $manager = new SecureCustomFieldManager();
        $this->assertTrue( $manager->is_enabled() );
    }
}
