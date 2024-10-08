<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Check;

use Cm_Cache_Backend_Redis;
use Yireo\IntegrationTestHelper\Utilities\CurrentInstallConfig;
use Zend_Cache_Exception;

class RedisCheck
{
    private CurrentInstallConfig $currentInstallConfig;

    /**
     * @param CurrentInstallConfig $currentInstallConfig
     */
    public function __construct(
        CurrentInstallConfig $currentInstallConfig
    ) {
        $this->currentInstallConfig = $currentInstallConfig;
    }

    /**
     * @return bool
     */
    public function checkRedisConnection(): bool
    {
        $config = $this->currentInstallConfig->getValues();
        $server = $config['cache-backend-redis-server'] ?? '';
        $port = $config['cache-backend-redis-port'] ?? '';
        $dbName = $config['cache-backend-redis-db'] ?? '';

        try {
            $redis = new Cm_Cache_Backend_Redis([
                'server' => $server,
                'port' => $port,
                'database' => $dbName
            ]);
        } catch (Zend_Cache_Exception $e) {
            return false;
        }

        $info = $redis->getInfo();
        return !empty($info);
    }
}
