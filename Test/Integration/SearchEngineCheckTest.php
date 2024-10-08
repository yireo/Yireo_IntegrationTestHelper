<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Check;

use Magento\Framework\App\ObjectManager;
use PHPUnit\Framework\TestCase;
use Yireo\IntegrationTestHelper\Check\SearchEngineCheck;
use Yireo\IntegrationTestHelper\Utilities\CurrentInstallConfig;

class SearchEngineCheckTest extends TestCase
{
    public function testCheckSearchEngineConnection()
    {
        $currentInstallConfig = $this->createMock(CurrentInstallConfig::class);
        $currentInstallConfig->method('getValues')->willReturn([]);

        $searchEngineCheck = ObjectManager::getInstance()->create(SearchEngineCheck::class, [
            'currentInstallConfig' => $currentInstallConfig
        ]);

        $this->assertFalse($searchEngineCheck->checkSearchEngineConnection());
    }
}
