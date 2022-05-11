<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Interception\PluginList;

trait AssertInterceptorPluginIsRegistered
{
    protected function assertInterceptorPluginIsRegistered(string $subjectClass, string $pluginClass, string $pluginName)
    {
        $pluginList = Bootstrap::getObjectManager()->get(PluginList::class);
        $pluginInfo = $pluginList->get($subjectClass, []);
        $this->assertArrayHasKey($pluginName, $pluginInfo, var_export(array_keys($pluginInfo), true));

        $this->assertSame(
            $pluginClass,
            $pluginInfo[$pluginName]['instance']
        );
    }
}