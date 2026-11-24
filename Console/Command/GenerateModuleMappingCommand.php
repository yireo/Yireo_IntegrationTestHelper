<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Console\Command;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateModuleMappingCommand extends Command
{
    private const MODULE_MAP_FILE = 'dev/tests/integration/module-map.json';

    public function __construct(
        private readonly ComponentRegistrar $componentRegistrar,
        private readonly Filesystem $filesystem,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('integration_tests:generate-module-mapping')
            ->setDescription('Generate ' . self::MODULE_MAP_FILE);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $paths = $this->componentRegistrar->getPaths(ComponentRegistrar::MODULE);
        $content = json_encode($paths, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($content === false) {
            $output->writeln('<error>Unable to encode module paths</error>');
            return Command::FAILURE;
        }

        $rootDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::ROOT);

        try {
            $rootDirectory->writeFile(self::MODULE_MAP_FILE, $content);
        } catch (FileSystemException $e) {
            $output->writeln('<error>Unable to write ' . self::MODULE_MAP_FILE . '</error>');
            return Command::FAILURE;
        }

        $output->writeln('Written module map to ' . $rootDirectory->getAbsolutePath(self::MODULE_MAP_FILE));
        return Command::SUCCESS;
    }
}
