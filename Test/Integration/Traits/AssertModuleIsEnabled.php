<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits;

use Magento\Framework\Module\ModuleList;
use Magento\TestFramework\Helper\Bootstrap;

trait AssertModuleIsEnabled
{
    protected function assertModuleIsEnabled(string $moduleName)
    {
        $moduleList = Bootstrap::getObjectManager()->create(ModuleList::class);
        $this->assertTrue(
            $moduleList->has($moduleName),
            'The module "' . $moduleName . '" is not enabled'
        );
    }
}