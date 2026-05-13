# API Documentation

## Overview

Sparxstar Access Manager provides a comprehensive API for managing Secure Custom Field (SCF) options and runtime rules in WordPress multi-site environments.

## Classes

### Plugin

Main plugin orchestrator class.

**Location:** `src/class-plugin.php`

**Methods:**

#### `get_instance(): Plugin`

Get the singleton instance of the plugin.

```php
$plugin = \Starisian\Sparxstar\BosonScaffold\Plugin::get_instance();
```

#### `init(): void`

Initialize the plugin. Called automatically on `plugins_loaded` hook.

#### `get_scf_manager(): SecureCustomFieldManager`

Get the Secure Custom Field Manager instance.

```php
$scf_manager = $plugin->get_scf_manager();
```

#### `get_rules_engine(): RulesEngine`

Get the Rules Engine instance.

```php
$rules_engine = $plugin->get_rules_engine();
```

#### `activate_for_site( int|null $site_id ): void`

Activate plugin for a specific site. Called automatically on activation.

---

### SecureCustomFieldManager

Manages SCF options for the current site.

**Location:** `src/class-secure-custom-field-manager.php`

**Methods:**

#### `load_options(): void`

Load SCF options from the database. Called automatically on `init` hook.

#### `get_options(): array`

Get all SCF options.

```php
$options = $scf_manager->get_options();
```

**Returns:** Array of all SCF options

#### `get_option( string $key, mixed $default = null ): mixed`

Get a specific SCF option.

```php
$value = $scf_manager->get_option('field_name', 'default_value');
```

**Parameters:**
- `$key` - Option key
- `$default` - Default value if option doesn't exist

**Returns:** Option value or default

#### `set_options( array $options ): bool`

Set all SCF options.

```php
$scf_manager->set_options([
    'field_1' => 'value_1',
    'field_2' => 'value_2'
]);
```

**Parameters:**
- `$options` - Array of options to set

**Returns:** True on success, false on failure

#### `is_enabled(): bool`

Check if plugin is enabled for the current site.

```php
if ( $scf_manager->is_enabled() ) {
    // Plugin is enabled
}
```

**Returns:** True if enabled, false otherwise

#### `get_plugin_options(): array`

Get all plugin options including SCF options and rules.

```php
$all_options = $scf_manager->get_plugin_options();
```

**Returns:** Array of plugin options

#### `update_plugin_options( array $options ): bool`

Update plugin options.

```php
$scf_manager->update_plugin_options([
    'enabled' => true,
    'scf_options' => ['key' => 'value']
]);
```

**Parameters:**
- `$options` - Options to update

**Returns:** True on success, false on failure

---

### RulesEngine

Enforces runtime rules based on SCF options.

**Location:** `src/class-rules-engine.php`

**Methods:**

#### `enforce_rules(): void`

Enforce all configured rules. Called automatically on `init` hook.

```php
$rules_engine->enforce_rules();
```

#### `get_rules(): array`

Get all configured rules.

```php
$rules = $rules_engine->get_rules();
```

**Returns:** Array of rules

---

### AdminManager

Manages the admin interface for subsite-specific settings.

**Location:** `src/class-admin-manager.php`

**Methods:**

#### `add_admin_menu(): void`

Add admin menu page. Called automatically on `admin_menu` hook.

#### `register_settings(): void`

Register plugin settings. Called automatically on `admin_init` hook.

#### `render_settings_page(): void`

Render the settings page.

#### `sanitize_options( array $input ): array`

Sanitize and validate settings input.

---

## Filters

### `spx_boson_scf_options`

Modify SCF options after they're loaded from the database.

**Parameters:**
- `$options` (array) - SCF options

**Example:**

```php
add_filter('spx_boson_scf_options', function($options) {
    // Add a custom option
    $options['custom_field'] = 'custom_value';
    
    // Modify existing option
    if (isset($options['existing_field'])) {
        $options['existing_field'] = strtoupper($options['existing_field']);
    }
    
    return $options;
});
```

---

### `spx_boson_rules`

Modify rules before they're enforced.

**Parameters:**
- `$rules` (array) - Array of rules

**Example:**

```php
add_filter('spx_boson_rules', function($rules) {
    // Add a custom rule
    $rules[] = [
        'type' => 'custom_type',
        'enabled' => true,
        'config' => ['key' => 'value']
    ];
    
    return $rules;
});
```

---

### `spx_boson_handle_rule`

Handle custom rule types.

**Parameters:**
- `$handled` (bool) - Whether the rule was handled
- `$rule` (array) - The rule configuration

**Example:**

```php
add_filter('spx_boson_handle_rule', function($handled, $rule) {
    if ($rule['type'] === 'my_custom_rule') {
        // Handle your custom rule logic
        if ($rule['enabled']) {
            // Do something
        }
        return true; // Mark as handled
    }
    return $handled;
}, 10, 2);
```

---

## Actions

### `spx_boson_options_loaded`

Fired after SCF options are loaded.

**Parameters:**
- `$options` (array) - Loaded SCF options

**Example:**

```php
add_action('spx_boson_options_loaded', function($options) {
    // React to options being loaded
    error_log('SCF options loaded: ' . print_r($options, true));
});
```

---

### `spx_boson_rules_enforced`

Fired after rules are enforced.

**Parameters:**
- `$rules` (array) - Enforced rules

**Example:**

```php
add_action('spx_boson_rules_enforced', function($rules) {
    // React to rules being enforced
    $count = count($rules);
    error_log("Enforced {$count} rules");
});
```

---

### `spx_boson_access_control_rule`

Fired when an access control rule is applied.

**Parameters:**
- `$rule` (array) - The rule configuration

**Example:**

```php
add_action('spx_boson_access_control_rule', function($rule) {
    // Custom access control logic
    if ($rule['condition'] === 'user_role') {
        // Check user role
    }
});
```

---

### `spx_boson_field_validation_rule`

Fired when a field validation rule is applied.

**Parameters:**
- `$rule` (array) - The rule configuration

**Example:**

```php
add_action('spx_boson_field_validation_rule', function($rule) {
    // Custom validation logic
});
```

---

### `spx_boson_content_restriction_rule`

Fired when a content restriction rule is applied.

**Parameters:**
- `$rule` (array) - The rule configuration

**Example:**

```php
add_action('spx_boson_content_restriction_rule', function($rule) {
    // Custom content restriction logic
});
```

---

### `spx_boson_unknown_rule_type`

Fired when an unknown rule type is encountered.

**Parameters:**
- `$rule_type` (string) - The unknown rule type
- `$rule` (array) - The rule configuration

**Example:**

```php
add_action('spx_boson_unknown_rule_type', function($rule_type, $rule) {
    error_log("Unknown rule type: {$rule_type}");
}, 10, 2);
```

---

## Rule Types

### Access Control Rule

Controls access based on user roles or capabilities.

**Structure:**

```json
{
  "type": "access_control",
  "enabled": true,
  "condition": "user_role",
  "value": "editor"
}
```

**Fields:**
- `type` - Must be "access_control"
- `enabled` - Boolean, whether rule is active
- `condition` - What to check (e.g., "user_role", "user_capability")
- `value` - Expected value

---

### Field Validation Rule

Validates field values against patterns or rules.

**Structure:**

```json
{
  "type": "field_validation",
  "enabled": true,
  "field": "email",
  "pattern": "^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}$"
}
```

**Fields:**
- `type` - Must be "field_validation"
- `enabled` - Boolean, whether rule is active
- `field` - Field name to validate
- `pattern` - Regex pattern for validation

---

### Content Restriction Rule

Restricts content based on rules.

**Structure:**

```json
{
  "type": "content_restriction",
  "enabled": true,
  "content_type": "post",
  "restriction": "role_based"
}
```

**Fields:**
- `type` - Must be "content_restriction"
- `enabled` - Boolean, whether rule is active
- `content_type` - Type of content to restrict
- `restriction` - Restriction method

---

## Constants

### `SPX_BOSON_VERSION`

Plugin version string.

```php
echo SPX_BOSON_VERSION; // "1.0.0"
```

### `SPX_BOSON_PLUGIN_FILE`

Full path to main plugin file.

```php
echo SPX_BOSON_PLUGIN_FILE;
```

### `SPX_BOSON_PLUGIN_DIR`

Plugin directory path with trailing slash.

```php
require_once SPX_BOSON_PLUGIN_DIR . 'includes/file.php';
```

### `SPX_BOSON_PLUGIN_URL`

Plugin URL with trailing slash.

```php
echo SPX_BOSON_PLUGIN_URL . 'assets/style.css';
```

### `SPX_BOSON_PLUGIN_BASENAME`

Plugin basename (directory/file.php).

```php
echo SPX_BOSON_PLUGIN_BASENAME;
```

---

## Examples

### Complete Integration Example

```php
<?php
/**
 * Custom integration with Sparxstar Access Manager
 */

// Modify SCF options
add_filter('spx_boson_scf_options', function($options) {
    $options['custom_integration'] = [
        'enabled' => true,
        'api_key' => get_option('my_api_key')
    ];
    return $options;
});

// Add custom rules
add_filter('spx_boson_rules', function($rules) {
    $rules[] = [
        'type' => 'api_access',
        'enabled' => true,
        'endpoint' => '/api/v1/protected'
    ];
    return $rules;
});

// Handle custom rule type
add_filter('spx_boson_handle_rule', function($handled, $rule) {
    if ($rule['type'] === 'api_access' && $rule['enabled']) {
        // Implement API access control
        add_filter('rest_pre_dispatch', function($result, $server, $request) use ($rule) {
            $route = $request->get_route();
            if (strpos($route, $rule['endpoint']) === 0) {
                // Check API access
                if (!current_user_can('manage_options')) {
                    return new WP_Error(
                        'rest_forbidden',
                        __('Access denied'),
                        ['status' => 403]
                    );
                }
            }
            return $result;
        }, 10, 3);
        return true;
    }
    return $handled;
}, 10, 2);

// React to options loaded
add_action('spx_boson_options_loaded', function($options) {
    if (isset($options['custom_integration'])) {
        // Initialize custom integration
    }
});
```

---

## Multi-Site Considerations

### Per-Site Configuration

Each subsite has its own independent configuration stored in its own options table:

```php
// On site 1
$site_1_options = get_blog_option(1, 'spx_boson_options');

// On site 2
$site_2_options = get_blog_option(2, 'spx_boson_options');

// These are completely independent
```

### Site Switching

When working with multiple sites programmatically:

```php
// Save current site
$original_site = get_current_blog_id();

// Switch to site 2
switch_to_blog(2);

// Get site 2's SCF manager options
$plugin = \Starisian\Sparxstar\BosonScaffold\Plugin::get_instance();
$scf_manager = $plugin->get_scf_manager();
$site_2_options = $scf_manager->get_options();

// Restore original site
restore_current_blog();
```

---

## Error Handling

The plugin includes built-in error handling:

```php
// JSON validation errors are added as settings errors
add_action('admin_notices', function() {
    settings_errors('spx_boson_options');
});
```

---

## Testing

### Unit Testing with Filters

```php
public function test_custom_scf_filter() {
    add_filter('spx_boson_scf_options', function($options) {
        $options['test'] = 'value';
        return $options;
    });
    
    $scf_manager = new SecureCustomFieldManager();
    $scf_manager->load_options();
    
    $this->assertEquals('value', $scf_manager->get_option('test'));
}
```
