<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits;

use Magento\Framework\Module\ModuleList;
use Magento\TestFramework\Helper\Bootstrap;

trait AssertModuleIsEnabled
{
    use GetObjectManager;

    protected function assertModuleIsEnabled(string $moduleName, bool $debug = false)
    {
        $moduleList = $this->om()->create(ModuleList::class);

        $debugMsg = '.';
        if ($debug) {
            $debugMsg = ': '.implode(', ', $moduleList->getNames());
        }

        $this->assertTrue(
            $moduleList->has($moduleName),
            'The module "' . $moduleName . '" is not enabled' . $debugMsg
        );
    }
}
