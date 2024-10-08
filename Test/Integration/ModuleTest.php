<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Check;

use PHPUnit\Framework\TestCase;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsEnabled;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsRegistered;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsRegisteredForReal;

class ModuleTest extends TestCase
{
    use AssertModuleIsRegistered;
    use AssertModuleIsRegisteredForReal;
    use AssertModuleIsEnabled;

    public function testIfModuleIsEnabled()
    {
        $moduleName = 'Yireo_IntegrationTestHelper';
        $this->assertModuleIsRegisteredForReal($moduleName);
        $this->assertModuleIsRegistered($moduleName);
        $this->assertModuleIsEnabled($moduleName);
    }
}
