# My `dev/tests/integration/etc/install-config-mysql.php` file

```php
<?php declare(strict_types=1);

use Yireo\IntegrationTestHelper\Utilities\DisableModules;
use Yireo\IntegrationTestHelper\Utilities\InstallConfig;

$disableModules = (new DisableModules(__DIR__ . '/../../../..'))
    ->disableAll()
    ->disableByPattern('Magento_AdminNotification')
    ->disableGraphQl()
    ->disableMagentoInventory()
    ->enableMagento()
    ->enableByMagentoModuleEnv();

$installConfig = (new InstallConfig())
    ->addElasticSearch('elasticsearch7', 'localhost', '9207')
    ->addRedis()
    ->addDb('mysql57_production', 'root', 'root', 'm2_test')
    ->setDisableModules($disableModules);

return $installConfig->get();
```