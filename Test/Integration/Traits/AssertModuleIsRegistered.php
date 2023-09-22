<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\TestFramework\Helper\Bootstrap;

trait AssertModuleIsRegistered
{
    use GetObjectManager;

    protected function assertModuleIsRegistered(string $moduleName)
    {
        $registrar = $this->om()->create(ComponentRegistrar::class);
        $this->assertArrayHasKey($moduleName, $registrar->getPaths(ComponentRegistrar::MODULE));
    }
}
