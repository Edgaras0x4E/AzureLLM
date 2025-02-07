<?php

namespace Edgaras\AzureLLM\Tests;

use Edgaras\AzureLLM\Agents\VectorStore;
use Edgaras\AzureLLM\LLM;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class VectorStoreTest extends TestCase
{
    private $vectorStore;
    private $mockHandler;

    protected function setUp(): void
    {
        $this->mockHandler = new MockHandler([]);

        $client = new Client([
            'handler' => HandlerStack::create($this->mockHandler)
        ]);

        $config = new LLM([
            'apiKey' => 'test-api-key',
            'endpoint' => 'https://test-api.openai.azure.com',
            'deployment' => 'test-deployment',   
            'apiVersion' => '2024-05-01-preview'
        ]);

        $this->vectorStore = new VectorStore($config);
        
        // Inject mocked client
        $reflection = new \ReflectionClass($this->vectorStore);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->vectorStore, $client);
    }

    public function testCreateVectorStore()
    {
        $this->mockHandler->append(new Response(201, [], json_encode(['id' => 'vs_12345'])));

        $response = $this->vectorStore->createVectorStore("TestVectorStore");

        $this->assertArrayHasKey('id', $response);
        $this->assertEquals('vs_12345', $response['id']);
    }

    public function testListVectorStores()
    {
        $expectedResponse = [
            'data' => [
                ['id' => 'vs_12345', 'name' => 'TestVectorStore'],
                ['id' => 'vs_67890', 'name' => 'AnotherVectorStore']
            ]
        ];

        $this->mockHandler->append(new Response(200, [], json_encode($expectedResponse)));

        $response = $this->vectorStore->listVectorStores();

        $this->assertArrayHasKey('data', $response);
        $this->assertCount(2, $response['data']);
        $this->assertEquals('TestVectorStore', $response['data'][0]['name']);
    }

    public function testDeleteVectorStore()
    {
        $this->mockHandler->append(new Response(204));

        $response = $this->vectorStore->deleteVectorStore("vs_12345");

        $this->assertEmpty($response);
    }

    public function testUploadFile()
    { 
        $tempFilePath = sys_get_temp_dir() . '/test.pdf';
        file_put_contents($tempFilePath, "Fake PDF content");

        $this->mockHandler->append(new Response(201, [], json_encode(['id' => 'file_98765'])));

        $response = $this->vectorStore->uploadFile($tempFilePath);

        $this->assertArrayHasKey('id', $response);
        $this->assertEquals('file_98765', $response['id']);
 
        unlink($tempFilePath);
    }

    public function testUploadFileThrowsExceptionForMissingFile()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("File not found: /invalid/path.pdf");

        $this->vectorStore->uploadFile("/invalid/path.pdf");
    }

    public function testAttachFileToVectorStore()
    {
        $this->mockHandler->append(new Response(200, [], json_encode(['status' => 'success'])));

        $response = $this->vectorStore->attachFileToVectorStore("vs_12345", "file_98765");

        $this->assertArrayHasKey('status', $response);
        $this->assertEquals('success', $response['status']);
    }

    public function testListFilesInVectorStore()
    {
        $expectedResponse = [
            'data' => [
                ['id' => 'file_98765', 'name' => 'test.pdf'],
                ['id' => 'file_54321', 'name' => 'another.pdf']
            ]
        ];

        $this->mockHandler->append(new Response(200, [], json_encode($expectedResponse)));

        $response = $this->vectorStore->listFilesInVectorStore("vs_12345");

        $this->assertArrayHasKey('data', $response);
        $this->assertCount(2, $response['data']);
        $this->assertEquals('test.pdf', $response['data'][0]['name']);
    }

    public function testDeleteFileFromVectorStore()
    {
        $this->mockHandler->append(new Response(204));

        $response = $this->vectorStore->deleteFileFromVectorStore("vs_12345", "file_98765");

        $this->assertEmpty($response);
    }

    public function testCreateVectorStoreWithInvalidResponse()
    {
        $this->mockHandler->append(new Response(400, [], json_encode(['error' => 'Invalid request'])));

        $response = $this->vectorStore->createVectorStore("InvalidVectorStore");

        $this->assertArrayHasKey('error', $response);
        $errorMessage = json_decode($response['response'], true)['error'] ?? null;
        $this->assertEquals('Invalid request', $errorMessage);
    }
}