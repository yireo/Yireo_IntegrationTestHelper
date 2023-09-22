<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits;

use Magento\Framework\App\State;
use Magento\Framework\Module\Dir\Reader as ModuleDirReader;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * @todo: Currently not working properly
 */
trait AssertDiFileIsLoaded
{
    use AssertModuleIsRegistered;
    use GetObjectManager;

    protected function assertDiFileIsLoaded(string $moduleName, string $areaCode = 'frontend')
    {
        $applicationState = $this->om()->get(State::class);
        $applicationState->setAreaCode($areaCode);

        $this->assertModuleIsRegistered($moduleName);

        /** @var ModuleDirReader $modulesReader */
        $modulesReader = $this->om()->create(ModuleDirReader::class);
        $configFiles = array_keys($modulesReader->getConfigurationFiles('di.xml')->toArray());
        $this->assertNotEmpty($configFiles);

        $diFile = $this->getModulePath($moduleName) . '/etc/' . $areaCode . '/.di.xml';

        $diXmlFound = false;
        foreach ($configFiles as $configFile) {
            if (strstr($configFile, $diFile)) {
                $diXmlFound = true;
                break;
            }
        }

        $this->assertTrue($diXmlFound, 'File "' . $diFile . '" has not been loaded');
    }
}
