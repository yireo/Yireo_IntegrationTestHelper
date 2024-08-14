<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Unit\Generator;

use PHPUnit\Framework\TestCase;
use Yireo\IntegrationTestHelper\Generator\ClassStub;
use Yireo\IntegrationTestHelper\Generator\TestClassStub;

class TestClassStubTest extends TestCase
{
    public function testGetModuleName()
    {
        $classStub = new ClassStub('Yireo_Foobar', '\Yireo\Foobar\Example');
        $testClassStub = TestClassStub::generateFromOriginalClassStub($classStub, 'unit');
        $this->assertEquals('Yireo_Foobar', $testClassStub->getModuleName());
    }

    public function testGetFullQualifiedClassName()
    {
        $classStub = new ClassStub('Yireo_IntegrationTestHelper', '\Yireo\IntegrationTestHelper\Some\Example');
        $testClassStub = TestClassStub::generateFromOriginalClassStub($classStub, 'unit');
        $this->assertEquals('Yireo\IntegrationTestHelper\Test\Unit\Some\ExampleTest', $testClassStub->getFullQualifiedClassName());

        $classStub = new ClassStub('Yireo_IntegrationTestHelper', ClassStub::class);
        $testClassStub = TestClassStub::generateFromOriginalClassStub($classStub, 'unit');
        $this->assertEquals('Yireo\IntegrationTestHelper\Test\Unit\Generator\ClassStubTest', $testClassStub->getFullQualifiedClassName());
    }

    public function testGetModuleClassPrefix()
    {
        $classStub = new ClassStub('Yireo_Foobar', '\Yireo\Foobar\Some\Example');
        $testClassStub = TestClassStub::generateFromOriginalClassStub($classStub, 'unit');
        $this->assertEquals('Yireo\Foobar', $testClassStub->getModuleClassPrefix());
    }

    public function testGetNamespace()
    {
        $classStub = new ClassStub('Yireo_IntegrationTestHelper', '\Yireo\IntegrationTestHelper\Some\Example');
        $testClassStub = TestClassStub::generateFromOriginalClassStub($classStub, 'unit');
        $this->assertEquals('Yireo\IntegrationTestHelper\Test\Unit\Some', $testClassStub->getNamespace());

        $classStub = new ClassStub('Yireo_IntegrationTestHelper', ClassStub::class);
        $testClassStub = TestClassStub::generateFromOriginalClassStub($classStub, 'unit');
        $this->assertEquals('Yireo\IntegrationTestHelper\Test\Unit\Generator', $testClassStub->getNamespace());
    }

    public function testGetClassName()
    {
        $classStub = new ClassStub('Yireo_IntegrationTestHelper', '\Foo\Bar\Some\Example');
        $testClassStub = TestClassStub::generateFromOriginalClassStub($classStub);
        $this->assertEquals('ExampleTest', $testClassStub->getClassName());
    }

    public function testGetRelativeNamespace()
    {
        $classStub = new ClassStub('Yireo_IntegrationTestHelper', ClassStub::class);
        $testClassStub = TestClassStub::generateFromOriginalClassStub($classStub);
        $this->assertEquals('Test\\Integration\\Generator', $testClassStub->getRelativeNamespace());
    }
}
