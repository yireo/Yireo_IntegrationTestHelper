<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Adminhtml;

class MuiBackendControllerTestCase extends AbstractBackendControllerTestCase
{
    protected function setUp(): void
    {
        $this->uri = 'backend/mui/index/render';
        $this->resource = 'Magento_Backend::admin';
        parent::setUp();
    }

    public function testAclNoAccess()
    {
    }

    protected function getMuiRenderData(string $uiComponentName): array
    {
        $this->getRequest()
            ->getHeaders()
            ->addHeaderLine('Accept', 'application/json');

        $url = 'backend/mui/index/render/?namespace=' . $uiComponentName . '&search=&isAjax=true';
        $this->dispatch($url);

        $response = $this->getResponse();
        $data = json_decode($response->getBody(), true);

        $this->assertNotEmpty($data);
        return $data;
    }
}
