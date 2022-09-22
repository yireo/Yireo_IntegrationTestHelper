<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Utilities;

use Magento\Framework\App\ObjectManager;

class InstallConfig
{
    private array $installConfig = [];
    private ?DisableModules $disableModules = null;

    public function __construct(
        array $installConfig = []
    ) {
        $this->installConfig = $installConfig;
    }

    public function getDefault(): array
    {
        return (new DefaultInstallConfig())->getValues();
    }

    public function setDisableModules(DisableModules $disableModules): InstallConfig
    {
        $this->disableModules = $disableModules;
        return $this;
    }

    /**
     * @param string $dbHost
     * @param string $dbUser
     * @param string $dbPassword
     * @param string $dbName
     * @param string $dbPrefix
     * @return void
     */
    public function addDb(
        string $dbHost = 'localhost',
        string $dbUser = 'root',
        string $dbPassword = 'root',
        string $dbName = 'magento2',
        string $dbPrefix = ''
    ): InstallConfig {
        $this->installConfig['db-host'] = $dbHost;
        $this->installConfig['db-user'] = $dbUser;
        $this->installConfig['db-password'] = $dbPassword;
        $this->installConfig['db-name'] = $dbName;
        $this->installConfig['db-prefix'] = $dbPrefix;
        return $this;
    }

    /**
     * @param string $searchEngine
     * @param string $serverName
     * @param string $serverPort
     * @return void
     */
    public function addElasticSearch(
        string $searchEngine = 'elasticsearch7',
        string $serverName = 'localhost',
        string $serverPort = '9200'
    ): InstallConfig {
        $this->installConfig['search-engine'] = $searchEngine;
        $this->installConfig['elasticsearch-host'] = $serverName;
        $this->installConfig['elasticsearch-port'] = $serverPort;
        return $this;
    }

    /**
     * @param string $serverName
     * @param string $serverPort
     * @param int $redisDb
     * @return void
     */
    public function addRedis(
        string $serverName = '127.0.0.1',
        string $serverPort = '6379',
        int $redisDb = 1
    ): InstallConfig {
        $this->installConfig['cache-backend-redis-server'] = $serverName;
        $this->installConfig['cache-backend-redis-port'] = $serverPort;
        $this->installConfig['cache-backend-redis-db'] = $redisDb;
        return $this;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        $installConfig = array_merge($this->getDefault(), $this->installConfig);

        if ($this->disableModules instanceof DisableModules) {
            $installConfig['disable-modules'] = $this->disableModules->toString();
        }

        return $installConfig;
    }
}
