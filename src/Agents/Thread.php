<?php

namespace Edgaras\AzureLLM\Agents;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException; 
use Edgaras\AzureLLM\LLM;

class Thread 
{
    private Client $client;
    private LLM $config;

    public function __construct(LLM $config)
    {
        $this->config = $config;
        $this->client = new Client(['base_uri' => $config->getEndpoint()]);
    }
 
    public function createThread(): array
    {
        $url = "/openai/threads?api-version={$this->config->getApiVersion()}";

        try {
            $response = $this->client->post($url, [
                'headers' => [
                    'api-key' => $this->config->getApiKey(),
                    'Content-Type' => 'application/json',
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
 
    public function addMessageToThread(string $threadId, string $role, string $content, array $attachments = []): array
    {
        $url = "/openai/threads/{$threadId}/messages?api-version={$this->config->getApiVersion()}";

        $body = [
            'role' => $role,
            'content' => $content,
        ];

        if (!empty($attachments)) {
            $body['attachments'] = $attachments;
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
 
    public function getThreadMessages(string $threadId): array
    {
        $url = "/openai/threads/{$threadId}/messages?api-version={$this->config->getApiVersion()}";

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
 
    public function runThread(string $threadId, string $agentId): array
    {
        $url = "/openai/threads/{$threadId}/runs?api-version={$this->config->getApiVersion()}";

        $body = [
            'assistant_id' => $agentId,
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
 
    public function getRunStatus(string $threadId, string $runId): array
    {
        $url = "/openai/threads/{$threadId}/runs/{$runId}?api-version={$this->config->getApiVersion()}";

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
 
    public function cancelRun(string $threadId, string $runId): array
    {
        $url = "/openai/threads/{$threadId}/runs/{$runId}/cancel?api-version={$this->config->getApiVersion()}";

        try {
            $response = $this->client->post($url, [
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
 
    public function listThreadRuns(string $threadId): array
    {
        $url = "/openai/threads/{$threadId}/runs?api-version={$this->config->getApiVersion()}";

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


    public function deleteThread(string $threadId): array
    {
        $url = "/openai/threads/{$threadId}?api-version={$this->config->getApiVersion()}";

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
