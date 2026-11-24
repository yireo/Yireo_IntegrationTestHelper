<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Check;

use Cm_Cache_Backend_Redis;
use Throwable;
use Yireo\IntegrationTestHelper\Utilities\CurrentInstallConfig;

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
                'database' => $dbName,
                'timeout' => 3,
                'connect_retries' => 1,
            ]);

            $info = $redis->getInfo();
        } catch (Throwable $e) {
            return false;
        }

        return !empty($info);
    }
}
