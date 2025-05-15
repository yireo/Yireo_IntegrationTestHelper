<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Console\Command;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Component\ComponentRegistrar;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yireo\IntegrationTestHelper\Check\RedisCheck;
use Yireo\IntegrationTestHelper\Exception\IntegrationTesting\PhpUnitFile\ConstantNotFound;
use Yireo\IntegrationTestHelper\Exception\IntegrationTesting\PhpUnitFile\FileNotFound;
use Yireo\IntegrationTestHelper\Exception\IntegrationTesting\PhpUnitFile\InvalidContent;
use Yireo\IntegrationTestHelper\Utilities\CurrentInstallConfig;
use Yireo\IntegrationTestHelper\Check\DbCheck;
use Yireo\IntegrationTestHelper\Utilities\IntegrationTesting\PhpUnitFile\Constant;
use Yireo\IntegrationTestHelper\Check\SearchEngineCheck;

class GenerateModuleMappingCommand extends Command
{
    public function __construct(
        private ComponentRegistrar $componentRegistrar,
        private DirectoryList $directoryList,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('integration_tests:generate-module-mapping')
            ->setDescription('Generate dev/tests/integration/module-map.json');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     * @throws ConstantNotFound
     * @throws FileNotFound
     * @throws InvalidContent
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $paths = $this->componentRegistrar->getPaths(ComponentRegistrar::MODULE);
        $file = $this->directoryList->getRoot() . '/dev/tests/integration/module-map.json';

        file_put_contents($file, json_encode($paths, JSON_PRETTY_PRINT));

        return Command::SUCCESS;
    }
}
