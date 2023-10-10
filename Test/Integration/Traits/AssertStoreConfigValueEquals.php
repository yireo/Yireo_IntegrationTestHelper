<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\StoreManagerInterface;

trait AssertStoreConfigValueEquals
{
    protected function assertStoreConfigValueEquals($expectedValue, string $path, ?string $scopeType = null, ?string $scopeCode = null)
    {
        if (empty($scopeType)) {
            $scopeType = 'default';
        }

        $scopeConfig = ObjectManager::getInstance()->get(ScopeConfigInterface::class);
        if ($scopeType === 'store' && empty($scopeCode)) {
            $storeManager = ObjectManager::getInstance()->get(StoreManagerInterface::class);
            $scopeCode = $storeManager->getDefaultStoreView();
        }

        $value = $scopeConfig->getValue($path, $scopeType, $scopeCode);
        $this->assertEquals($expectedValue, $value);
    }
}
