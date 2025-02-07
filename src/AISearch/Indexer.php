<?php

namespace Edgaras\AzureLLM\AISearch;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;

class Indexer {

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
 
    public function createIndexer(string $indexerName, array $indexerConfig): array
    {
        $url = $this->config->getApiUrl('indexers');
        $body = json_encode(array_merge(['name' => $indexerName], $indexerConfig));

        try {
            $response = $this->client->post($url, ['body' => $body]);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            throw new \RuntimeException("Failed to create indexer: " . $e->getMessage());
        }
    }
 
    public function updateIndexer(string $indexerName, array $indexerConfig): array
    {
        $url = $this->config->getApiUrl('indexers', $indexerName);
        $body = json_encode(array_merge(['name' => $indexerName], $indexerConfig));

        try {
            $response = $this->client->put($url, ['body' => $body]);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            throw new \RuntimeException("Failed to update indexer: " . $e->getMessage());
        }
    }
 
    public function deleteIndexer(string $indexerName): bool
    {
        $url = $this->config->getApiUrl('indexers', $indexerName);

        try {
            $this->client->delete($url);
            return true;
        } catch (RequestException $e) {
            throw new \RuntimeException("Failed to delete indexer: " . $e->getMessage());
        }
    }
 
    public function runIndexer(string $indexerName): bool
    {
        $url = $this->config->getApiUrl('indexers', "$indexerName/run");

        try {
            $this->client->post($url);
            return true;
        } catch (RequestException $e) {
            throw new \RuntimeException("Failed to run indexer: " . $e->getMessage());
        }
    }
 
    public function getIndexer(string $indexerName): array
    {
        $url = $this->config->getApiUrl('indexers', $indexerName);

        try {
            $response = $this->client->get($url);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            throw new \RuntimeException("Failed to get indexer details: " . $e->getMessage());
        }
    }
 
    public function listIndexers(): array
    {
        $url = $this->config->getApiUrl('indexers');

        try {
            $response = $this->client->get($url);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            throw new \RuntimeException("Failed to list indexers: " . $e->getMessage());
        }
    }
 
    public function getIndexerStatus(string $indexerName): array
    {
        $url = $this->config->getApiUrl('indexers', "$indexerName/status");

        try {
            $response = $this->client->get($url);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            throw new \RuntimeException("Failed to get indexer status: " . $e->getMessage());
        }
    }
 
    public function resetIndexer(string $indexerName): bool
    {
        $url = $this->config->getApiUrl('indexers', "$indexerName/reset");

        try {
            $this->client->post($url);
            return true;
        } catch (RequestException $e) {
            throw new \RuntimeException("Failed to reset indexer: " . $e->getMessage());
        }
    }

}
 