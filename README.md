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

These classes offer some utility functions plus import various traits (see `Test/Integration/Traits/`) with PHPUnit assertions.


## Toggle TESTS_CLEANUP in integration tests configuration
When running integration tests, you probably want to frequently toggle the constant `TESTS_CLEANUP` from `disabled` to `enabled` to `disabled`. The following command-line easily allows for this (assuming the file is actually `dev/tests/integration/phpunit.xml` cause you shouldn't modify the `*.dist` version):

    bin/magento integration_tests:toggle_tests_cleanup

It is toggled. You can also set the value directly:

    bin/magento integration_tests:toggle_tests_cleanup enabled
