# {{ title }}

## Requirements

## Installation

Install via Composer:

```bash
composer require saeedhosan/module-console
```

## Configuration

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


## Testing


## Contributing

Found a bug or want to add a feature? Pull requests are totally welcome. Just make sure the tests pass before submitting.

---

**Module console** was created by **[Saeed Hosan](https://www.linkedin.com/in/saeedhosan)** under the **[MIT license](https://opensource.org/licenses/MIT)**.
