<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Check;

use GuzzleHttp\Client;
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
       $response = $this->httpClient->get($config['elasticsearch-host'].':'.$config['elasticsearch-port']);
       return $response->getStatusCode() === 200;
    }
}