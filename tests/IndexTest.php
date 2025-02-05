<?php

namespace Edgaras\AzureLLM\Tests;

use Edgaras\AzureLLM\AISearch\Auth;
use Edgaras\AzureLLM\AISearch\Index;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    private $indexService;
    private $mockHandler;

    protected function setUp(): void
    {
        $this->mockHandler = new MockHandler([]);

        $client = new Client([
            'handler' => HandlerStack::create($this->mockHandler)
        ]);

        $auth = new Auth([
            'apiKey' => 'test-api-key',
            'endpoint' => 'https://test-search-instance.search.windows.net',
            'apiVersion' => '2023-07-01-Preview'
        ]);

        $this->indexService = new Index($auth);

        // Inject mocked client
        $reflection = new \ReflectionClass($this->indexService);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->indexService, $client);
    }

    public function testCreateIndex()
    {
        $this->mockHandler->append(new Response(201, [], json_encode(['name' => 'test-index'])));

        $fields = [
            ['name' => 'id', 'type' => 'Edm.String', 'key' => true],
            ['name' => 'content', 'type' => 'Edm.String', 'searchable' => true, 'retrievable' => true]
        ];

        $response = $this->indexService->createIndex('test-index', $fields);

        $this->assertArrayHasKey('name', $response);
        $this->assertEquals('test-index', $response['name']);
    }

    public function testUpdateIndex()
    {
        $this->mockHandler->append(new Response(200, [], json_encode(['message' => 'Index updated successfully'])));

        $fields = [
            ['name' => 'id', 'type' => 'Edm.String', 'key' => true],
            ['name' => 'content', 'type' => 'Edm.String', 'searchable' => true, 'retrievable' => true],
            ['name' => 'new_field', 'type' => 'Edm.String', 'searchable' => true, 'retrievable' => true]
        ];

        $response = $this->indexService->updateIndex('test-index', $fields);

        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('Index updated successfully', $response['message']);
    }

    public function testGetIndex()
    {
        $expectedResponse = [
            'name' => 'test-index',
            'fields' => [
                ['name' => 'id', 'type' => 'Edm.String', 'key' => true],
                ['name' => 'content', 'type' => 'Edm.String']
            ]
        ];

        $this->mockHandler->append(new Response(200, [], json_encode($expectedResponse)));

        $response = $this->indexService->getIndex('test-index');

        $this->assertArrayHasKey('name', $response);
        $this->assertEquals('test-index', $response['name']);
    }

    public function testListIndexes()
    {
        $expectedResponse = [
            'value' => [
                ['name' => 'test-index'],
                ['name' => 'another-index']
            ]
        ];

        $this->mockHandler->append(new Response(200, [], json_encode($expectedResponse)));

        $response = $this->indexService->listIndexes();

        $this->assertArrayHasKey('value', $response);
        $this->assertCount(2, $response['value']);
        $this->assertEquals('test-index', $response['value'][0]['name']);
        $this->assertEquals('another-index', $response['value'][1]['name']);
    }

    public function testGetIndexStats()
    {
        $expectedResponse = [
            'documentCount' => 100,
            'storageSize' => 10240
        ];

        $this->mockHandler->append(new Response(200, [], json_encode($expectedResponse)));

        $response = $this->indexService->getIndexStats('test-index');

        $this->assertArrayHasKey('documentCount', $response);
        $this->assertEquals(100, $response['documentCount']);
    }

    public function testDeleteIndex()
    {
        $this->mockHandler->append(new Response(204));

        $response = $this->indexService->deleteIndex('test-index');

        $this->assertTrue($response);
    }

    public function testDeleteIndexThrowsException()
    {
        $this->mockHandler->append(new Response(400, [], json_encode(['error' => 'Failed to delete'])));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to delete index');

        $this->indexService->deleteIndex('test-index');
    }
}
