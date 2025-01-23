<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits;

use Magento\Framework\Module\ModuleList;
use Magento\TestFramework\Helper\Bootstrap;

trait AssertModuleIsEnabled
{
    use GetObjectManager;

    protected function assertModuleIsEnabled(string $moduleName)
    {
        $moduleList = $this->om()->create(ModuleList::class);
        $modulesOutput = implode(', ', $moduleList->getNames());

        $this->assertTrue(
            $moduleList->has($moduleName),
            'The module "' . $moduleName . '" is not enabled',
            $modulesOutput
        );
    }
}
