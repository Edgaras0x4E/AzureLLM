<?php

namespace Edgaras\AzureLLM\Tests;

use Edgaras\AzureLLM\AISearch\Auth;
use Edgaras\AzureLLM\AISearch\DataSource;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class DataSourceTest extends TestCase
{
    private $dataSource;
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

        $this->dataSource = new DataSource($auth);
        
        // Inject mocked client
        $reflection = new \ReflectionClass($this->dataSource);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->dataSource, $client);
    }

    public function testCreateDataSource()
    {
        $this->mockHandler->append(new Response(201, [], json_encode(['name' => 'test-data-source'])));

        $dataSourceConfig = [
            'type' => 'azureblob',
            'credentials' => ['connectionString' => 'test-connection-string'],
            'container' => ['name' => 'test-container']
        ];

        $response = $this->dataSource->createDataSource('test-data-source', $dataSourceConfig);

        $this->assertArrayHasKey('name', $response);
        $this->assertEquals('test-data-source', $response['name']);
    }

    public function testUpdateDataSource()
    {
        $this->mockHandler->append(new Response(200, [], json_encode(['message' => 'Data source updated successfully'])));

        $updatedConfig = [
            'type' => 'azureblob',
            'credentials' => ['connectionString' => 'updated-connection-string'],
            'container' => ['name' => 'updated-container']
        ];

        $response = $this->dataSource->updateDataSource('test-data-source', $updatedConfig);

        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('Data source updated successfully', $response['message']);
    }

    public function testGetDataSource()
    {
        $expectedResponse = [
            'name' => 'test-data-source',
            'type' => 'azureblob',
            'container' => ['name' => 'test-container']
        ];

        $this->mockHandler->append(new Response(200, [], json_encode($expectedResponse)));

        $response = $this->dataSource->getDataSource('test-data-source');

        $this->assertArrayHasKey('name', $response);
        $this->assertEquals('test-data-source', $response['name']);
    }

    public function testListDataSources()
    {
        $expectedResponse = [
            'value' => [
                ['name' => 'test-data-source'],
                ['name' => 'another-data-source']
            ]
        ];

        $this->mockHandler->append(new Response(200, [], json_encode($expectedResponse)));

        $response = $this->dataSource->listDataSources();

        $this->assertArrayHasKey('value', $response);
        $this->assertCount(2, $response['value']);
        $this->assertEquals('test-data-source', $response['value'][0]['name']);
        $this->assertEquals('another-data-source', $response['value'][1]['name']);
    }

    public function testDeleteDataSource()
    {
        $this->mockHandler->append(new Response(204));

        $response = $this->dataSource->deleteDataSource('test-data-source');

        $this->assertTrue($response);
    }

    public function testDeleteDataSourceThrowsException()
    {
        $this->mockHandler->append(new Response(400, [], json_encode(['error' => 'Failed to delete'])));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to delete data source');

        $this->dataSource->deleteDataSource('test-data-source');
    }
}
