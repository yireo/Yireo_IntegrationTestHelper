<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Generator;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
use PHPUnit\Framework\TestCase;

class PhpGeneratorFactory
{
    public function __construct(
        private DirectoryList $directoryList,
        private Filesystem $filesystem
    ) {
    }

    public function create(string $className, string $classNamespace): PHPGenerator
    {
        $classType = new ClassType($className);
        $classType->setFinal();
        $classType->setExtends(TestCase::class);

        $namespaceType = new PhpNamespace($classNamespace);
        $namespaceType->add($classType);
        $namespaceType->addUse(TestCase::class);

        $fileType = new PhpFile;
        $fileType->setStrictTypes();

        $writer = $this->filesystem->getDirectoryWrite($this->directoryList::ROOT);
        return new PhpGenerator($classType, $namespaceType, $fileType, $writer);
    }
}
