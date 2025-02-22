# AzureLLM

PHP package for integrating and interacting with deployed Azure LLM models.

## 🚀 Changelog (v1.2.5)
### **New Features**
- **Added `Agent` Class**: Manage Azure AI Assistants (create, update, delete, retrieve, list).
- **Added `Thread` Class**: Handle conversation threads (create, send messages, run, manage runs).
- **Added `VectorStore` Class**: Manage vector stores and file attachments. 

---

## 📌 Documentation 
- 📖 **[AI Agents](docs/agents.md)**
- 🔍 **[AI Search](docs/aisearch.md)**

---

## Features 
- Simplifies managing **Azure OpenAI API** settings such as API keys, endpoints, deployments, and API versions.
- Full support for **Agents, Threads, and Vector Stores**.
- Integrated **Azure AI Search** functionalities.

## Requirements

- PHP 8.1+
- Composer

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

$config = new LLM([
    'apiKey' => '<YOUR-API-KEY>',
    'endpoint' => 'https://<DEPLOYMENT>.openai.azure.com',
    'deployment' => '<MODEL-DEPLOYMENT-ID>',
    'apiVersion' => '<API-VERSION>'
]); 

$azureLLM = new AzureOpenAI($config);

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

### 4. Initialize AI Search Configuration

```php

use Edgaras\AzureLLM\AISearch\Auth;

$config = new Auth([
    'apiKey' => '<YOUR-API-KEY>',
    'endpoint' => 'https://<YOUR-SEARCH-SERVICE>.search.windows.net',
    'apiVersion' => '2023-07-01-Preview'
]); 

```

### 5. Manage AI Search Indexes

```php
use Edgaras\AzureLLM\AISearch\Index;

$indexService = new Index($config);

// Define Index Fields
$fields = [
    ['name' => 'id', 'type' => 'Edm.String', 'key' => true],
    ['name' => 'content', 'type' => 'Edm.String', 'searchable' => true, 'retrievable' => true]
];

// Create Index
$indexService->createIndex('test-index', $fields);

// List all indexes
$indexes = $indexService->listIndexes();
print_r($indexes);

```

### 6. Manage AI Search Indexers

```php
use Edgaras\AzureLLM\AISearch\Indexer;

$indexerService = new Indexer($config);

// Define Indexer
$indexerConfig = [
    'dataSourceName' => 'test-data-source',
    'targetIndexName' => 'test-index',
    'schedule' => ['interval' => 'P1D']
];

// Create Indexer
$indexerService->createIndexer('test-indexer', $indexerConfig);

// Run Indexer Manually
$indexerService->runIndexer('test-indexer');

```

### 7. Manage AI Search Data Sources

```php
use Edgaras\AzureLLM\AISearch\DataSource;

$dataSourceService = new DataSource($config);

// Define Data Source Configuration
$dataSourceConfig = [
    'type' => 'azureblob',
    'credentials' => ['connectionString' => '<YOUR-STORAGE-CONNECTION-STRING>'],
    'container' => ['name' => 'your-container']
];

// Create Data Source
$dataSourceService->createDataSource('test-data-source', $dataSourceConfig);
```
[Full AI Search Docs](docs/aisearch.md)


### 8. Agents & Threads
```php
use Edgaras\AzureLLM\LLM; 
use Edgaras\AzureLLM\Agents\Agent;
use Edgaras\AzureLLM\Agents\Thread;

// Initialize
$config = new LLM([
    'apiKey' => '<API-KEY>',
    'endpoint' => 'https://<DEPLOYMENT-NAME>.openai.azure.com',
    'deployment' => '<MODEL>',
    'apiVersion' => '2024-05-01-preview'
]);

$agent = new Agent($config);
$thread = new Thread($config);

// Create an Agent
$agentResponse = $agent->createAgent("SupportBot", "Assist users with support queries.");
$agentId = $agentResponse['id'];

// Start a conversation thread
$threadResponse = $thread->createThread();
$threadId = $threadResponse['id'];

// Send a message
$thread->addMessageToThread($threadId, "user", "How do I reset my password?");

// Run the AI Assistant on the thread
$thread->runThread($threadId, $agentId);
```
[Full AI Agents Docs](docs/agents.md)

## Useful links

- [Azure OpenAI Service REST API reference](https://learn.microsoft.com/en-us/azure/ai-services/openai/reference).
- [Azure AI Search documentation](https://learn.microsoft.com/en-us/azure/search/).
- [What is Azure AI Agent Service?](https://learn.microsoft.com/en-us/azure/ai-services/agents/overview).