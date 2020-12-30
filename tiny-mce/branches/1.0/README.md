# Tiny MCE Component

[![Latest Version](https://img.shields.io/badge/release-1.0.0-blue?style=for-the-badge)](https://www.presstify.com/pollen-solutions/tiny-mce/)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-green?style=for-the-badge)](LICENSE.md)

## Installation

```bash
composer require pollen-solutions/tiny-mce
```

## Pollen Framework Setup

### Declaration

```php
// config/app.php
return [
      //...
      'providers' => [
          //...
          \Pollen\TinyMce\TinyMceServiceProvider::class,
          //...
      ];
      // ...
];
```

### Configuration

```php
// config/tiny-mce.php
// @see /vendor/pollen-solutions/tiny-mce/resources/config/tiny-mce.php
return [
      //...

      // ...
];
```
