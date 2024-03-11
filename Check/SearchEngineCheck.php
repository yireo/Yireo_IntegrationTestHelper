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
       if ($config['search-engine']) {
           $hostKey = 'opensearch-host';
           $portKey = 'opensearch-port';
       }

        try {
            $response = $this->httpClient->get($config[$hostKey].':'.$config[$portKey]);
        } catch (GuzzleException $e) {
            return false;
        }

        return $response->getStatusCode() === 200;
    }
}
