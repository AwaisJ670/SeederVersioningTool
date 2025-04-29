# Laravel Seeder Versioning Tool 


![Seeder Versioning Tool](https://banners.beyondco.de/Seeder%20Versioning%20Tool.png?theme=dark&packageManager=composer+require&packageName=codebider%2Fseeder-versioning-tool&pattern=zigZag&style=style_1&description=Effortlessly+manage+and+execute+seeders+in+a+controlled%2C+chronological+order+with+module+support+and+batch+tracking&md=1&showWatermark=1&fontSize=100px&images=https%3A%2F%2Flaravel.com%2Fimg%2Flogomark.min.svg)


[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
![Packagist Downloads](https://img.shields.io/packagist/dt/codebider/seeder-versioning-tool)
![Packagist Version](https://img.shields.io/packagist/v/codebider/seeder-versioning-tool)

A Laravel package to manage and version database seeders using Artisan commands. This tool ensures that seeders are executed in a controlled and chronological order, with support for modules and batch tracking.

---

## 📦 Installation

You can install the package via Composer:

```bash
composer require codebider/seeder-versioning-tool
```

If you’re using Laravel < 5.5, add the service provider manually in `config/app.php`:

```php
'providers' => [
    CodeBider\versionSeeder\versionSeederServiceProvider::class,
];
```

---

## ⚙️ Configuration (Optional)

You can publish the migration and stub files using:

```bash
php artisan vendor:publish --provider="CodeBider\versionSeeder\versionSeederServiceProvider"
```

Make sure to run the migration to create the `seeders` table:

```bash
php artisan vendor:publish --tag=migrations
php artisan migrate
```

This will publish:

- `database/migrations/2025_04_29_171328_create_seeders_table.php`
- `stubs/versioned_seeder.stub`

---

## 🧪 Usage

### Running Versioned Seeders

Run all versioned seeders in chronological order:

```bash
php artisan db:seed:versioned
```

Options:
- `--connection=`: Specify the database connection.
- `--database=`: Specify the database name.
- `--module=`: Run seeders for a specific module.

### Creating a Versioned Seeder

Generate a new versioned seeder file:

```bash
php artisan make:seeder:versioned SeederName
```

Options:
- `--module=`: Specify the module name for the seeder.

This will create a timestamped seeder file in the `database/seeders/Versioned` directory (or inside the specified module).

---

## 🛠 How It Works

1. **Versioned Seeder Execution**:
   - Seeders are executed in chronological order based on their file creation time.
   - Each seeder is tracked in the `seeders` table to prevent duplicate execution.

2. **Batch Tracking**:
   - Seeders are grouped into batches for better tracking and rollback capabilities.

3. **Module Support**:
   - Seeders can be organized into modules for better separation of concerns.

4. **Custom Stub**:
   - The default stub file for versioned seeders can be customized by modifying the published `stubs/versioned_seeder.stub`.

---

## ✏️ Custom Stub

The stub file supports the following variables:
- `{{ namespace }}`
- `{{ class }}`

Example stub:

```php
<?php

namespace {{ namespace }};

use App\Console\VersionedSeeder;

class {{ class }} extends VersionedSeeder
{
    public function run()
    {
        // Add your seeding logic here
    }
}
```


---


## 📄 License

This package is open-sourced software licensed under the [MIT license](LICENSE).

---

## 👤 Author

Developed with ❤️ by [Awais Javaid](mailto:info.awaisjavaid@gmail.com)  
📦 Package: `codebider/seeder-versioning-tool`

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request or open an issue.
