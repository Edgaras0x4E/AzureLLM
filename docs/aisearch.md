# AI Search in AzureLLM

## Overview
AzureLLM provides an integration with **Azure AI Search**, allowing you to:
- **Manage Indexes** (Create, Update, Delete, Retrieve, Search)
- **Manage Indexers** (Automate data indexing)
- **Manage Data Sources** (Store data for indexing)
- **Perform AI-powered Semantic Search**

This covers:
1. **Managing AI Search Indexes**
2. **Handling AI Search Indexers**
3. **Managing Data Sources**
4. **Method Reference Table**
5. **Code Examples**

# **Managing AI Search Indexes**
Indexes store structured data for **full-text and semantic search**.

## **Initializing AI Search Configuration**
```php
use Edgaras\AzureLLM\AISearch\Auth;

$config = new Auth([
    'apiKey' => '<YOUR-API-KEY>',
    'endpoint' => 'https://<YOUR-SEARCH-SERVICE>.search.windows.net',
    'apiVersion' => '2023-07-01-Preview'
]); 
```

## Creating an Index
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
```

## Listing All Indexes
```php
$indexes = $indexService->listIndexes();
print_r($indexes);
```
## Retrieving an Index
```php
$indexDetails = $indexService->getIndex('test-index');
print_r($indexDetails);
```
## Updating an Index
```php
$updateResponse = $indexService->updateIndex('test-index', $fields);
print_r($updateResponse);
```
## Deleting an Index
```php
$deleteResponse = $indexService->deleteIndex('test-index');
print_r($deleteResponse);
```
## Searching an Index
```php
$searchResults = $indexService->searchIndex('test-index', 'query text');
print_r($searchResults);
```
# Managing AI Search Indexers

Indexers automate importing data from sources like Azure Blob Storage.

## Creating an Indexer
```php
use Edgaras\AzureLLM\AISearch\Indexer;

$indexerService = new Indexer($config);

// Define Indexer Configuration
$indexerConfig = [
    'dataSourceName' => 'test-data-source',
    'targetIndexName' => 'test-index',
    'schedule' => ['interval' => 'P1D']
];

// Create Indexer
$indexerService->createIndexer('test-indexer', $indexerConfig);
```
## Running an Indexer Manually
```php
$indexerService->runIndexer('test-indexer');
```
## Listing All Indexers
```php
$indexers = $indexerService->listIndexers();
print_r($indexers);
```
## Retrieving an Indexer
```php
$indexerDetails = $indexerService->getIndexer('test-indexer');
print_r($indexerDetails);
```
## Updating an Indexer
```php
$updateIndexer = $indexerService->updateIndexer('test-indexer', $indexerConfig);
print_r($updateIndexer);
```
## Checking Indexer Status
```php
$status = $indexerService->getIndexerStatus('test-indexer');
print_r($status);
```
## Resetting an Indexer
```php
$indexerService->resetIndexer('test-indexer');
```
## Deleting an Indexer
```php
$deleteResponse = $indexerService->deleteIndexer('test-indexer');
print_r($deleteResponse);
```
# Managing AI Search Data Sources
 
A Data Source stores information that the indexer reads from.

## Creating a Data Source
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
## Listing All Data Sources
```php
$dataSources = $dataSourceService->listDataSources();
print_r($dataSources);
```
## Retrieving a Data Source
```php
$dataSourceDetails = $dataSourceService->getDataSource('test-data-source');
print_r($dataSourceDetails);
```
## Updating a Data Source
```php
$updateResponse = $dataSourceService->updateDataSource('test-data-source', $dataSourceConfig);
print_r($updateResponse);
```
## Deleting a Data Source
```php
$deleteResponse = $dataSourceService->deleteDataSource('test-data-source');
print_r($deleteResponse);
```
# Method Reference Table

## Index Methods

| Method | Required Parameters | Description |
|-------------|------|------|
| `createIndex($name, $fields, $semanticConfig)` | `name (string)`, `fields (array)` | Creates a new index with optional semantic settings. |
| `updateIndex($name, $fields, $semanticConfig)` | `name (string)`, `fields (array)` | Updates an existing index with fields and semantic settings. |
| `deleteIndex($name)` | `name (string)` | Deletes an index. |
| `getIndex($name)` | `name (string)` | Retrieves details of an index. |
| `listIndexes()` | - | Lists all indexes in Azure AI Search. |
| `getIndexStats($name)` | `name (string)` | Gets statistics for a specific index. |
| `searchIndex($indexName, $query, $top, $searchParams)` | `indexName (string)`, `query (string)` | Performs a search query on an index. |

## Indexer Methods

| Method | Required Parameters | Description |
|-------------|------|------|
| `createIndexer($name, $config)` | `name (string)`, `config (array)` | Creates an indexer that links a data source to an index. |
| `updateIndexer($name, $config)` | `name (string)`, `config (array)` | Updates an existing indexer. |
| `deleteIndexer($name)` | `name (string)` | Deletes an indexer. |
| `runIndexer($name)` | `name (string)` | Runs an indexer manually. |
| `getIndexer($name)` | `name (string)` | Retrieves indexer details. |
| `listIndexers()` | - | Lists all indexers. |
| `getIndexerStatus($name)` | `name (string)` | Retrieves execution status of an indexer. |
| `resetIndexer($name)` | `name (string)` | Resets an indexer (clears checkpoint and reindexes all data). |

## Data Source Methods

| Method | Required Parameters | Description |
|-------------|------|------|
| `createDataSource($name, $config)` | `name (string)`, `config (array)` | Creates a new data source. |
| `updateDataSource($name, $config)` | `name (string)`, `config (array)` | Updates a data source. |
| `deleteDataSource($name)` | `name (string)` | Deletes a data source. |
| `getDataSource($name)` | `name (string)` | Retrieves details of a data source. |
| `listDataSources()` | - | Lists all data sources. |