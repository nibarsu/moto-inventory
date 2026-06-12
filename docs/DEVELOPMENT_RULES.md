# Development Rules

## Core Conventions

- Use Laravel Breeze layout for internal pages.
- Keep admin routes in [routes/web.php](/c:/laragon/www/moto-inventory/routes/web.php) under `Route::middleware('auth')`.
- Follow the existing CRUD structure:
  - controller
  - request validation classes
  - model
  - resource-like Blade views under `resources/views/<module>`

## Validation Pattern

Use dedicated form requests for create and update actions.

Typical conventions already used:

- `StoreXxxRequest`
- `UpdateXxxRequest`
- `prepareForValidation()` to normalize checkbox boolean values
- explicit uniqueness rules
- required name/code fields
- numeric minimum checks for prices and stock counts

## Blade Pattern

Each CRUD module should keep the same Blade layout:

- `index.blade.php`
- `create.blade.php`
- `edit.blade.php`
- `show.blade.php`

Expected page behavior:

- `index`: listing table, flash success message, action links
- `create` / `edit`: Breeze form components and validation error output
- `show`: read-only detail page

## Frontend Asset Rule

- If a change touches `resources/js`, `resources/css`, shared layouts, navigation, or introduces new Tailwind utility classes in Blade, run `npm run build` before final verification.
- Do not assume a Blade-only UI fix is visible in the browser until Vite assets are rebuilt.
- If the UI still looks wrong after a code change, confirm whether the browser is using stale built assets before changing the implementation again.

## Route Pattern

Standard modules use:

```php
Route::resource('module-name', ModuleController::class);
```

Non-resource workflow pages use explicit named routes.

Example:

```php
Route::get('stocks', [StockController::class, 'index'])->name('stocks.index');
```

## Model Pattern

- Define `fillable` explicitly.
- Define `casts()` for booleans, integers, and decimals.
- Add relationships only when they are actively used by the module.

Current codebase pattern:

- simple master models keep only fillable + casts
- product and stock models include the active relationships they need

## Query Pattern

- Use ordered listings for predictable admin pages.
- Use `paginate(10)` in standard CRUD index pages unless the page has a different operational purpose.
- Use eager loading when a list view displays related records.

Examples already in use:

- `Part::with(['brand', 'category'])`
- `Vehicle::with(['brand', 'category'])`

## Stock Module Rules

- Stock adjustment must stay transactional.
- Stock balance tables are the source of current stock.
- `stock_movements` is the audit log.
- When adding new stock-affecting modules later, update both:
  - stock balance table
  - stock movement history table

## Documentation Expectations

When a new module changes business structure, update:

- [PROJECT_OVERVIEW.md](/c:/laragon/www/moto-inventory/docs/PROJECT_OVERVIEW.md)
- [DATABASE_DESIGN.md](/c:/laragon/www/moto-inventory/docs/DATABASE_DESIGN.md)
- [BUSINESS_RULES.md](/c:/laragon/www/moto-inventory/docs/BUSINESS_RULES.md)
- [DEVELOPMENT_RULES.md](/c:/laragon/www/moto-inventory/docs/DEVELOPMENT_RULES.md)

## Recommended Module Completion Flow

For consistency with the current team workflow:

1. Add migration, model, controller, request, routes, and views.
2. Run migration.
3. Run `artisan route:list`.
4. Run `npm run build` when frontend assets or Tailwind classes are affected.
5. Verify syntax and Blade compilation if needed.
6. Commit with a module-specific message.
7. Push to remote.

## Known Codebase Notes

- Some older files show encoding damage in comments or output strings when viewed through certain shell/codepage combinations.
- Prefer preserving executable behavior and updating user-facing strings carefully when touching those files.
- The local shell environment may not always expose `php` or `git` directly on `PATH`; verify tooling path if commands unexpectedly fail.
