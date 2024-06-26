<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Generator;

use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsEnabled;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsRegistered;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsRegisteredForReal;

class ModuleTestGenerator
{
    public function __construct(
        private PhpGeneratorFactory $phpGeneratorFactory,
    ) {
    }

    public function generate(string $moduleName, string $classNamePrefix, string $classPath): bool
    {
        $phpGenerator = $this->phpGeneratorFactory->create('ModuleTest', $classNamePrefix);
        $phpGenerator->addTrait(AssertModuleIsEnabled::class);
        $phpGenerator->addTrait(AssertModuleIsRegistered::class);
        $phpGenerator->addTrait(AssertModuleIsRegisteredForReal::class);
        $phpGenerator->addClassMethod('testModule', $this->getMethodModuleTest($moduleName));

        $file = 'ModuleTest.php';
        return $phpGenerator->generate($classPath . '/' . $file);
    }

    private function getMethodModuleTest(string $moduleName): string
    {
        return <<<EOF
\$moduleName = '$moduleName';
\$this->assertModuleIsEnabled(\$moduleName);
\$this->assertModuleIsRegistered(\$moduleName);
\$this->assertModuleIsRegisteredForReal(\$moduleName);
EOF;
    }
}
