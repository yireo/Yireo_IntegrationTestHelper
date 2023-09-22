<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits\Layout;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\LayoutInterface;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\GetObjectManager;

trait AssertHandleInLayout
{
    use GetObjectManager;

    public function assertHandleInLayout(string $handleName)
    {
        $layout = $this->om()->get(LayoutInterface::class);
        $handles = $layout->getUpdate()->getHandles();

        $debugMsg = 'Handle "' . $handleName . '" is not found in layout: ';
        $debugMsg .= var_export($handles, true);
        $this->assertTrue(in_array($handleName, $handles), $debugMsg);
    }
}
