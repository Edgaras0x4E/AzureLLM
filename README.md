# AzureLLM

PHP package for integrating and interacting with deployed Azure LLM models.

## Features
- Simplifies managing Azure OpenAI API settings such as API keys, endpoints, deployments, and API versions.
- Supports Azure Search integration for enhanced contextual completions.

## Requirements

PHP 8.1+

## Installation

1. Use the library via Composer:

```
composer require edgaras/azurellm
```

2. Include the Composer autoloader:

```php
require __DIR__ . '/vendor/autoload.php';
```

## Usage

### 1. Initialization
Set up your Azure OpenAI configuration:
 
```php
use Edgaras\AzureLLM\LLM;

$config = new LLM([
    'apiKey' => '<YOUR-API-KEY>',
    'endpoint' => 'https://<DEPLOYMENT>.openai.azure.com',
    'deployment' => '<MODEL-DEPLOYMENT-ID>',
    'apiVersion' => '<API-VERSION>'
]); 
 
```

### 2. Basic usage
Send requests to your Azure OpenAI deployment:

```php

use Edgaras\AzureLLM\LLM;
use Edgaras\AzureLLM\AzureOpenAI;

$config = new LLM([
    'apiKey' => '<YOUR-API-KEY>',
    'endpoint' => 'https://<DEPLOYMENT>.openai.azure.com',
    'deployment' => '<MODEL-DEPLOYMENT-ID>',
    'apiVersion' => '<API-VERSION>'
]); 

$azureLLM = new AzureOpenAI($config);

$inputMessages = [
    ['role' => 'system', 'content' => 'You are a helpful assistant.'],
    ['role' => 'user', 'content' => 'What is the capital of Lithuania?']
];

$options = [
    "temperature" => 0.7,
    "top_p" => 0.95,
    "max_tokens" => 150 
];

$response = $azureLLM->chatCompletions($inputMessages, $options);

```

### 3. Use with Azure AI Search
Combine the Azure OpenAI service with Azure Search for contextual completions:

```php

use Edgaras\AzureLLM\LLM;
use Edgaras\AzureLLM\AzureOpenAI;

$inputMessages = [
    ['role' => 'system', 'content' => 'You are a helpful assistant.'],
    ['role' => 'user', 'content' => 'Summarize your knowledgebase']
];

$options = [
    "temperature" => 0.7,
    "top_p" => 0.95,
    "max_tokens" => 150 
];

$data_sources = [[
    "type" => "azure_search",
    "parameters" => [
        "filter" => null,
        "endpoint" => 'https://<SEARCH-DEPLOYMENT>.search.windows.net',
        "index_name" => '<SEARCH-INDEX-NAME>',
        "authentication" => [
            "type" => "api_key",
            "key" => '<SEARCH-API-KEY>'
        ],
    ],
]];

$response = $azureLLM->chatCompletions($inputMessages, $options, $data_sources);

```

## Useful links

- [Azure OpenAI Service REST API reference](https://learn.microsoft.com/en-us/azure/ai-services/openai/reference).
- [Azure AI Search documentation](https://learn.microsoft.com/en-us/azure/search/).