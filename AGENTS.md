# AGENTS.md

## Cursor Cloud specific instructions

This is a **Laravel Composer package** (`callcocam/laravel-raptor`), not a standalone application. It provides multi-tenant CRUD admin panel features (Landlord, Shinobi RBAC, navigation, form/table builders).

### System Requirements

- **PHP 8.4+** with extensions: bcmath, curl, dom, exif, gd, intl, mbstring, pdo_sqlite, soap, xml, zip
- **Composer** for dependency management
- **SQLite** (in-memory) used for testing via Orchestra Testbench — no external database needed

### Key Commands

See `composer.json` `scripts` section for all available commands. Summary:

| Task | Command |
|------|---------|
| Install deps | `composer install` |
| Run tests | `composer test` (runs `vendor/bin/pest`) |
| Code style check | `vendor/bin/pint --test` |
| Fix code style | `composer format` (runs `vendor/bin/pint`) |
| Build workbench | `composer run build` |
| Dev server | `composer run start` (or `php vendor/bin/testbench serve --host=0.0.0.0 --port=8000`) |

### Known Issues

- **Duplicate class error**: `EditableColumn` is declared in both `src/Support/Table/Columns/Types/Editable/EditableColumn.php` and `src/Support/Table/Columns/Types/EditableColumn.php`. This causes the ArchTest to fail with a fatal error. The ExampleTest passes fine.
- **Pint style issues**: There are 25 pre-existing code style violations detected by `vendor/bin/pint --test`.

### Gotchas

- The `composer.json` originally had dev dependency version constraints incompatible with its `illuminate/contracts ^12.0` target (Pest v2 + Testbench v8/v9 only support Laravel 10/11). The dev deps were updated to Pest v3 + Testbench v10 to resolve the conflict.
- The dev server (`composer run start`) uses Orchestra Testbench's built-in PHP server, which serves a minimal Laravel app with the package loaded. No external web server (Apache/Nginx) is needed.
