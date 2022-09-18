# Magento 2 integration testing helper
This module adds various utilities to aid in creating integration tests for Magento 2.

## Installation
Use the following commands to install:

    composer require yireo/magento2-integration-test-helper --dev

Enable this module:

    ./bin/magento module:enable Yireo_IntegrationTestHelper
    ./bin/magento setup:upgrade

## Using this helper to enhance your tests
Parent classes:
- `\Yireo\IntegrationTestHelper\Test\Integration\AbstractTestCase`
- `\Yireo\IntegrationTestHelper\Test\Integration\GraphQlTestCase`

These classes offer some utility functions plus import numerous traits (see `Test/Integration/Traits/`) with PHPUnit assertions. For instance, the following test checks for the proper registration of your module:

```php
<?php declare(strict_types=1);

namespace Yireo\Example\Test\Integration;

use PHPUnit\Framework\TestCase;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsEnabled;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsRegistered;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsRegisteredForReal;

class ModuleTest extends TestCase
{
    use AssertModuleIsEnabled;
    use AssertModuleIsRegistered;
    use AssertModuleIsRegisteredForReal;

    public function testIfModuleIsWorking()
    {
        $this->assertModuleIsEnabled('Yireo_Example');
        $this->assertModuleIsRegistered('Yireo_Example');
        $this->assertModuleIsRegisteredForReal('Yireo_Example');
    }
}
```

## Toggle TESTS_CLEANUP in integration tests configuration
When running integration tests, you probably want to frequently toggle the constant `TESTS_CLEANUP` from `disabled` to `enabled` to `disabled`. The following command-line easily allows for this (assuming the file is actually `dev/tests/integration/phpunit.xml` cause you shouldn't modify the `*.dist` version):

    bin/magento integration_tests:toggle_tests_cleanup

It is toggled. You can also set the value directly:

    bin/magento integration_tests:toggle_tests_cleanup enabled

## Generating the `install-config-mysql.php` return array
When installing Magento - as part of the procedure of running Integration Tests - the file `dev/tests/integration/etc/install-config-mysql.php` should return an array with all of your relevant settings, most importantly the database settings. By using the utility class `Yireo\IntegrationTestHelper\Utilities\InstallConfig` you can quickly generate the relevant output, plus details like Redis and ElasticSearch:

```php
<?php declare(strict_types=1);

use Yireo\IntegrationTestHelper\Utilities\InstallConfig;

return (new InstallConfig())
    ->addDb('mysql80_tmpfs')
    ->addRedis()
    ->addElasticSearch('elasticsearch6')
    ->get();
```

## Disable modules when installing Magento
When installing Magento - as part of the procedure of running Integration Tests - the file `dev/tests/integration/etc/install-config-mysql.php` is modified to point to your test database. There is also a flag `disable-modules` that allows you to disable specific Magento modules. Disabling modules is a benefit for performance. The utility class `Yireo\IntegrationTestHelper\Utilities\DisableModules` allows you to generate a listing of modules to disable quicker. 

In the following example, first all (!) modules that are listed in the global `app/etc/config.php` are disabled by default. But then all Magento core modules and the module `Yireo_GoogleTagManager2` are enabled (but only if they are marked as active in the global configuration):
```php
<?php declare(strict_types=1);

use Yireo\IntegrationTestHelper\Utilities\DisableModules;
use Yireo\IntegrationTestHelper\Utilities\InstallConfig;

$disableModules = (new DisableModules())
    ->disableAll()
    ->enableMagento()
    ->enableByName('Yireo_GoogleTagManager2')
    ->toString();

return (new InstallConfig())
    ->setDisableModules($disableModules)
    ->addDb('mysql80_tmpfs')
    ->addRedis()
    ->addElasticSearch('elasticsearch6')
    ->get();
```

Instead of using a hard-coded value, you might also want to set an environment variable `MAGENTO_MODULE` - for instance, in the **Run** configuration in PHPStorm - which is then reused via the method `enableByMagentoModuleEnv`. This way, you can keep the same `install-config-mysql.php` file while reusing it for various **Run** configurations:

```php
$disableModules->disableAll()
    ->enableMagento()
    ->enableByMagentoModuleEnv();
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

## Validating your configuration
The module also ships with a CLI command to easily check whether the currently returned `setup:install` flags make sense:
```bash
$ bin/magento integration_tests:check
+--------------------+--------------------+
| Setting            | Value              |
+--------------------+--------------------+
| TESTS_CLEANUP      | enabled            |
| TESTS_MAGENTO_MODE | developer          |
| DB host            | mysql57_production |
| DB username        | root               |
| DB password        | root               |
| DB name            | m2_test            |
| DB reachable       | Yes                |
| ES host            | localhost          |
| ES port            | 9207               |
| ES reachable       | Yes                |
| Redis host         | 127.0.0.1          |
| Redis port         | 6379               |
| Redis reachable    | Yes                |
+--------------------+--------------------+
```