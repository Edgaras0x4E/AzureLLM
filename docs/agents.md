 # Agents in AzureLLM

 ## Overview
AzureLLM provides an implementation for managing **Agents** using the Azure OpenAI API. Agents act as **virtual assistants** that can respond to messages, use tools, and interact with vector stores to retrieve relevant data.
  
This covers:
1. **Managing Agents** (Creating, Updating, Deleting)
2. **Handling Conversation Threads**
3. **Using Vector Stores for AI Agents**
4. **Method Reference Table**
5. **Code Examples**

# **Managing Agents**
## **Creating an Agent**
To create an agent, you need to specify:
- **Name** → Unique agent identifier.
- **Instructions** → Defines the agent's behavior.
- **Description** *(optional)* → Additional info.
- **Tools** *(optional)* → Enables external integrations (e.g., file search).
- **Vector Store ID** *(optional)* → Allows document retrieval.

```php
use Edgaras\AzureLLM\LLM; 
use Edgaras\AzureLLM\Agents\Agent;

$config = new LLM([
    'apiKey' => '<API-KEY>',
    'endpoint' => 'https://<DEPLOYMENT-NAME>.openai.azure.com',
    'deployment' => '<MODEL>',
    'apiVersion' => '2024-05-01-preview'
]);

$agent = new Agent($config);

$response = $agent->createAgent(
    "SupportAgent",
    "You provide customer support.",
    "AI-powered support assistant"
);

print_r($response);
```
## **Updating an Agent**

```php
$updateResponse = $agent->updateAgent(
    "asst_12345",
    ["instructions" => "Updated support instructions"]
);
print_r($updateResponse);
```

## **Retrieving an Agent**
```php
$agentDetails = $agent->getAgent("asst_12345");
print_r($agentDetails);
```

## **Listing All Agents**
```php
$allAgents = $agent->listAgents();
print_r($allAgents);
```

## **Deleting an Agent**
```php
$deleteResponse = $agent->deleteAgent("asst_12345");
print_r($deleteResponse);
```

# **Managing Conversation Threads**

AI Agents interact through conversation threads that store user messages and responses.

## **Creating a Thread**

```php
use Edgaras\AzureLLM\LLM; 
use Edgaras\AzureLLM\Agents\Thread;

$config = new LLM([
    'apiKey' => '<API-KEY>',
    'endpoint' => 'https://<DEPLOYMENT-NAME>.openai.azure.com',
    'deployment' => '<MODEL>',
    'apiVersion' => '2024-05-01-preview'
]);

$thread = new Thread($config);

$threadResponse = $thread->createThread();
print_r($threadResponse);
```

## **Sending a Message to a Thread**

```php
$threadId = "thrd_67890";

$messageResponse = $thread->addMessageToThread($threadId, "user", "Hello, how can you help?");
print_r($messageResponse);
```

## **Retrieving Messages from a Thread**

```php
$messages = $thread->getThreadMessages($threadId);
print_r($messages);
```

## **Running a Thread with an Agent**

```php
$agentId = "asst_12345";

$runResponse = $thread->runThread($threadId, $agentId);
print_r($runResponse);
```

## **Checking Thread Run Status**

```php
$runId = "run_98765";

$runStatus = $thread->getRunStatus($threadId, $runId);
print_r($runStatus);
```

## **Canceling a Thread Run**

```php
$cancelResponse = $thread->cancelRun($threadId, $runId);
print_r($cancelResponse);
```

## **Listing All Runs in a Thread**

```php
$allRuns = $thread->listThreadRuns($threadId);
print_r($allRuns);
```

# **Using Vector Stores with Agents**

Vector stores allow AI agents to search and retrieve document-based information.

## **Creating a Vector Store**

```php
use Edgaras\AzureLLM\LLM; 
use Edgaras\AzureLLM\Agents\VectorStore;

$config = new LLM([
    'apiKey' => '<API-KEY>',
    'endpoint' => 'https://<DEPLOYMENT-NAME>.openai.azure.com',
    'deployment' => '<MODEL>',
    'apiVersion' => '2024-05-01-preview'
]);

$vectorStore = new VectorStore($config);

$vectorResponse = $vectorStore->createVectorStore("DocumentStorage");
print_r($vectorResponse);
```

## **Uploading a File**
```php
$fileResponse = $vectorStore->uploadFile("/path/to/document.pdf");
print_r($fileResponse);
```

## **Attaching a File to a Vector Store**
```php
$fileId = "file_67890";
$vectorStoreId = "vs_98765";

$attachResponse = $vectorStore->attachFileToVectorStore($vectorStoreId, $fileId);
print_r($attachResponse);
```

## **Listing Files in a Vector Store**
```php
$fileList = $vectorStore->listFilesInVectorStore($vectorStoreId);
print_r($fileList);
```

## **Deleting a File from a Vector Store**

```php
$deleteFileResponse = $vectorStore->deleteFileFromVectorStore($vectorStoreId, $fileId);
print_r($deleteFileResponse);
```

# **Method Reference Table**

## **Agent Methods**

| Method | Required Parameters | Description |
|-------------|------|------|
| `createAgent($name, $instructions, $description, $tools, $vectorStoreId)` | `name (string)`, `instructions (string)`, `description (string, optional)`, `tools (array, optional)`, `vectorStoreId (string, optional)` | Create an AI assistant. |
| `updateAgent($agentId, $updates, $vectorStoreId)` | `agentId (string)`, `updates (array)`, `vectorStoreId (string, optional)` | Update an existing assistant. |
| `deleteAgent($agentId)` | `agentId (string)` | Delete an assistant. |
| `getAgent($agentId)` | `agentId (string)` | Retrieve assistant details. |
| `listAgents()` | - | List all assistants. | 

## **Thread Methods**

| Method | Required Parameters | Description |
|-------------|------|------|
| `createThread()` | - | Create a conversation thread. |
| `addMessageToThread($threadId, $role, $content)` | `threadId (string)`, `role (string)`, `content (string)` | Send a message to a thread. |
| `getThreadMessages($threadId)` | `threadId (string)` | Retrieve all messages in a thread. |
| `runThread($threadId, $agentId)` | `threadId (string)`, `agentId (string)` | Run a thread with an AI assistant. |
| `getRunStatus($threadId, $runId)` | `threadId (string)`, `runId (string)` | Check the status of a thread run. |
| `cancelRun($threadId, $runId)` | `threadId (string)`, `runId (string)` | Cancel an ongoing thread run. |
| `listThreadRuns($threadId)` | `threadId (string)` | List all runs for a thread. |


## **VectorStore Methods** 

| Method | Required Parameters | Description |
|-------------|------|------|
| `createVectorStore($name)` | `name (string)` | Create a vector store. |
| `listVectorStores()` | - | List all vector stores. |
| `deleteVectorStore($vectorStoreId)` | `vectorStoreId (string)` | Delete a vector store. |
| `uploadFile($filePath)` | `filePath (string)` | Upload a file to Azure OpenAI. |
| `attachFileToVectorStore($vectorStoreId, $fileId)` | `vectorStoreId (string)`, `fileId (string)` | Attach a file to a vector store. |
| `listFilesInVectorStore($vectorStoreId)` | `vectorStoreId (string)` | List files in a vector store. |
| `deleteFileFromVectorStore($vectorStoreId, $fileId)` | `vectorStoreId (string)`, `fileId (string)` | Delete a file from a vector store. |