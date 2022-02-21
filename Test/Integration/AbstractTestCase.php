<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration;

use Magento\Framework\App\State;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertDiFileIsLoaded;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertInterceptorPluginIsRegistered;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsEnabled;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsRegistered;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsRegisteredForReal;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertPreferenceOf;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertStoreConfigValueEquals;

class AbstractTestCase extends TestCase
{
    use AssertDiFileIsLoaded;
    use AssertInterceptorPluginIsRegistered;
    use AssertModuleIsEnabled;
    use AssertModuleIsRegistered;
    use AssertModuleIsRegisteredForReal;
    use AssertStoreConfigValueEquals;
    use AssertPreferenceOf;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->objectManager = Bootstrap::getObjectManager();
    }

    protected function setAreaCode($areaCode)
    {
        $applicationState = $this->objectManager->get(State::class);
        $applicationState->setAreaCode($areaCode);
    }


    protected function setAreaCodeToFrontend()
    {
        $applicationState = $this->objectManager->get(State::class);
        $applicationState->setAreaCode(Area::AREA_FRONTEND);
    }

    protected function getModulePath(string $moduleName): string
    {
        $componentRegistrar = $this->objectManager->create(ComponentRegistrar::class);

        $modulePaths = $componentRegistrar->getPaths(ComponentRegistrar::MODULE);
        $this->assertArrayHasKey($moduleName, $modulePaths);

        $modulePath = $componentRegistrar->getPath(ComponentRegistrar::MODULE, $moduleName);
        $this->assertNotEmpty($modulePath, 'Module path is empty');

        return $modulePath;
    }

    protected function setDiTypeArgument(string $typeClass, array $arguments = [])
    {
        $this->objectManager->configure([
            $typeClass => [
                'shared' => false,
                'arguments' => $arguments,
            ],
        ]);

        $this->objectManager->create($typeClass);
    }
}
