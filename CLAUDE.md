# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

WordPress site for **cip.org.uy** (CIP - Centro de Informática del Paraguay), hosted on Hostinger and running locally via Laragon. The site is in Spanish (es_ES locale).

- **WordPress version**: 6.x (core files present)
- **PHP**: Requires 5.3+ (production runs 7.2.34)
- **Database**: MariaDB, database name `u449780709_8LoRF`, table prefix `wp_`
- **SQL dump**: `u449780709_8LoRF.sql` at project root (production backup)

## Local Development Environment

- **Server**: Laragon on Windows, accessible at `http://cip-wordpress.test` (Laragon auto-vhost)
- **Document root**: `C:\laragon\www\cip-wordpress`
- **wp-config.php**: Points to `127.0.0.1` with production credentials — update `DB_NAME`, `DB_USER`, `DB_PASSWORD` for local use
- **WP_DEBUG**: Set to `false` by default; set to `true` for development
- **Caching**: WP_CACHE is enabled, LiteSpeed Cache plugin active — disable during development to avoid stale content

### Database Setup

```bash
# Import the production SQL dump into a local database
mysql -u root -p < u449780709_8LoRF.sql
# Then update wp-config.php with local DB credentials
# Run search-replace for domain: production is https://cip.org.uy
wp search-replace 'https://cip.org.uy' 'http://cip-wordpress.test' --all-tables
```

## Theme Architecture

- **Active theme**: Astra v4.12.3 (parent theme, no child theme)
- **Theme path**: `wp-content/themes/astra/`
- **Customizations should use a child theme** — the Astra parent theme will be overwritten on updates. If creating a child theme, place it at `wp-content/themes/astra-child/`.

Key Astra files:
- `functions.php` — Bootstraps all theme components via requires from `inc/`
- `inc/core/` — Theme options, enqueue scripts, admin helpers
- `inc/customizer/` — WordPress Customizer integration
- `inc/dynamic-css/` — Generates dynamic CSS based on theme settings
- `inc/compatibility/` — Integration with WooCommerce, Elementor, page builders, etc.
- `inc/builder/` — Header/footer builder system
- `theme.json` — Block editor theme configuration

## Active Plugins

| Plugin | Purpose |
|--------|---------|
| **Ultimate Addons for Gutenberg (Spectra)** | Block editor extensions with custom blocks |
| **SureForms** | Form builder |
| **SureMails** | Email management |
| **SureRank** | SEO tool |
| **LiteSpeed Cache** | Performance caching |
| **WP Live Chat Support** | Live chat widget |
| **Astra Sites (Starter Templates)** | Template import (was used for initial setup) |
| Hostinger / Hostinger Easy Onboarding / Hostinger Reach | Hosting platform plugins |

## Key Directories

- `wp-content/uploads/` — Media library (organized by year/month)
- `wp-content/mu-plugins/` — Must-use plugins (Hostinger auto-updates, preview domain)
- `.private/config.json` — Hostinger update/API configuration
- `.htaccess` — LiteSpeed cache rules + WordPress permalinks

## WP-CLI Commands

```bash
# Check WordPress status
wp core version
wp plugin list --status=active
wp theme list --status=active

# Clear all caches
wp litespeed-purge all
wp cache flush

# Export database
wp db export backup.sql

# Search-replace (e.g., domain migration)
wp search-replace 'old-domain.com' 'new-domain.com' --all-tables --dry-run
```

## Important Notes

- **No child theme exists** — any theme customizations are either in the Customizer (stored in DB) or via Spectra blocks. Avoid directly editing Astra parent theme files.
- **No version control initialized** — this is a raw WordPress install exported from Hostinger. Consider initializing git and adding a `.gitignore` for `wp-content/uploads/`, `node_modules/`, and the SQL dump.
- **Production URL**: `https://cip.org.uy` — all absolute URLs in the database reference this domain.
- **Block editor focused** — the site uses Gutenberg + Spectra blocks, not a traditional page builder like Elementor.
