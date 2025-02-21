<?php

namespace Edgaras\AzureLLM\Agents;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;
use Edgaras\AzureLLM\LLM;

class VectorStore
{
    private Client $client;
    private LLM $config;

    public function __construct(LLM $config)
    {
        $this->config = $config;
        $this->client = new Client(['base_uri' => $config->getEndpoint()]);
    }
 
    public function createVectorStore(string $name): array
    {
        $url = "/openai/vector_stores?api-version={$this->config->getApiVersion()}";

        $body = [
            'name' => $name 
        ];

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

    public function updateVectorStore(string $vectorStoreId, array $updateData): array
    {
        $url = "/openai/vector_stores/{$vectorStoreId}?api-version={$this->config->getApiVersion()}";

        try {
            $response = $this->client->post($url, [
                'headers' => [
                    'api-key' => $this->config->getApiKey(),
                    'Content-Type' => 'application/json',
                ],
                'json' => $updateData,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            return [
                'error' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ];
        }
    }
 
    public function listVectorStores(): array
    {
        $url = "/openai/vector_stores?api-version={$this->config->getApiVersion()}";

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

    public function getVectorStore(string $vectorStoreId): array
    {
        $url = "/openai/vector_stores/{$vectorStoreId}?api-version={$this->config->getApiVersion()}";

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
 
    public function deleteVectorStore(string $vectorStoreId): array
    {
        $url = "/openai/vector_stores/{$vectorStoreId}?api-version={$this->config->getApiVersion()}";

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
 
    public function uploadFile(string $filePath): array
    {
        $url = "/openai/files?api-version={$this->config->getApiVersion()}";

        if (!file_exists($filePath)) {
            throw new InvalidArgumentException("File not found: {$filePath}");
        }

        try {
            $response = $this->client->post($url, [
                'headers' => [
                    'api-key' => $this->config->getApiKey(),
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => fopen($filePath, 'r'),
                        'filename' => basename($filePath),
                    ],
                    [
                        'name' => 'purpose',
                        'contents' => 'assistants',
                    ],
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

    public function deleteUploadedFile(string $fileId): array
    {
        $url = "/openai/files/{$fileId}?api-version={$this->config->getApiVersion()}";

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
 
    public function attachFileToVectorStore(string $vectorStoreId, string $fileId): array
    {
        $url = "/openai/vector_stores/{$vectorStoreId}/files?api-version={$this->config->getApiVersion()}";

        $body = [
            'file_id' => $fileId
        ];

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
 
    public function listFilesInVectorStore(string $vectorStoreId): array
    {
        $url = "/openai/vector_stores/{$vectorStoreId}/files?api-version={$this->config->getApiVersion()}";

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
 
    public function deleteFileFromVectorStore(string $vectorStoreId, string $fileId): array
    {
        $url = "/openai/vector_stores/{$vectorStoreId}/files/{$fileId}?api-version={$this->config->getApiVersion()}";

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
}
