<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Generator;

use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsEnabled;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsRegistered;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsRegisteredForReal;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\GetObjectManager;

class GenericTestGenerator
{

    public function __construct(
        private PhpGeneratorFactory $phpGeneratorFactory,
    ) {
    }

    public function generate(string $className, string $classNamePrefix, string $classPath): bool
    {
        $phpGenerator = $this->phpGeneratorFactory->create($className, $classNamePrefix);
        $phpGenerator->addTrait(GetObjectManager::class);


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
