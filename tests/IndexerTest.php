<?php

namespace Edgaras\AzureLLM\Tests;

use Edgaras\AzureLLM\AISearch\Auth;
use Edgaras\AzureLLM\AISearch\Indexer;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class IndexerTest extends TestCase
{
    private $indexerService;
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

        $this->indexerService = new Indexer($auth);

        // Inject mocked client
        $reflection = new \ReflectionClass($this->indexerService);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->indexerService, $client);
    }

    public function testCreateIndexer()
    {
        $this->mockHandler->append(new Response(201, [], json_encode(['name' => 'test-indexer'])));

        $indexerConfig = [
            'dataSourceName' => 'test-data-source',
            'targetIndexName' => 'test-index'
        ];

        $response = $this->indexerService->createIndexer('test-indexer', $indexerConfig);

        $this->assertArrayHasKey('name', $response);
        $this->assertEquals('test-indexer', $response['name']);
    }

    public function testUpdateIndexer()
    {
        $this->mockHandler->append(new Response(200, [], json_encode(['message' => 'Indexer updated successfully'])));

        $indexerConfig = [
            'dataSourceName' => 'test-data-source',
            'targetIndexName' => 'test-index',
            'schedule' => ['interval' => 'P1D']
        ];

        $response = $this->indexerService->updateIndexer('test-indexer', $indexerConfig);

        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('Indexer updated successfully', $response['message']);
    }

    public function testGetIndexer()
    {
        $expectedResponse = [
            'name' => 'test-indexer',
            'dataSourceName' => 'test-data-source',
            'targetIndexName' => 'test-index'
        ];

        $this->mockHandler->append(new Response(200, [], json_encode($expectedResponse)));

        $response = $this->indexerService->getIndexer('test-indexer');

        $this->assertArrayHasKey('name', $response);
        $this->assertEquals('test-indexer', $response['name']);
    }

    public function testListIndexers()
    {
        $expectedResponse = [
            'value' => [
                ['name' => 'test-indexer'],
                ['name' => 'another-indexer']
            ]
        ];

        $this->mockHandler->append(new Response(200, [], json_encode($expectedResponse)));

        $response = $this->indexerService->listIndexers();

        $this->assertArrayHasKey('value', $response);
        $this->assertCount(2, $response['value']);
        $this->assertEquals('test-indexer', $response['value'][0]['name']);
        $this->assertEquals('another-indexer', $response['value'][1]['name']);
    }

    public function testGetIndexerStatus()
    {
        $expectedResponse = [
            'status' => 'running',
            'lastResult' => 'success'
        ];

        $this->mockHandler->append(new Response(200, [], json_encode($expectedResponse)));

        $response = $this->indexerService->getIndexerStatus('test-indexer');

        $this->assertArrayHasKey('status', $response);
        $this->assertEquals('running', $response['status']);
    }

    public function testRunIndexer()
    {
        $this->mockHandler->append(new Response(202));

        $response = $this->indexerService->runIndexer('test-indexer');

        $this->assertTrue($response);
    }

    public function testResetIndexer()
    {
        $this->mockHandler->append(new Response(202));

        $response = $this->indexerService->resetIndexer('test-indexer');

        $this->assertTrue($response);
    }

    public function testDeleteIndexer()
    {
        $this->mockHandler->append(new Response(204));

        $response = $this->indexerService->deleteIndexer('test-indexer');

        $this->assertTrue($response);
    }

    public function testDeleteIndexerThrowsException()
    {
        $this->mockHandler->append(new Response(400, [], json_encode(['error' => 'Failed to delete'])));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to delete indexer');

        $this->indexerService->deleteIndexer('test-indexer');
    }
}
