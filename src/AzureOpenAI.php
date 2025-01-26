<?php

namespace Edgaras\AzureLLM;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;

class AzureOpenAI
{
    private Client $client;
    private LLM $config;

    public function __construct(LLM $config)
    {
        $this->config = $config;
        $this->client = new Client(['base_uri' => $config->getEndpoint()]);
    }

    public function chatCompletions(array $messages, array $options = [], array $data_sources = []): array
    {
        $this->validateMessages($messages);

        $url = "/openai/deployments/{$this->config->getDeployment()}/chat/completions?api-version={$this->config->getApiVersion()}";
        $body = [
            "messages" => $messages,
            "temperature" => $options['temperature'] ?? 0.7,
            "top_p" => $options['top_p'] ?? 0.95,
            "max_tokens" => $options['max_tokens'] ?? 1000,
            "stop" => $options['stop'] ?? null,
            "stream" => $options['stream'] ?? false,
        ];

        if (isset($data_sources) && count($data_sources)>0) {
            $this->validateDataSources($data_sources);
            $body['data_sources'] = $data_sources;
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

    private function validateMessages(array $messages)
    {
        if (empty($messages)) {
            throw new InvalidArgumentException("Messages array cannot be empty.");
        }
        foreach ($messages as $message) {
            if (!isset($message['role']) || !in_array($message['role'], ['system', 'user', 'assistant'])) {
                throw new InvalidArgumentException("Each message must have a valid 'role' ('system', 'user', 'assistant').");
            }
            if (!isset($message['content']) || !is_string($message['content']) || empty($message['content'])) {
                throw new InvalidArgumentException("Each message must have a non-empty 'content' field.");
            }
        }
    }

    private function validateDataSources(array $dataSources)
    {
        foreach ($dataSources as $dataSource) {
            if (!isset($dataSource['type']) || $dataSource['type'] !== 'azure_search') {
                throw new InvalidArgumentException("Each data source must have a type of 'azure_search'.");
            }
            $parameters = $dataSource['parameters'] ?? [];
            if (empty($parameters['endpoint']) || !filter_var($parameters['endpoint'], FILTER_VALIDATE_URL)) {
                throw new InvalidArgumentException("Data Source Endpoint must be a valid URL.");
            }
            if (empty($parameters['index_name']) || !is_string($parameters['index_name'])) {
                throw new InvalidArgumentException("Index Name must be a valid string.");
            }
        }
    }
}