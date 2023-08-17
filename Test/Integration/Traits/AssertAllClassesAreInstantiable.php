<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\ObjectManagerInterface;
use Symfony\Component\Finder\Finder;

trait AssertAllClassesAreInstantiable
{
    public function assertAllClassesAreInstantiable(
        ObjectManagerInterface $objectManager,
        string $moduleName,
        string $baseNamespace
    ) {
        $componentRegistrar = $objectManager->get(ComponentRegistrar::class);
        $modulePath = $componentRegistrar->getPath('module', $moduleName);
        $finder = new Finder();
        $finder->in($modulePath);
        $finder->exclude('Test/');
        $files = $finder->files()->notName('registration.php')->name('*.php');

        $fileCount = $files->count();
        $instanceCount = 0;

        foreach ($files as $file) {
            $classRelativePath = str_replace(['.php', '/'], ['', '\\'], $file->getRelativePathname());
            $className = $baseNamespace.'\\'.$classRelativePath;

            $mock = $this->getMockBuilder($baseNamespace.'\\'.$classRelativePath)
                ->disableOriginalConstructor()
                ->getMock();

            $this->assertInstanceOf($className, $mock);
            $instanceCount++;
        }

        $this->assertEquals($fileCount, $instanceCount);
    }
}
