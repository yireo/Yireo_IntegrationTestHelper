<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits\GraphQl;

trait AssertGraphQlDataHasError
{
    protected function assertGraphQlDataHasError(string $errorString, array $queryData)
    {
        $queryResult = 'Query result: '.json_encode($queryData);

        $this->assertArrayHasKey('errors', $queryData, $queryResult);
        $this->assertNotEmpty($queryData['errors'], $queryResult);
        $this->assertArrayHasKey('message', $queryData['errors'][0], $queryResult);
        $this->assertStringContainsString($errorString, $queryData['errors'][0]['message'], $queryResult);
    }
}
