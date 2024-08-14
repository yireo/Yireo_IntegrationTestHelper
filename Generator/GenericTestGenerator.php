<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Generator;

use Yireo\IntegrationTestHelper\Test\Integration\Traits\GetObjectManager;

class GenericTestGenerator
{
    public function __construct(
        private PhpGeneratorFactory $phpGeneratorFactory,
    ) {
    }

    public function generate(ClassStub $classStub, string $modulePath): bool
    {
        $testClassStub = TestClassStub::generateFromOriginalClassStub($classStub);
        $testClassName = $testClassStub->getClassName();

        $phpGenerator = $this->phpGeneratorFactory->create($testClassName, $testClassStub->getNamespace());
        $phpGenerator->addTrait(GetObjectManager::class);
        $phpGenerator->addUse($classStub->getFullQualifiedClassName());

        $phpGenerator->addClassMethod('testIfInstantiationWorks', $this->getTestIfInstantiationWorks($classStub->getClassName()));

        $file = $modulePath . '/' .$testClassStub->getRelativeNamespace() . '/' . $testClassName.'.php';
        $file = str_replace('\\','/', $file);

        return $phpGenerator->generate($file);
    }

    private function getTestIfInstantiationWorks(string $className): string
    {
        $variableName = lcfirst($className);

        return <<<EOF
\${$variableName} = \$this->om()->get({$className}::class);
\$this->assertInstanceOf({$className}::class, \${$variableName});
EOF;
    }
}
