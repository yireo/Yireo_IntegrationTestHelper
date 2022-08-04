# Magento 2 integration testing helper
This module adds various utilities to aid in creating integration tests for Magento 2.

## Installation
Use the following commands to install:

    composer require yireo/magento2-integration-test-helper:@dev --dev

Enable this module:

    ./bin/magento module:enable Yireo_IntegrationTestHelper
    ./bin/magento setup:upgrade

## Code usage
Parent classes:
- `\Yireo\IntegrationTestHelper\Test\Integration\AbstractTestCase`
- `\Yireo\IntegrationTestHelper\Test\Integration\GraphQlTestCase`

These classes offer some utility functions plus import numerous traits (see `Test/Integration/Traits/`) with PHPUnit assertions.

## Toggle TESTS_CLEANUP in integration tests configuration
When running integration tests, you probably want to frequently toggle the constant `TESTS_CLEANUP` from `disabled` to `enabled` to `disabled`. The following command-line easily allows for this (assuming the file is actually `dev/tests/integration/phpunit.xml` cause you shouldn't modify the `*.dist` version):

    bin/magento integration_tests:toggle_tests_cleanup

It is toggled. You can also set the value directly:

    bin/magento integration_tests:toggle_tests_cleanup enabled

## Disable modules when installing Magento
When installing Magento - as part of the procedure of running Integration Tests - the file `dev/tests/integration/etc/install-config-mysql.php` is modified to point to your test database. There is also a flag `disable-modules` that allows you to disable specific Magento modules. Disabling modules is a benefit for performance. The utility class `Yireo\IntegrationTestHelper\Utilities\DisableModules` allows you to generate a listing of modules to disable quicker. 

In the following example, first all (!) modules that are listed in the global `app/etc/config.php` are disabled by default. But then all Magento core modules and the module `Yireo_GoogleTagManager2` are enabled (but only if they are marked as active in the global configuration):
```php
<?php declare(strict_types=1);

use Yireo\IntegrationTestHelper\Utilities\DisableModules;

$disableModules = (new DisableModules())
    ->disableAll()
    ->enableMagento()
    ->enableByName('Yireo_GoogleTagManager2')
    ->toString();

return [
    'db-host' => 'localhost',
    'db-user' => 'root',
    'db-password' => 'root',
    'db-name' => 'magento2test',
    ...
    'disable-modules' => $disableModules
];
```

Another example, all the Magento modules are enabled by default. But then MSI and GraphQL are disabled again, while all Yireo modules are enabled:
```php
$disableModules = (new DisableModules())
    ->disableAll()
    ->enableMagento()
    ->disableMagentoInventory()
    ->disableGraphQl()
    ->enableByPattern('Yireo_')
    ->toString();
```

Note that if there would be a module `Yireo_ExampleGraphQl` then this would be first disabled with `disableGraphQl()` and then re-enabled again with `enableByPattern('Yireo_')`. The ordering of your methods matters!
