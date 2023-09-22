<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

trait AssertStoreConfigValueEquals
{
    use GetObjectManager;

    protected function assertStoreConfigValueEquals($expectedValue, string $path, ?string $scopeType = null, ?string $scopeCode = null)
    {
        $scopeConfig = $this->om()->get(ScopeConfigInterface::class);
        if ($scopeType === 'store' && empty($scopeCode)) {
            $storeManager = $this->om()->get(StoreManagerInterface::class);
            $scopeCode = $storeManager->getDefaultStoreView();
        }

        $value = $scopeConfig->getValue($path, $scopeType, $scopeCode);
        $this->assertEquals($expectedValue, $value);
    }
}
