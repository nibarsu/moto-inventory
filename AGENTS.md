# Project Overview

This project uses:

* Laravel 12
* PHP 8.3
* MySQL 8
* Laravel Breeze
* Bootstrap 5
* Git
* GitHub
* Laragon

Currently completed modules:

* Brand
* Category
* Warehouse
* Supplier
* Customer
* Part
* Vehicle

# Encoding Rules

Important rules:

* Preserve original file encoding
* Preserve BOM settings
* Preserve line endings
* New files must use UTF-8
* If Chinese text appears garbled:
* DO NOT SAVE FILE
* DO NOT OVERWRITE FILE
* Report issue first

PowerShell output may display Chinese incorrectly.

Always verify actual file contents before saving.

# Business Rules

* Parts and Vehicles must remain separate tables.
* Category.type:
* part
* vehicle
* Barcode may be same as part_no or model_code.
* Multi-warehouse supported.
* Users can create warehouses manually.
* Actual purchase amount must be preserved.
* Do not force cost rounding.

# Development Rules

Every module must include:

1. Migration
2. Model
3. Request
4. Controller
5. Route
6. Blade View
7. Navigation

# Validation Rules

Use:

* StoreXXXRequest
* UpdateXXXRequest

Avoid validation inside Controller.

# Testing Rules

Before module completion:

Run:

`php artisan migrate`

Run:

`php artisan route:list`

Both must pass.

# Git Rules

When module is completed:

`git add .`
`git commit`
`git push`

Report:

* Commit Hash
* Modified Files
* Push Result

# Documentation Rules

When adding a new module:

Update:

* docs/PROJECT_OVERVIEW.md
* docs/DATABASE_DESIGN.md
* docs/BUSINESS_RULES.md
* docs/ROADMAP.md

if applicable.

# Roadmap Rules

Follow `docs/ROADMAP.md`.

Do not skip roadmap order unless explicitly instructed.

# UI Rules

Use Laravel Breeze Layout.

Keep navigation consistent.

# AI Agent Instructions

Before generating code:

1. Read AGENTS.md
2. Read docs/BUSINESS_RULES.md
3. Read docs/ROADMAP.md
4. Reuse existing coding style
5. Reuse existing validation pattern
6. Do not introduce new frameworks
