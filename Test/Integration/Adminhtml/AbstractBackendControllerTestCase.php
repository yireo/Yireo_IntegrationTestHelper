<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Adminhtml;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Magento\Ui\Component\DataSource;
use Magento\Ui\Component\Form as UiComponentForm;
use Magento\Ui\Component\Listing as UiComponentListing;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\Adminhtml\AssertAclResourceExists;

/**
 * @magentoAppArea adminhtml
 */
class AbstractBackendControllerTestCase extends AbstractBackendController
{
    use AssertAclResourceExists;

    protected function getUiComponent(string $uiComponentName): UiComponentInterface
    {
        $uiComponentFactory = ObjectManager::getInstance()->get(UiComponentFactory::class);
        $uiComponent = $uiComponentFactory->create($uiComponentName);
        $this->assertEquals($uiComponentName, $uiComponent->getName());

        $data = $uiComponent->getData();
        $this->assertNotEmpty($data);

        return $uiComponent;
    }

    protected function getDataProviderFromUiComponent(UiComponentInterface $uiComponent): DataProviderInterface
    {
        $data = $uiComponent->getData();
        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('js_config', $data);
        $this->assertArrayHasKey('provider', $data['js_config']);
        $this->assertNotEmpty($data['js_config']['provider']);
        $providerName = $data['js_config']['provider'];
        $providerName = str_replace($uiComponent->getName() . '.', '', $providerName);

        $childComponents = $uiComponent->getChildComponents();
        $this->assertArrayHasKey($providerName, $childComponents);
        /** @var DataSource $providerComponent */
        $providerComponent = $childComponents[$providerName];
        $this->assertInstanceOf(DataSource::class, $providerComponent);
        return $providerComponent->getDataProvider();
    }

    protected function getDataFromUiComponentDataProvider(UiComponentInterface $uiComponent): ?array
    {
        $dataProvider = $this->getDataProviderFromUiComponent($uiComponent);
        $data = $dataProvider->getData();

        if ($uiComponent instanceof UiComponentForm) {
        }

        if ($uiComponent instanceof UiComponentListing) {
            $this->assertArrayHasKey('totalRecords', $data);
            $this->assertArrayHasKey('items', $data);
        }

        return $data;
    }
}
