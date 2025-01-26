<?php

namespace Edgaras\AzureLLM;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;

class LLM
{
    private string $apiKey;
    private string $endpoint;
    private string $deployment;
    private string $apiVersion;

    public function __construct(array $config)
    {
        $this->validateConfig($config);

        $this->apiKey = $config['apiKey'];
        $this->endpoint = rtrim($config['endpoint'], '/');
        $this->deployment = $config['deployment'];
        $this->apiVersion = $config['apiVersion'] ?? '';
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function getDeployment(): string
    {
        return $this->deployment;
    }

    public function getApiVersion(): string
    {
        return $this->apiVersion;
    }

    private function validateConfig(array $config): void
    {
        if (empty($config['apiKey']) || !is_string($config['apiKey'])) {
            throw new InvalidArgumentException("API Key must be a non-empty string.");
        }
        if (empty($config['endpoint']) || !filter_var($config['endpoint'], FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException("Endpoint must be a valid URL.");
        }
        if (empty($config['deployment']) || !is_string($config['deployment'])) {
            throw new InvalidArgumentException("Deployment must be a non-empty string.");
        }
    }
}