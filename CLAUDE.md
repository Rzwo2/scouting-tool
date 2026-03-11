# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

A volleyball scouting web application built with **Symfony 7.4** (PHP 8.2+), **MariaDB**, and **FrankenPHP**. It manages teams, players, games, and player statistics with an EasyAdmin-based admin panel.

## Development Environment

The project runs entirely in Docker. All PHP commands must be run inside the container.

```bash
# Start dev containers (waits for healthy state)
make up.dev

# Stop containers
make down

# Shell access
make bash.php        # PHP container
make bash.database   # MariaDB CLI

# Logs
make logs.docker.php
make logs.docker.database
```

The `.env` file contains defaults; **actual credentials go in `.env.local`** (not committed). Required `.env.local` variables: `APP_SECRET`, `MARIADB_ROOT_PASSWORD`, `MARIADB_USER`, `MARIADB_PASSWORD`, `SUPERADMIN_EMAIL`, `SUPERADMIN_PASSWORD`, `ZIH_LOGIN`, `ZIH_PASSWORD`, `ZIH_SENDER`, `BALLTIME_TOKEN`.

## Common Commands (run inside PHP container via `make bash.php`)

```bash
# Database migrations
php bin/console doctrine:migrations:migrate
php bin/console doctrine:migrations:generate

# Database initialization (creates superadmin)
php bin/console app:init:database

# Import teams from Balltime API
php bin/console app:import:teams

# PHP code style — uses @Symfony ruleset
vendor/bin/php-cs-fixer fix       # apply fixes
vendor/bin/php-cs-fixer check     # check only

# Run tests
php bin/phpunit
php bin/phpunit tests/Controller/SomeTest.php   # single test file
```

## Frontend (TypeScript)

TypeScript sources live in `assets/ts/`. Compiled output goes to `public/assets/`.

```bash
npm run ts:build    # one-time compile
npm run ts:watch    # watch mode
```

The project uses **Symfony Asset Mapper** (no Webpack). JS imports are configured in `importmap.php`. Biome is available for JS/TS linting (`npx biome check`).

**Symfony UX** is used for interactivity:
- **Stimulus** (`assets/controllers/`) — JS controllers attached to HTML via `data-controller` attributes.
- **Live Components** (`src/Twig/`) — PHP-backed Twig components that re-render server-side on user interaction. Annotate with `#[AsLiveComponent]` and use `#[LiveProp]` / `#[LiveAction]`.

## Architecture

### Layered Structure

```
HTTP Request → Controller → Domain Service → Repository → Doctrine Entity → MariaDB
                         ↓
                    Twig Template
```

- **`src/Controller/`** — HTTP controllers with attribute-based routing. Admin CRUD controllers extend EasyAdmin's `AbstractCrudController`. Statistic-specific controllers live in `src/Controller/Statistic/`.
- **`src/Domain/`** — Business logic: `RegistrationService` (email invitations), `StatisticService` (aggregated stats), `StatisticImportService` (Balltime API), `DataTable/` helpers.
- **`src/Entity/`** — Doctrine ORM entities using PHP attributes. Custom DBAL types in `src/Entity/Type/`.
- **`src/Repository/`** — Doctrine repositories, one per entity.
- **`src/Form/`** — Symfony form types.
- **`src/Security/`** — Authentication and authorization.
- **`templates/`** — Twig templates organized by domain (team/, player/, game/, statistic/, admin/, security/).
- **`translations/`** — i18n files; the UI is primarily in German.

### Key Entities and Relationships

```
Team ──< Player
Team ──< Game (as home or away team)
Game ──< GameSet
Game ──< PlayerGameStatistic >── Player
User ──< RegistrationInvitation
```

`PlayerGameStatistic` stores per-player, per-game statistics (attacks, serves, receives, blocks, digs, etc.).

### Security / Roles

Role hierarchy: `ROLE_USER` → `ROLE_ADMIN` → `ROLE_SUPER_ADMIN`. Users are created only via email invitations (`RegistrationInvitation` entity + token-based `/register/{token}` route). The superadmin is seeded by `app:init:database`.

### Admin Panel

EasyAdmin handles all CRUD for Teams, Players, Games, PlayerGameStatistics, Users, and RegistrationInvitations. Admin controllers live in `src/Controller/Admin/`.

### Async Queue

Symfony Messenger uses a Doctrine transport (`messenger_messages` table). In production, Supervisor runs the worker process (configured in `frankenphp/supervisor.d/`).

## Code Style

Follow the `@Symfony` PHP-CS-Fixer ruleset (see `.php-cs-fixer.dist.php`). Notable rules: ordered class elements (constants → properties → constructor → magic → methods), no useless `else`/`return`, `return_assignment`, single space around `.` concatenation.
