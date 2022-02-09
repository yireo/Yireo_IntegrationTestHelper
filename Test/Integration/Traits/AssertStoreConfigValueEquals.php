<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\TestFramework\Helper\Bootstrap;

trait AssertStoreConfigValueEquals
{
    protected function assertStoreConfigValueEquals($expectedValue, string $path)
    {
        $scopeConfig = Bootstrap::getObjectManager()->get(ScopeConfigInterface::class);
        $value = $scopeConfig->getValue($path);
        $this->assertEquals($expectedValue, $value);
    }
}