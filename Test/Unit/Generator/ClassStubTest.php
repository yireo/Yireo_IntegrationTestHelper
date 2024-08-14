<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Unit\Generator;

use PHPUnit\Framework\TestCase;
use Yireo\IntegrationTestHelper\Generator\ClassStub;

class ClassStubTest extends TestCase
{
    public function testGetModuleName()
    {
        $classStub = new ClassStub('Yireo_Foobar', 'whatever');
        $this->assertEquals('Yireo_Foobar', $classStub->getModuleName());
    }

    public function testGetFullQualifiedClassName()
    {
        $classStub = new ClassStub('whatever', '\Yireo\Foobar\Some\Example');
        $this->assertEquals('Yireo\Foobar\Some\Example', $classStub->getFullQualifiedClassName());

        $classStub = new ClassStub('whatever', ClassStubTest::class);
        $this->assertEquals(ClassStubTest::class, $classStub->getFullQualifiedClassName());
    }

    public function testGetModuleClassPrefix()
    {
        $classStub = new ClassStub('Yireo_Foobar', '\Yireo\Foobar\Some\Example');
        $this->assertEquals('Yireo\Foobar', $classStub->getModuleClassPrefix());
    }

    public function testGetNamespace()
    {
        $classStub = new ClassStub('whatever', '\Foo\Bar\Some\Example');
        $this->assertEquals('Foo\Bar\Some', $classStub->getNamespace());

        $classStub = new ClassStub('whatever', ClassStubTest::class);
        $this->assertEquals('Yireo\IntegrationTestHelper\Test\Unit\Generator', $classStub->getNamespace());
    }

    public function testGetClassName()
    {
        $classStub = new ClassStub('whatever', '\Foo\Bar\Some\Example');
        $this->assertEquals('Example', $classStub->getClassName());
    }

    public function testGetRelativeNamespace()
    {
        $classStub = new ClassStub('Yireo_IntegrationTestHelper', ClassStubTest::class);
        $this->assertEquals('Test\\Unit\\Generator', $classStub->getRelativeNamespace());
    }
}
