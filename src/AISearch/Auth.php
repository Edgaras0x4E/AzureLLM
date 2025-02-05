<?php

namespace Edgaras\AzureLLM\AISearch;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;

class Auth
{
    private string $apiKey;
    private string $endpoint; 
    private string $apiVersion;

    public function __construct(array $config)
    {
        $this->validateConfig($config);

        $this->apiKey = $config['apiKey'];
        $this->endpoint = rtrim($config['endpoint'], '/'); 
        $this->apiVersion = $config['apiVersion'] ?? '';
    }

    public function getApiUrl(string $resource, string $name = ''): string
    {
        $base = "/$resource";
        if ($name !== '') {
            $base .= "/$name";
        }
        return $base . "?api-version=" . $this->getApiVersion();
    }


    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
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
    }
}