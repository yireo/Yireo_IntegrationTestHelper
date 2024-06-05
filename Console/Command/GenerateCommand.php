<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Console\Command;

use Composer\Console\Input\InputArgument;
use Magento\Framework\Component\ComponentRegistrar;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yireo\IntegrationTestHelper\Check\RedisCheck;
use Yireo\IntegrationTestHelper\Exception\IntegrationTesting\PhpUnitFile\ConstantNotFound;
use Yireo\IntegrationTestHelper\Exception\IntegrationTesting\PhpUnitFile\FileNotFound;
use Yireo\IntegrationTestHelper\Exception\IntegrationTesting\PhpUnitFile\InvalidContent;
use Yireo\IntegrationTestHelper\Generator\TestGenerator;
use Yireo\IntegrationTestHelper\Utilities\CurrentInstallConfig;
use Yireo\IntegrationTestHelper\Check\DbCheck;
use Yireo\IntegrationTestHelper\Utilities\IntegrationTesting\PhpUnitFile\Constant;
use Yireo\IntegrationTestHelper\Check\SearchEngineCheck;

class GenerateCommand extends Command
{
    public function __construct(
        private TestGenerator $testGenerator,
        private ComponentRegistrar $componentRegistrar,
        $name = null
    ) {
        parent::__construct($name);
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('integration_tests:generate')
            ->setDescription('Generate tests for a given module')
            ->addArgument('moduleName', InputArgument::REQUIRED, 'Module name');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $moduleName = (string)$input->getArgument('moduleName');
        if (empty($moduleName)) {
            $output->writeln('<error>No module name given as argument</error>');
            return Command::INVALID;
        }

        $path = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, $moduleName);
        if (false === is_dir($path)) {
            $output->writeln('<error>Module name is not registered</error>');
            return Command::INVALID;
        }

        $this->testGenerator->generate($moduleName, $output);

        return Command::SUCCESS;
    }
}
