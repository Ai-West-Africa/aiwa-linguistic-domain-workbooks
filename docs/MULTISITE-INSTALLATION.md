# Multi-Site Installation Guide

This guide provides detailed instructions for installing SPARXSTAR Boson Scaffold on a WordPress multi-site network.

## Prerequisites

- WordPress Multi-Site network installed and configured
- Access to server files and wp-content directory
- Composer installed (for Composer method)
- PHP 8.2 or higher

## Installation Methods

### Method 1: Composer Installation (Recommended for WordPress Composer Projects)

If you're using WordPress with Composer (e.g., Bedrock or similar):

#### Step 1: Add Repository to composer.json

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/Starisian-Technologies/sparxstar-boson-scaffold"
    }
  ],
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

#### Step 2: Install via Composer

```bash
composer install
```

#### Step 3: Create MU-Plugin Loader

Create file `wp-content/mu-plugins/sparxstar-boson-loader.php`:

```php
<?php
/**
 * Plugin Name: SPARXSTAR Boson Scaffold Loader
 * Description: Loads the SPARXSTAR Boson Scaffold MU-plugin
 */
require_once WPMU_PLUGIN_DIR . '/sparxstar-boson-scaffold/sparxstar-access-manager.php';
```

### Method 2: Manual Installation

#### Step 1: Download Plugin

Clone or download the plugin:

```bash
cd wp-content/mu-plugins
git clone https://github.com/Starisian-Technologies/sparxstar-boson-scaffold.git
```

Or download and extract the ZIP file to `wp-content/mu-plugins/sparxstar-boson-scaffold/`

#### Step 2: Install Dependencies

```bash
cd sparxstar-boson-scaffold
composer install --no-dev
```

#### Step 3: Create MU-Plugin Loader

Create file `wp-content/mu-plugins/sparxstar-boson-loader.php`:

```php
<?php
/**
 * Plugin Name: SPARXSTAR Boson Scaffold Loader
 * Description: Loads the SPARXSTAR Boson Scaffold MU-plugin
 */
require_once WPMU_PLUGIN_DIR . '/sparxstar-boson-scaffold/sparxstar-access-manager.php';
```

### Method 3: Regular Plugin Installation (Network Activated)

While designed as an MU-plugin, you can also install as a regular plugin:

#### Step 1: Install Plugin

1. Download the plugin ZIP
2. Go to Network Admin → Plugins → Add New
3. Upload the ZIP file
4. Network Activate the plugin

Note: Each subsite admin can disable it via their settings page if needed.

## Verification

After installation, verify the plugin is working:

1. Go to any subsite admin dashboard
2. Navigate to Settings → Boson Scaffold
3. You should see the plugin settings page
4. Each subsite will have its own independent configuration

## Network Admin vs Subsite Admin

Important distinction:

- **Network Admin**: The plugin menu does NOT appear here (by design)
- **Subsite Admin**: Settings → Boson Scaffold available on each subsite
- Each subsite has completely independent settings
- No network-level configuration exists

## Configuration for Each Subsite

### Initial Setup per Subsite

1. Log into a subsite admin dashboard
2. Go to Settings → Boson Scaffold
3. Configure:
   - Enable/disable the plugin for this subsite
   - Set SCF options (JSON format)
   - Define runtime rules (JSON format)
4. Save changes

### Example Configuration

**SCF Options:**
```json
{
  "secure_field_1": "value1",
  "access_control": {
    "enabled": true,
    "level": "editor"
  }
}
```

**Runtime Rules:**
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

## Automatic Subsite Initialization

When a new subsite is created in your network:

1. The plugin automatically detects the new site
2. Default settings are initialized
3. The admin can then customize settings for that subsite

## Updating

### Composer Method

```bash
composer update starisian-technologies/sparxstar-boson-scaffold
```

### Manual Method

1. Backup current installation
2. Download new version
3. Replace files in `wp-content/mu-plugins/sparxstar-boson-scaffold/`
4. Run `composer install --no-dev` if dependencies changed

## Troubleshooting

### Plugin Not Showing in Subsite Admin

- Verify the loader file exists: `wp-content/mu-plugins/sparxstar-boson-loader.php`
- Check file permissions (files should be readable by web server)
- Check error logs for any PHP errors

### Settings Not Saving

- Verify the subsite admin has 'manage_options' capability
- Check for JavaScript errors in browser console
- Verify database permissions

### Different Subsites Sharing Settings

- This should NOT happen - each subsite has independent settings
- Verify you're not on a network admin page
- Check that site switching is working correctly

## Uninstallation

To remove the plugin:

### If Using Composer

```bash
composer remove starisian-technologies/sparxstar-boson-scaffold
```

Then delete the loader file.

### If Manually Installed

1. Delete `wp-content/mu-plugins/sparxstar-boson-loader.php`
2. Delete `wp-content/mu-plugins/sparxstar-boson-scaffold/` directory

Note: Settings for each subsite remain in the database. To remove them, you would need to delete the `spx_boson_options` option from each subsite's options table.

## Security Considerations

- Settings are stored per-subsite in WordPress options
- All inputs are sanitized and validated
- Only users with 'manage_options' capability can change settings
- No network-level settings mean super admins don't have centralized control
- Each subsite administrator controls their own configuration

## Performance

- Plugin loads early in WordPress initialization
- SCF options are loaded once per request
- Rules are evaluated on each page load if enabled
- Minimal database queries (uses WordPress options API with caching)

## Integration with Other Plugins

The plugin provides hooks for integration:

```php
// Modify SCF options before use
add_filter('spx_boson_scf_options', 'my_custom_scf_filter');

// Handle custom rule types
add_filter('spx_boson_handle_rule', 'my_custom_rule_handler', 10, 2);

// React to options being loaded
add_action('spx_boson_options_loaded', 'my_custom_action');
```

## Support

For issues or questions:
- GitHub Issues: https://github.com/Starisian-Technologies/sparxstar-boson-scaffold/issues
- Documentation: https://github.com/Starisian-Technologies/sparxstar-boson-scaffold
