<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yireo\IntegrationTestHelper\Utilities\DbCheck;
use Yireo\IntegrationTestHelper\Utilities\IntegrationTesting\InstallConfig;
use Yireo\IntegrationTestHelper\Utilities\IntegrationTesting\PhpUnitFile\Constant;

class CheckCommand extends Command
{
    /**
     * @var Constant
     */
    private $constant;

    /**
     * @var InstallConfig
     */
    private $installConfig;
    /**
     * @var DbCheck
     */
    private $dbCheck;

    /**
     * @param Constant $constant
     * @param InstallConfig $installConfig
     * @param DbCheck $dbCheck
     * @param string|null $name
     */
    public function __construct(
        Constant      $constant,
        InstallConfig $installConfig,
        DbCheck       $dbCheck,
        string        $name = null
    )
    {
        parent::__construct($name);
        $this->constant = $constant;
        $this->installConfig = $installConfig;
        $this->dbCheck = $dbCheck;
    }

    protected function configure()
    {
        $this->setName('integration_tests:check')
            ->setDescription('Perform a simple check before running integration tests');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('TESTS_CLEANUP: ' . $this->constant->getValue('TESTS_CLEANUP'));
        $output->writeln('TESTS_MAGENTO_MODE: ' . $this->constant->getValue('TESTS_MAGENTO_MODE'));
        $output->writeln('DB host: ' . $this->installConfig->getConfigValue('db-host'));
        $output->writeln('DB username: ' . $this->installConfig->getConfigValue('db-user'));
        $output->writeln('DB password: ' . $this->installConfig->getConfigValue('db-password'));
        $output->writeln('DB name: ' . $this->installConfig->getConfigValue('db-name'));
        $output->writeln('DB reachable: ' . $this->getDbReachable());
    }

    private function getDbReachable(): string
    {
        return $this->dbCheck->checkDbConnection(
            $this->installConfig->getConfigValues()
        ) ? 'Yes' : 'No';
    }
}