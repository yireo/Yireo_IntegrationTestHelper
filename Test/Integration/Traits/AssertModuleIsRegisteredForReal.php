<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\DeploymentConfig\Reader as DeploymentConfigReader;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Module\ModuleList;
use Magento\TestFramework\ObjectManager;

trait AssertModuleIsRegisteredForReal
{
    protected function assertModuleIsRegisteredForReal(string $moduleName)
    {
        $objectManager = ObjectManager::getInstance();

        $directoryList = $objectManager->create(DirectoryList::class, ['root' => BP]);
        $deploymentConfigReader = $objectManager->create(DeploymentConfigReader::class, ['dirList' => $directoryList]);
        $deploymentConfig = $objectManager->create(DeploymentConfig::class, ['reader' => $deploymentConfigReader]);

        /** @var $moduleList ModuleList */
        $moduleList = $objectManager->create(
            ModuleList::class,
            ['config' => $deploymentConfig]
        );

        $this->assertTrue($moduleList->has($moduleName));
    }
}