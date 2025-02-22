<?php

namespace Edgaras\AzureLLM\Agents;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;
use Edgaras\AzureLLM\LLM;

class Agent
{
    private Client $client;
    private LLM $config;

    public function __construct(LLM $config)
    {
        $this->config = $config;
        $this->client = new Client(['base_uri' => $config->getEndpoint()]);
    }
 
    public function createAgent(string $name, string $instructions, string $description = '', array $tools = [], ?string $vectorStoreId = null, array $additionalParams = []): array 
    {
        $url = "/openai/assistants?api-version={$this->config->getApiVersion()}";
 
        $body = [
            'name' => $name,
            'instructions' => $instructions,
            'description' => $description,
            'model' => $this->config->getDeployment(),
            'tools' => $tools
        ];

        $body = array_merge($body, $additionalParams);
 
        if ($vectorStoreId) {
            $body['tools'][] = ['type' => 'file_search'];
            $body['tool_resources'] = [
                'file_search' => [
                    'vector_store_ids' => [$vectorStoreId]
                ]
            ];
        }

        try {
            $response = $this->client->post($url, [
                'headers' => [
                    'api-key' => $this->config->getApiKey(),
                    'Content-Type' => 'application/json',
                ],
                'json' => $body,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            return [
                'error' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ];
        }
    }
 
    public function updateAgent(string $agentId, array $updates, ?string $vectorStoreId = null): array 
    { 
        $url = "/openai/assistants/{$agentId}?api-version={$this->config->getApiVersion()}";
 
        if ($vectorStoreId) {
            $updates['tools'][] = ['type' => 'file_search'];
            $updates['tool_resources'] = [
                'file_search' => [
                    'vector_store_ids' => [$vectorStoreId]
                ]
            ];
        }

        try {
            $response = $this->client->post($url, [
                'headers' => [
                    'api-key' => $this->config->getApiKey(),
                    'Content-Type' => 'application/json',
                ],
                'json' => $updates,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            return [
                'error' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ];
        }
    }
 
    public function deleteAgent(string $agentId): array
    {
        $url = "/openai/assistants/{$agentId}?api-version={$this->config->getApiVersion()}";

        try {
            $response = $this->client->delete($url, [
                'headers' => [
                    'api-key' => $this->config->getApiKey(),
                ],
            ]);

            if ($response->getStatusCode() === 204) {
                return [];  
            } 

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            return [
                'error' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ];
        }
    }
 
    public function getAgent(string $agentId): array
    {
        $url = "/openai/assistants/{$agentId}?api-version={$this->config->getApiVersion()}";

        try {
            $response = $this->client->get($url, [
                'headers' => [
                    'api-key' => $this->config->getApiKey(),
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            return [
                'error' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ];
        }
    }
 
    public function listAgents(): array
    {
        $url = "/openai/assistants?api-version={$this->config->getApiVersion()}";

        try {
            $response = $this->client->get($url, [
                'headers' => [
                    'api-key' => $this->config->getApiKey(),
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            return [
                'error' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ];
        }
    }
}
