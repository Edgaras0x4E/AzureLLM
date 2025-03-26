<?php

namespace Edgaras\AzureLLM;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;

class DeepSeek
{
    private Client $client;
    private LLM $config;

    public function __construct(LLM $config)
    {
        $this->config = $config;
        $this->client = new Client(['base_uri' => $config->getEndpoint()]);
    }

    public function chatCompletions(array $messages, array $options = []): array
    {
        $this->validateMessages($messages);

        $url = "/models/chat/completions?api-version={$this->config->getApiVersion()}";
        $body = [
            "model" => $this->config->getDeployment(),   
            "messages" => $messages,
            "temperature" => $options['temperature'] ?? 0.7,
            "top_p" => $options['top_p'] ?? 0.95,
            "max_tokens" => $options['max_tokens'] ?? 1000,
            "stop" => $options['stop'] ?? null,
            "stream" => $options['stream'] ?? false,
        ];
  
        try {
            $response = $this->client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->config->getApiKey(),
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
}
