# ⚠️Abandoned!
Blueprint as of [v2.8.0](https://github.com/laravel-shift/blueprint/releases/tag/v2.8.0) now has first party support for generating PestPHP tests. So this package is now read only and you should update blueprint to use their new test generation.

# Blueprint Pest Addon

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fidum/laravel-blueprint-pestphp-addon.svg?style=for-the-badge)](https://packagist.org/packages/fidum/laravel-blueprint-pestphp-addon)
[![GitHub Workflow Status (with branch)](https://img.shields.io/github/actions/workflow/status/fidum/laravel-blueprint-pestphp-addon/run-tests.yml?branch=main&style=for-the-badge)](https://github.com/fidum/laravel-blueprint-pestphp-addon/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![Codecov](https://img.shields.io/codecov/c/github/fidum/laravel-blueprint-pestphp-addon?logo=codecov&logoColor=white&style=for-the-badge)](https://codecov.io/gh/fidum/laravel-blueprint-pestphp-addon)
[![Twitter Follow](https://img.shields.io/badge/follow-%40danmasonmp-1DA1F2?logo=twitter&style=for-the-badge)](https://twitter.com/danmasonmp)  

:mega: Shoutout to [Jason McCreary](https://github.com/jasonmccreary) whose [Blueprint](https://github.com/laravel-shift/blueprint) and [Assertions](https://github.com/jasonmccreary/laravel-test-assertions) packages lay the groundwork for this addon. :raised_hands:

Installing this addon will allow you to generate [Pest](https://github.com/pestphp/pest) HTTP tests instead of the standard PHPUnit HTTP tests with the `php artisan blueprint:build` command.

![Preview](docs/preview.png)

## Installation

You can install this package and **[Blueprint](https://github.com/laravel-shift/blueprint)** via composer:

```bash
composer require --dev laravel-shift/blueprint fidum/laravel-blueprint-pestphp-addon
```

## Usage

1. Install [Pest](https://github.com/pestphp/pest) by following their [installation instructions](https://pestphp.com/docs/installation/).

2. Refer to [Blueprint's Basic Usage](https://github.com/laravel-shift/blueprint#basic-usage) to get started. Afterwards you can run the `blueprint:build` command to generate Pest HTTP tests automatically for your controllers.

3. Read [Pest's Writing Tests](https://pestphp.com/docs/writing-tests/) to help understand the generated test output. 

## Examples
We use fixtures in our tests to make sure this package generates the files correctly. Feel free to browse them at the link below as examples of what output you should expect. 

[Click here to view the files](https://github.com/fidum/laravel-blueprint-pestphp-addon/tree/master/tests/fixtures/tests/Feature/Http/Controllers)

## Testing
```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email :author_email instead of using the issue tracker.

## Credits

- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
