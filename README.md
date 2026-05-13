![boson](https://github.com/user-attachments/assets/712dda46-101e-46ca-af97-908d5f548b3b)
# SPARXSTAR™ Boson Scaffold

> **This is a named scaffold.** It is designed to be renamed and extended when building your own WordPress Multisite plugin. See [Renaming This Scaffold](#renaming-this-scaffold) before writing any production code.

A WordPress Multisite MU-plugin scaffold that loads Secure Custom Field (SCF) options and enforces runtime rules, with full per-subsite network support.

Built to the [SPARXSTAR Engineering Standards v2](docs/ENGINEERING-STANDARDS.md) — Multisite-first, PHP 8.2+, WordPress 6.8+, offline-ready.

[![Copilot code review](https://github.com/Starisian-Technologies/sparxstar-boson-scaffold/actions/workflows/copilot-pull-request-reviewer/copilot-pull-request-reviewer/badge.svg)](https://github.com/Starisian-Technologies/sparxstar-boson-scaffold/actions/workflows/copilot-pull-request-reviewer/copilot-pull-request-reviewer)  [![Copilot coding agent](https://github.com/Starisian-Technologies/sparxstar-boson-scaffold/actions/workflows/copilot-swe-agent/copilot/badge.svg)](https://github.com/Starisian-Technologies/sparxstar-boson-scaffold/actions/workflows/copilot-swe-agent/copilot)

---

## Renaming This Scaffold

**Before writing a single line of your own logic**, rename the following identifiers throughout the codebase:

| What to change | Current value | Replace with |
|---|---|---|
| Plugin Name (header) | `SPARXSTAR Boson Scaffold` | `My Product Name` |
| PHP Namespace | `Starisian\Sparxstar\BosonScaffold` | `Vendor\MyProduct\{Module}` |
| PHP/WP function prefix | `spx_boson_` | `mypfx_` |
| Constants prefix | `SPX_BOSON_` | `MYPFX_` |
| WordPress option key | `spx_boson_options` | `mypfx_options` |
| Hook names | `spx_boson_*` | `mypfx_*` |
| Text domain | `sparxstar-boson` | `my-product` |
| Composer package name | `starisian-technologies/sparxstar-boson-scaffold` | `vendor/my-product` |
| `phpcs.xml` prefixes | `spx_boson`, `SPX_BOSON`, `Starisian\Sparxstar\BosonScaffold` | your own |

Use your editor's project-wide find-and-replace, then run `composer install` and `make lint` to verify.

---

## Features

- **Multisite-First Architecture** — Designed for WordPress Multisite from line one; never retrofitted
- **Per-Subsite Settings** — Each subsite has independent configuration (no network-level settings)
- **Secure Custom Field Integration** — Load and manage SCF options per subsite
- **Runtime Rules Engine** — Enforce access control and validation rules dynamically
- **Frontend Access Control** — ACF-based query, REST, AJAX, template, and admin enforcement
- **Composer-Ready** — Install via `wordpress-muplugin` type
- **Extensible** — Full filter and action hook API

---

## Requirements

- **PHP 8.2+** (strict types required)
- **WordPress 6.8+**
- **WordPress Multisite** (recommended; works on single-site for development)
- **Composer** for dependency management
- **Node.js 20+** for JS/CSS/Markdown linting

---

## Installation

### Method 1: Composer (Recommended)

Add to your project's `composer.json`:

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

Then run:

```bash
composer install
```

Create a loader file at `wp-content/mu-plugins/sparxstar-boson-loader.php`:

```php
<?php
// See examples/sparxstar-access-manager-loader.php
require_once WPMU_PLUGIN_DIR . '/sparxstar-boson-scaffold/sparxstar-access-manager.php';
```

### Method 2: Manual

1. Clone to `wp-content/mu-plugins/sparxstar-boson-scaffold/`
2. Run `composer install --no-dev`
3. Create the loader file as shown above

### Multisite Network Activation

1. The plugin loads automatically via the MU-plugins system
2. Each subsite gets its own settings page under **Settings → Boson Scaffold**
3. New subsites are automatically initialized with default settings

---

## Configuration

### Admin Interface

Each subsite has its own settings page at **Settings → Boson Scaffold**.

#### General Settings

Enable or disable the scaffold for the current subsite.

#### Secure Custom Field Options

Configure SCF options in JSON format:

```json
{
  "field_name": "custom_field_key",
  "access_level": "editor",
  "validation_rule": "email"
}
```

#### Runtime Rules

Define enforcement rules in JSON format:

```json
[
  {
    "type": "access_control",
    "enabled": true,
    "condition": "user_role",
    "value": "editor"
  },
  {
    "type": "field_validation",
    "enabled": true,
    "field": "email",
    "pattern": "^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}$"
  }
]
```

### Programmatic Configuration

#### Filters

**Modify SCF Options:**
```php
add_filter( 'spx_boson_scf_options', function( array $options ): array {
    $options['custom_key'] = 'custom_value';
    return $options;
} );
```

**Modify Rules:**
```php
add_filter( 'spx_boson_rules', function( array $rules ): array {
    $rules[] = [
        'type'    => 'custom_rule',
        'enabled' => true,
        'config'  => ['key' => 'value'],
    ];
    return $rules;
} );
```

**Handle Custom Rule Types:**
```php
add_filter( 'spx_boson_handle_rule', function( bool $handled, array $rule ): bool {
    if ( $rule['type'] === 'my_custom_type' ) {
        // Handle your custom rule
        return true;
    }
    return $handled;
}, 10, 2 );
```

#### Actions

**After Options Loaded:**
```php
add_action( 'spx_boson_options_loaded', function( array $options ): void {
    // React to SCF options being loaded
} );
```

**After Rules Enforced:**
```php
add_action( 'spx_boson_rules_enforced', function( array $rules ): void {
    // React to rules being enforced
} );
```

---

## Development

### Setup

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### Linting

```bash
# Run all linters (PHPCS, PHPStan, ESLint, Stylelint, markdownlint, JSON)
make lint

# PHP only
make lint-php

# PHPStan static analysis (Level 5)
make lint-phpstan

# JavaScript
make lint-js

# CSS / SCSS
make lint-css

# Markdown
make lint-md

# JSON
make lint-json
```

> **Note:** `make lint` runs in **report mode only**. Auto-fix (`make lint-fix`) is never run in CI.

### Testing

```bash
# Run all tests
make test

# Run with HTML coverage
make test-coverage

# Run a specific test suite
vendor/bin/phpunit tests/unit
vendor/bin/phpunit tests/integration
```

---

## Architecture

### Core Components

| File | Class | Responsibility |
|---|---|---|
| `src/class-plugin.php` | `Plugin` | Singleton orchestrator |
| `src/class-secure-custom-field-manager.php` | `SecureCustomFieldManager` | Per-subsite SCF options |
| `src/class-rules-engine.php` | `RulesEngine` | Runtime rule enforcement |
| `src/class-admin-manager.php` | `AdminManager` | Subsite settings UI |
| `src/sparxstar-access-manager.php` | `FrontendAccess` | ACF-based access control |

### Multisite Architecture

- Each subsite stores its own configuration in its own options table
- No network-level settings or shared configurations
- New subsites are automatically initialized on creation
- Can be installed as a regular plugin (per-subsite) or as an MU-plugin (always active)

### Namespace

```
Starisian\Sparxstar\BosonScaffold\
```

> Rename this to `Vendor\YourProduct\{Module}` before shipping.

---

## Security

- All admin inputs follow **Sanitize → Validate → Escape** order
- JSON configurations are validated before saving
- WordPress nonces and capability checks on all admin actions
- PHP 8.2+ strict types throughout
- PHPStan Level 5 static analysis
- WordPress VIP coding standards enforced

---

## API Reference

See [docs/API.md](docs/API.md) for the full hook and class reference.

---

## Support

For issues, questions, or contributions:
https://github.com/Starisian-Technologies/sparxstar-boson-scaffold

---

## License

MIT License — see [LICENSE](LICENSE)

---

## Credits

Developed by [Starisian Technologies](https://starisian.tech)
Copyright © 2026 Starisian Technologies.

SPARXSTAR™ and Starisian Technologies™ are trademarks of Starisian Technologies. WordPress is a trademark of Automattic Inc. Starisian Technologies is not affiliated with or endorsed by Automattic Inc.
