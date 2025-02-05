<?php

namespace Edgaras\AzureLLM\AISearch;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;

class Index {

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

    public function createIndex(string $indexName, array $fields, array $semanticConfig = []): array
    {
        $url = $this->config->getApiUrl('indexes');
        $body = [
            'name' => $indexName,
            'fields' => $fields
        ];

        if (!empty($semanticConfig)) {
            $body['semantic'] = $semanticConfig;
        }

        try {
            $response = $this->client->post($url, ['body' => json_encode($body)]);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            throw new \RuntimeException("Failed to create index: " . $e->getMessage());
        }
    }
 
    public function updateIndex(string $indexName, array $fields, array $semanticConfig = []): array
    {
        $url = $this->config->getApiUrl('indexes', $indexName);
        $body = [
            'name' => $indexName,
            'fields' => $fields
        ];

        if (!empty($semanticConfig)) {
            $body['semantic'] = $semanticConfig;  
        }

        try {
            $response = $this->client->put($url, ['body' => json_encode($body)]);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            throw new \RuntimeException("Failed to update index: " . $e->getMessage());
        }
    }
 
    public function deleteIndex(string $indexName): bool
    {
        $url = $this->config->getApiUrl('indexes', $indexName);

        try {
            $this->client->delete($url);
            return true;
        } catch (RequestException $e) {
            throw new \RuntimeException("Failed to delete index: " . $e->getMessage());
        }
    }

    public function getIndex(string $indexName): array
    {
        $url = $this->config->getApiUrl('indexes', $indexName);

        try {
            $response = $this->client->get($url);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            throw new \RuntimeException("Failed to get index details: " . $e->getMessage());
        }
    }
 
    public function listIndexes(): array
    {
        $url = $this->config->getApiUrl('indexes');

        try {
            $response = $this->client->get($url);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            throw new \RuntimeException("Failed to list indexes: " . $e->getMessage());
        }
    }
 
    public function getIndexStats(string $indexName): array
    {
        $url = $this->config->getApiUrl('indexes', "$indexName/stats");

        try {
            $response = $this->client->get($url);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            throw new \RuntimeException("Failed to get index statistics: " . $e->getMessage());
        }
    }

    public function searchIndex(string $indexName, string $query, int $top = 10, array $searchParams = []): array
    {
        $url = $this->config->getApiUrl("indexes/$indexName/docs/search");

        $body = array_merge([
            'search' => $query,
            'top' => $top,  
        ], $searchParams);

        try {
            $response = $this->client->post($url, [
                'headers' => [
                    'api-key' => $this->config->getApiKey(),
                    'Content-Type' => 'application/json'
                ],
                'json' => $body
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            throw new \RuntimeException("Failed to search index: " . $e->getMessage());
        }
    }

}
