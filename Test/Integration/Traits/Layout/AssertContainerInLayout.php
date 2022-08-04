<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits\Layout;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\LayoutInterface;

trait AssertContainerInLayout
{
    public function assertContainerInLayout(string $containerName)
    {
        $layout = ObjectManager::getInstance()->get(LayoutInterface::class);
        $containers = $layout->getUpdate()->getContainers();

        $debugMsg = 'Container "' . $containerName . '" is not found in layout: ';
        $debugMsg .= var_export($containers, true);
        $this->assertTrue(array_key_exists($containerName, $containers), $debugMsg);
    }
}