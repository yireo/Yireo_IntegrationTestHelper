<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\DeploymentConfig\Reader as DeploymentConfigReader;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Module\ModuleList;
use Magento\TestFramework\ObjectManager;

trait AssertModuleIsRegisteredForReal
{
    use GetObjectManager;

    protected function assertModuleIsRegisteredForReal(string $moduleName)
    {
        $directoryList = $this->om()->create(DirectoryList::class, ['root' => BP]); // @phpstan-ignore
        $deploymentConfigReader = $this->om()->create(DeploymentConfigReader::class, ['dirList' => $directoryList]);
        $deploymentConfig = $this->om()->create(DeploymentConfig::class, ['reader' => $deploymentConfigReader]);

        /** @var $moduleList ModuleList */
        $moduleList = $this->om()->create(
            ModuleList::class,
            ['config' => $deploymentConfig]
        );

        $this->assertTrue($moduleList->has($moduleName));
    }
}
