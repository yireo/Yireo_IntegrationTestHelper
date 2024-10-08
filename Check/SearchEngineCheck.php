<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Check;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Yireo\IntegrationTestHelper\Utilities\CurrentInstallConfig;

class SearchEngineCheck
{
    private CurrentInstallConfig $currentInstallConfig;
    private Client $httpClient;

    public function __construct(
        CurrentInstallConfig $currentInstallConfig,
        Client $httpClient
    ) {
        $this->currentInstallConfig = $currentInstallConfig;
        $this->httpClient = $httpClient;
    }

    public function checkSearchEngineConnection(): bool
    {
        $config = $this->currentInstallConfig->getValues();
        $hostKey = 'elasticsearch-host';
        $portKey = 'elasticsearch-port';
        if (isset($config['search-engine']) && $config['search-engine'] === 'opensearch') {
            $hostKey = 'opensearch-host';
            $portKey = 'opensearch-port';
        }

        $configHostKey = $config[$hostKey] ?? 'localhost';
        $configPortKey = $config[$portKey] ?? '9200';

        try {

            $response = $this->httpClient->get($configHostKey.':'.$configPortKey);
        } catch (GuzzleException $e) {
            return false;
        }

        return $response->getStatusCode() === 200;
    }
}
