# Module Console

This package extends every Laravel artisan `make` command with a `--module` flag, so you can generate models, controllers, migrations, and more directly inside any module. It also scaffolds entire module skeletons in one command and lets you publish and customize the stubs to fit your workflow.

### Alternative

There are many alternative packages for creating module skeletons, but they don't give you much flexibility and are auto-discovered. This package gives you full control over your modules.

Here are a few popular packages:
- nwidart/laravel-modules
- internachi/modular
- joshbrw/laravel-module-installer

## Requirements

- PHP 8.2+
- Laravel 11, 12, or 13
- [saeedhosan/module-support](https://github.com/saeedhosan/module-support)

## Installation

Install via Composer:

```bash
composer require saeedhosan/module-console
```

## Suggestions

After creating a module, you need to load it into your application. Here are **two ways** to do it:

**1. Use Composer path repositories (recommended)**

Add this to your root `composer.json` so Composer treats every module as a local package:

```json
"repositories": [
    {
        "type": "path",
        "url": "modules/*",
        "options": {
            "symlink": true
        }
    }
]
```

Then require the module like any Composer package:

```bash
composer require modules/blog
```

Composer will symlink it and auto-discover the service provider — no manual registration needed.

**2. Add the module namespace manually**

Register the module's namespace in your `composer.json`:

```json
"autoload": {
    "psr-4": {
        "Modules\\Blog\\": "modules/blog/app/"
    }
}
```

Then run `composer dump-autoload` and register the service provider in `bootstrap/providers.php`:

```php
Modules\Blog\BlogServiceProvider::class,
```

## Configuration

Want to customize the generated files? Publish the stubs and edit them however you like:

```bash
php artisan vendor:publish --tag=module-stubs
```

Want to customize how modules are organized? Publish the config file:

```bash
php artisan vendor:publish --tag=module-config
```

Here's what you can tweak in `config/module.php`:

```php
[
    'directory' => 'modules',      // Where your modules live
    'namespace' => 'Modules',       // The root namespace for modules
    'lowercase' => true,            // Normalize module names to lowercase
    'vendor' => 'modules',          // Vendor directory for module packages
    'view_path' => 'resources/views',
    'app_path' => 'app',
]
```

## Quick Start

Create your first module:

```bash
php artisan make:module blog
```

This generates a clean module structure based on stubs:

```
modules/blog/
├── app/
│   └── BlogServiceProvider.php
├── config/
│   └── blog.php
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
├── routes/
│   └── web.php
├── tests/
│   └── example.php
├── composer.json
└── README.md
```

Every Laravel `make` command works with modules too. Just pass the `--module` flag:

```bash
php artisan make:model Post --module=blog
php artisan make:enum PostStatus --module=blog
php artisan make:controller PostController --module=blog
php artisan make:migration create_posts_table --module=blog
```

This package supports almost all `make:*` commands to create something inside a module.

```bash
php artisan make:YourMakeCommand YourClassName --module=ModuleName
```

See all your modules:

```bash
php artisan module:list
```

## Testing

```bash
npm run test          # Everything (lint + types + unit)
npm run test:lint     # Code style (Pint)
npm run test:types    # Static analysis (PHPStan)
npm run test:unit     # Unit tests (Pest)
```

## Contributing

Found a bug or want to add a feature? Pull requests are totally welcome. Just make sure the tests pass before submitting.

---

**Module Console** was created by **[Saeed Hosan](https://www.linkedin.com/in/saeedhosan)** under the **[MIT license](https://opensource.org/licenses/MIT)**.
