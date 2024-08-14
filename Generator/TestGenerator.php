<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Generator;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Filesystem;
use PhpToken;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;
use Symfony\Component\Console\Output\OutputInterface;

class TestGenerator
{
    public function __construct(
        private ComponentRegistrar $componentRegistrar,
        private ModuleTestGenerator $moduleTestGenerator,
        private GenericTestGenerator $genericTestGenerator,
        private DirectoryList $directoryList,
        private Filesystem $filesystem
    ) {
    }

    public function generate(string $moduleName, OutputInterface $output)
    {
        $modulePath = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, $moduleName);
        $this->createFolder($modulePath.'/Test/Integration/');

        $classNamePrefix = $this->getClassNamePrefix($moduleName);

        $output->writeln('Generating module test');
        $this->moduleTestGenerator->generate($moduleName, $classNamePrefix, $modulePath);

        $phpClasses = $this->getPhpClasses($modulePath);

        foreach ($phpClasses as $phpClass) {
            $output->writeln('Generating test for '.$phpClass);
            $classStub = new ClassStub($moduleName, $phpClass);
            $this->genericTestGenerator->generate($classStub, $modulePath);
        }
    }

    private function getClassNamePrefix(string $moduleName): string
    {
        $moduleNameParts = explode('_', $moduleName);

        return $moduleNameParts[0].'\\'.$moduleNameParts[1].'\\Test\\Integration';
    }

    private function createFolder(string $folder)
    {
        $writer = $this->filesystem->getDirectoryWrite($this->directoryList::ROOT);
        $writer->create($folder);
    }

    private function getPhpClasses(string $folder): array
    {
        $classes = [];
        $directory = new RecursiveDirectoryIterator($folder);
        $iterator = new RecursiveIteratorIterator($directory);
        $regex = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

        foreach ($regex as $file) {
            $file = $file[0];
            if (strstr($file, '/Test/')) {
                continue;
            }

            $class = $this->getClassNameFromFile((string)$file);
            if (empty($class)) {
                continue;
            }

            $classes[$file] = $class;
        }

        return $classes;
    }

    private function getClassNameFromFile(string $file): string
    {
        $className = '';
        $namespace = '';
        $tokens    = PhpToken::tokenize(file_get_contents($file));

        for ($i = 0; $i < count($tokens); $i++) {
            if ($tokens[$i]->getTokenName() === 'T_NAMESPACE') {
                for ($j = $i + 1; $j < count($tokens); $j++) {
                    if ($tokens[$j]->getTokenName() === 'T_NAME_QUALIFIED') {
                        $namespace = $tokens[$j]->text;
                        break;
                    }
                }
            }

            if ($tokens[$i]->getTokenName() === 'T_CLASS') {
                for ($j = $i + 1; $j < count($tokens); $j++) {
                    if ($tokens[$j]->getTokenName() === 'T_WHITESPACE') {
                        continue;
                    }

                    if ($tokens[$j]->getTokenName() === 'T_STRING') {
                        $className = $namespace . '\\' . $tokens[$j]->text;
                    } else {
                        break;
                    }
                }
            }
        }

        return $className;
    }
}
