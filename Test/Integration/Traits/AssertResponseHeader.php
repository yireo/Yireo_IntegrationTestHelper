<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits;

use Laminas\Http\Headers;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\TestFramework\TestCase\AbstractController;

trait AssertResponseHeader
{
    protected function assertResponseHeadersEmpty()
    {
        $this->assertInstanceOf(AbstractController::class, $this);
        $headers = $this->getResponseHeaders();
        $this->assertEmpty($headers->toArray());
    }

    protected function assertResponseHeadersNotEmpty()
    {
        $this->assertInstanceOf(AbstractController::class, $this);
        $headers = $this->getResponseHeaders();
        $this->assertNotEmpty($headers->toArray());
    }

    protected function assertResponseHeaderValue(string $headerName, mixed $headerValue)
    {
        $this->assertInstanceOf(AbstractController::class, $this);
        $headers = $this->getResponseHeaders();
        $debugMessage = var_export($headers->toArray(), true);

        $header = $headers->get($headerName);
        $this->assertNotFalse($header, 'Header "'.$headerName.'" not found: '.$debugMessage);
        $this->assertEquals($headerValue, $header->getFieldValue(), $debugMessage);
    }

    protected function getResponseHeaders(): Headers
    {
        /** @var HttpResponse $response */
        $response = $this->getResponse();
        return $response->getHeaders();
    }
}
