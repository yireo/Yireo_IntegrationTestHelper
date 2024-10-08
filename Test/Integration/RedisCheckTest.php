<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Check;

use Magento\Framework\App\ObjectManager;
use PHPUnit\Framework\TestCase;
use Yireo\IntegrationTestHelper\Check\RedisCheck;
use Yireo\IntegrationTestHelper\Utilities\CurrentInstallConfig;

class RedisCheckTest extends TestCase
{
    public function testCheckRedisConnection()
    {
        $currentInstallConfig = $this->createMock(CurrentInstallConfig::class);
        $currentInstallConfig->method('getValues')->willReturn([]);

        $redisCheck = ObjectManager::getInstance()->create(RedisCheck::class, [
            'currentInstallConfig' => $currentInstallConfig
        ]);

        $this->assertFalse($redisCheck->checkRedisConnection());
    }
}
