# AzureLLM

PHP package for integrating and interacting with deployed Azure LLM models.

## 🚀 Changelog (v1.1.0)
### **New Features**
- **Added `Index` Class**: Manage Azure AI Search indexes (create, update, delete, retrieve).
- **Added `Indexer` Class**: Handle indexers to automate indexing from data sources.
- **Added `DataSource` Class**: Manage data sources for Azure AI Search.
- **Added Semantic Search Support**: Now indexes can have semantic configurations.


## Features
- Simplifies managing Azure OpenAI API settings such as API keys, endpoints, deployments, and API versions.
- Full support for **indexes, indexers, and data sources** in Azure AI Search.

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

## Method Reference Table

| Class | Method | Required Parameters | Description |
|--------|-------------|------|------|
| **AzureOpenAI** | `chatCompletions($messages, $options, $data_sources)` | `messages (array)` | Sends chat completion request to Azure OpenAI. |
| **Index** | `createIndex($name, $fields, $semanticConfig)` | `name (string)`, `fields (array)` | Creates a new index with optional semantic settings. |
| | `updateIndex($name, $fields, $semanticConfig)` | `name (string)`, `fields (array)` | Updates an existing index with fields and semantic settings. |
| | `deleteIndex($name)` | `name (string)` | Deletes an index. |
| | `getIndex($name)` | `name (string)` | Retrieves details of an index. |
| | `listIndexes()` | - | Lists all indexes in Azure AI Search. |
| | `getIndexStats($name)` | `name (string)` | Gets statistics for a specific index. |
| **Indexer** | `createIndexer($name, $config)` | `name (string)`, `config (array)` | Creates an indexer that links a data source to an index. |
| | `updateIndexer($name, $config)` | `name (string)`, `config (array)` | Updates an existing indexer. |
| | `deleteIndexer($name)` | `name (string)` | Deletes an indexer. |
| | `runIndexer($name)` | `name (string)` | Runs an indexer manually. |
| | `getIndexer($name)` | `name (string)` | Retrieves indexer details. |
| | `listIndexers()` | - | Lists all indexers. |
| | `getIndexerStatus($name)` | `name (string)` | Retrieves execution status of an indexer. |
| | `resetIndexer($name)` | `name (string)` | Resets an indexer (clears checkpoint and reindexes all data). |
| **DataSource** | `createDataSource($name, $config)` | `name (string)`, `config (array)` | Creates a new data source. |
| | `updateDataSource($name, $config)` | `name (string)`, `config (array)` | Updates a data source. |
| | `deleteDataSource($name)` | `name (string)` | Deletes a data source. |
| | `getDataSource($name)` | `name (string)` | Retrieves details of a data source. |
| | `listDataSources()` | - | Lists all data sources. |



## Useful links

- [Azure OpenAI Service REST API reference](https://learn.microsoft.com/en-us/azure/ai-services/openai/reference).
- [Azure AI Search documentation](https://learn.microsoft.com/en-us/azure/search/).