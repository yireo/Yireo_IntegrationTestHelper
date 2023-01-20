<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Console\Command;

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

class CheckCommand extends Command
{
    private Constant $constant;
    private CurrentInstallConfig $currentInstallConfig;
    private DbCheck $dbCheck;
    private SearchEngineCheck $searchEngineCheck;
    private RedisCheck $redisCheck;
    
    /**
     * @param Constant $constant
     * @param CurrentInstallConfig $currentInstallConfig
     * @param DbCheck $dbCheck
     * @param SearchEngineCheck $searchEngineCheck
     * @param RedisCheck $redisCheck
     * @param string|null $name
     */
    public function __construct(
        Constant $constant,
        CurrentInstallConfig $currentInstallConfig,
        DbCheck $dbCheck,
        SearchEngineCheck $searchEngineCheck,
        RedisCheck $redisCheck,
        string $name = null
    ) {
        parent::__construct($name);
        $this->constant = $constant;
        $this->currentInstallConfig = $currentInstallConfig;
        $this->dbCheck = $dbCheck;
        $this->searchEngineCheck = $searchEngineCheck;
        $this->redisCheck = $redisCheck;
    }
    
    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('integration_tests:check')
            ->setDescription('Perform a simple check before running integration tests');
    }
    
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     * @throws ConstantNotFound
     * @throws FileNotFound
     * @throws InvalidContent
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(['Setting', 'Value']);
        $table->addRow(['TESTS_CLEANUP', $this->constant->getValue('TESTS_CLEANUP')]);
        $table->addRow(['TESTS_MAGENTO_MODE', $this->constant->getValue('TESTS_MAGENTO_MODE')]);
        $table->addRow(['DB host', $this->currentInstallConfig->getValue('db-host')]);
    
        $table->addRow(['DB username', $this->currentInstallConfig->getValue('db-user')]);
        $table->addRow(['DB password', $this->currentInstallConfig->getValue('db-password')]);
        $table->addRow(['DB name', $this->currentInstallConfig->getValue('db-name')]);
        $table->addRow(['DB reachable', $this->getDbReachable()]);
    
        $table->addRow(['ES host', $this->currentInstallConfig->getValue('elasticsearch-host')]);
        $table->addRow(['ES port', $this->currentInstallConfig->getValue('elasticsearch-port')]);
        $table->addRow(['ES reachable', $this->getSearchEngineReachable()]);
    
        $table->addRow(['Redis host', $this->currentInstallConfig->getValue('cache-backend-redis-server')]);
        $table->addRow(['Redis port', $this->currentInstallConfig->getValue('cache-backend-redis-port')]);
        $table->addRow(['Redis reachable', $this->getRedisReachable()]);
        $table->render();
    }
    
    /**
     * @return string
     */
    private function getDbReachable(): string
    {
        return $this->dbCheck->checkDbConnection() ? 'Yes' : 'No';
    }
    
    /**
     * @return string
     */
    private function getSearchEngineReachable(): string
    {
        return $this->searchEngineCheck->checkSearchEngineConnection() ? 'Yes' : 'No';
    }
    
    /**
     * @return string
     */
    private function getRedisReachable(): string
    {
        return $this->redisCheck->checkRedisConnection() ? 'Yes' : 'No';
    }
}
