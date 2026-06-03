<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits;

use Magento\Framework\Component\ComponentRegistrar;

trait AssertModuleIsRegistered
{
    use GetObjectManager;

    protected function assertModuleIsRegistered(string $moduleName, bool $debug = false)
    {
        $registrar = $this->om()->create(ComponentRegistrar::class);

        $debugMsg = '.';
        if ($debug) {
            $debugMsg = ': '.PHP_EOL.implode(PHP_EOL, $registrar->getPaths(ComponentRegistrar::MODULE));
        }

        $this->assertArrayHasKey(
            $moduleName,
            $registrar->getPaths(ComponentRegistrar::MODULE),
            'Module ' . $moduleName . ' is not registered'.$debugMsg
        );
    }
}
