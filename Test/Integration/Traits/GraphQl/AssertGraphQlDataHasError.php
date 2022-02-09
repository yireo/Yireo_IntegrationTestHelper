<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits\GraphQl;

trait AssertGraphQlDataHasError
{
    protected function assertGraphQlDataHasError(string $errorString, array $queryData)
    {
        $this->assertArrayHasKey('errors', $queryData);
        $this->assertNotEmpty($queryData['errors']);
        $this->assertArrayHasKey('message', $queryData['errors'][0]);
        $this->assertStringContainsString($errorString, $queryData['errors'][0]['message'], var_export($queryData, true));
    }
}
