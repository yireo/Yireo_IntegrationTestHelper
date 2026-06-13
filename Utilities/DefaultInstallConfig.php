<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Utilities;

use Magento\TestFramework\Bootstrap;

class DefaultInstallConfig
{
    /**
     * @return mixed[]
     */
    public function getValues(): array
    {
        return [
            'db-host' => 'localhost',
            'db-user' => 'root',
            'db-password' => 'root',
            'db-name' => 'magento',
            'db-prefix' => '',
            'search-engine' => 'elasticsearch7',
            'elasticsearch-host' => 'localhost',
            'elasticsearch-port' => '9200',
            'backend-frontname' => 'backend',
            'admin-user' => Bootstrap::ADMIN_NAME,
            'admin-password' => Bootstrap::ADMIN_PASSWORD,
            'admin-email' => Bootstrap::ADMIN_EMAIL,
            'admin-firstname' => Bootstrap::ADMIN_FIRSTNAME,
            'admin-lastname' => Bootstrap::ADMIN_LASTNAME,
            'allow-parallel-generation' => null,
            'skip-db-validation' => null
        ];
    }
}
