<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Generator;

class TestClassStub extends ClassStub
{
    static public function generateFromOriginalClassStub(ClassStub $original, string $type = 'Integration')
    {
        $type = ucfirst($type);
        $fullyQualifiedClassName = $original->getModuleClassPrefix() . '\\Test\\'.$type.'\\' . $original->getRelativeNamespace() . '\\' . $original->getClassName(). 'Test';

        return new ClassStub(
            $original->getModuleName(),
            $fullyQualifiedClassName
        );
    }
}
