# Quick Start Guide

## Installation (Multi-Site)

### For Composer Projects:

1. Add to `composer.json`:
```json
{
  "require": {
    "starisian-technologies/sparxstar-boson-scaffold": "^1.0"
  },
  "extra": {
    "installer-paths": {
      "wp-content/mu-plugins/{$name}/": ["type:wordpress-muplugin"]
    }
  }
}
```

2. Run: `composer install`

3. Create loader: `wp-content/mu-plugins/sparxstar-boson-loader.php`:
```php
<?php
require_once WPMU_PLUGIN_DIR . '/sparxstar-boson-scaffold/sparxstar-access-manager.php';
```

### Manual Installation:

1. Clone to: `wp-content/mu-plugins/sparxstar-boson-scaffold/`
2. Run: `composer install --no-dev`
3. Create loader file (same as above)

## Configuration

### Per Subsite:

1. Log into subsite admin
2. Go to: **Settings → Boson Scaffold**
3. Configure:
   - Enable/disable plugin
   - Set SCF options (JSON)
   - Define rules (JSON)

### Example SCF Options:
```json
{
  "secure_field": "value",
  "access_level": "editor"
}
```

### Example Rules:
```json
[
  {
    "type": "access_control",
    "enabled": true,
    "condition": "user_role",
    "value": "editor"
  }
]
```

## Usage

### Get Plugin Instance:
```php
$plugin = \Starisian\Sparxstar\BosonScaffold\Plugin::get_instance();
```

### Get SCF Options:
```php
$scf_manager = $plugin->get_scf_manager();
$options = $scf_manager->get_options();
$value = $scf_manager->get_option('key', 'default');
```

### Check if Enabled:
```php
if ( $scf_manager->is_enabled() ) {
    // Plugin is active for this subsite
}
```

## Customization

### Add Custom SCF Options:
```php
add_filter('spx_boson_scf_options', function($options) {
    $options['custom'] = 'value';
    return $options;
});
```

### Add Custom Rules:
```php
add_filter('spx_boson_rules', function($rules) {
    $rules[] = ['type' => 'custom', 'enabled' => true];
    return $rules;
});
```

### Handle Custom Rules:
```php
add_filter('spx_boson_handle_rule', function($handled, $rule) {
    if ($rule['type'] === 'custom') {
        // Your logic here
        return true;
    }
    return $handled;
}, 10, 2);
```

## Development

### Run Tests:
```bash
composer test
```

### Run Linter:
```bash
composer lint
```

### Fix Code Style:
```bash
composer lint:fix
```

### Using Make:
```bash
make install    # Install dependencies
make test       # Run tests
make lint       # Run linter
```

## Key Features

✅ Multi-site with subsite-specific settings  
✅ No network-level configuration  
✅ Composer ready (wordpress-muplugin type)  
✅ Auto-initialization of new subsites  
✅ Extensible via filters and actions  
✅ Comprehensive test coverage  
✅ WordPress coding standards compliant  

## Important Notes

- **Settings are per-subsite** - Each subsite has independent configuration
- **Network Admin** - Plugin settings do NOT appear in network admin
- **Subsite Admin** - Settings → Boson Scaffold available on each subsite
- **Auto-activation** - New subsites automatically get default settings

## Support

- Docs: [README.md](../README.md)
- API: [docs/API.md](API.md)
- Installation: [docs/MULTISITE-INSTALLATION.md](MULTISITE-INSTALLATION.md)
- Examples: [examples/](../examples/)
