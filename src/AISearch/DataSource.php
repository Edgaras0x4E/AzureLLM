<?php

namespace Edgaras\AzureLLM\AISearch;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;

class DataSource {

    private Client $client;
    private Auth $config;

    public function __construct(Auth $config) 
    {
        $this->config = $config;
        $this->client = new Client([
            'base_uri' => $config->getEndpoint(),
            'headers'  => [
                'api-key' => $config->getApiKey(),
                'Content-Type' => 'application/json'
            ]
        ]);
    }
 
    public function createDataSource(string $dataSourceName, array $dataSourceConfig): array
    {
        $url = $this->config->getApiUrl('datasources');
        $body = json_encode(array_merge(['name' => $dataSourceName], $dataSourceConfig));

        try {
            $response = $this->client->post($url, ['body' => $body]);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            throw new \RuntimeException("Failed to create data source: " . $e->getMessage());
        }
    }
 
    public function updateDataSource(string $dataSourceName, array $dataSourceConfig): array
    {
        $url = $this->config->getApiUrl('datasources', $dataSourceName);
        $body = json_encode(array_merge(['name' => $dataSourceName], $dataSourceConfig));

        try {
            $response = $this->client->put($url, ['body' => $body]);
            $responseBody = (string) $response->getBody(); 

            return !empty($responseBody) ? json_decode($responseBody, true) : ['message' => 'Data source updated successfully'];
        } catch (RequestException $e) {
            throw new \RuntimeException("Failed to update data source: " . $e->getMessage());
        }
    }
 
    public function deleteDataSource(string $dataSourceName): bool
    {
        $url = $this->config->getApiUrl('datasources', $dataSourceName);

        try {
            $this->client->delete($url);
            return true;
        } catch (RequestException $e) {
            throw new \RuntimeException("Failed to delete data source: " . $e->getMessage());
        }
    }
 
    public function getDataSource(string $dataSourceName): array
    {
        $url = $this->config->getApiUrl('datasources', $dataSourceName);

        try {
            $response = $this->client->get($url);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            throw new \RuntimeException("Failed to get data source details: " . $e->getMessage());
        }
    }
 
    public function listDataSources(): array
    {
        $url = $this->config->getApiUrl('datasources');

        try {
            $response = $this->client->get($url);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            throw new \RuntimeException("Failed to list data sources: " . $e->getMessage());
        }
    }


}
