<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration;

use Magento\GraphQl\Service\GraphQlRequest;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\GraphQl\AssertGraphQlDataHasData;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\GraphQl\AssertGraphQlDataHasError;

class GraphQlTestCase extends AbstractTestCase
{
    use AssertGraphQlDataHasError;
    use AssertGraphQlDataHasData;

    /**
     * @param string $query
     * @return array
     */
    protected function getGraphQlQueryData(string $query): array
    {
        // @phpstan-ignore-next-line
        $graphQlRequest = $this->objectManager->get(GraphQlRequest::class);
        $response = $graphQlRequest->send($query);
        return json_decode($response->getContent(), true);
    }
}
