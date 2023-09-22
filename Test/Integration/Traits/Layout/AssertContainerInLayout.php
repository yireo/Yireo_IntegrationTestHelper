<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits\Layout;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\LayoutInterface;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\GetObjectManager;

trait AssertContainerInLayout
{
    use GetObjectManager;

    public function assertContainerInLayout(string $containerName)
    {
        $layout = $this->om()->get(LayoutInterface::class);
        $containers = $layout->getUpdate()->getContainers();

        $debugMsg = 'Container "' . $containerName . '" is not found in layout: ';
        $debugMsg .= var_export($containers, true);
        $this->assertTrue(array_key_exists($containerName, $containers), $debugMsg);
    }
}
