<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits\GraphQl;

trait AssertGraphQlDataHasData
{
    protected function assertGraphQlDataHasData(string $dataPath, array $queryData)
    {
        $queryResult = 'Query result: '.json_encode($queryData);

        $dataParts = explode('.', 'data.'.$dataPath);
        foreach ($dataParts as $dataPart) {
            $this->assertArrayHasKey($dataPart, $queryData, $queryResult);
            $this->assertNotEmpty($queryData[$dataPart], $queryResult);
            $queryData = $queryData[$dataPart];
        }
    }
}
