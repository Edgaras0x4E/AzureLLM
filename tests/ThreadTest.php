<?php

namespace Edgaras\AzureLLM\Tests;

use Edgaras\AzureLLM\Agents\Thread;
use Edgaras\AzureLLM\LLM;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ThreadTest extends TestCase
{
    private $thread;
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

        $this->thread = new Thread($config);
         
        $reflection = new \ReflectionClass($this->thread);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->thread, $client);
    }

    public function testCreateThread()
    {
        $this->mockHandler->append(new Response(201, [], json_encode(['id' => 'thrd_12345'])));

        $response = $this->thread->createThread();

        $this->assertArrayHasKey('id', $response);
        $this->assertEquals('thrd_12345', $response['id']);
    }

    public function testAddMessageToThread()
    {
        $this->mockHandler->append(new Response(201, [], json_encode(['id' => 'msg_56789'])));

        $response = $this->thread->addMessageToThread("thrd_12345", "user", "Hello, assistant!");

        $this->assertArrayHasKey('id', $response);
        $this->assertEquals('msg_56789', $response['id']);
    }

    public function testGetThreadMessages()
    {
        $expectedResponse = [
            'data' => [
                ['id' => 'msg_56789', 'role' => 'user', 'content' => 'Hello, assistant!'],
                ['id' => 'msg_67890', 'role' => 'assistant', 'content' => 'Hello! How can I assist?']
            ]
        ];

        $this->mockHandler->append(new Response(200, [], json_encode($expectedResponse)));

        $response = $this->thread->getThreadMessages("thrd_12345");

        $this->assertArrayHasKey('data', $response);
        $this->assertCount(2, $response['data']);
        $this->assertEquals('Hello, assistant!', $response['data'][0]['content']);
    }

    public function testRunThread()
    {
        $this->mockHandler->append(new Response(201, [], json_encode(['id' => 'run_98765'])));

        $response = $this->thread->runThread("thrd_12345", "asst_54321");

        $this->assertArrayHasKey('id', $response);
        $this->assertEquals('run_98765', $response['id']);
    }

    public function testGetRunStatus()
    {
        $expectedResponse = ['id' => 'run_98765', 'status' => 'completed'];

        $this->mockHandler->append(new Response(200, [], json_encode($expectedResponse)));

        $response = $this->thread->getRunStatus("thrd_12345", "run_98765");

        $this->assertArrayHasKey('status', $response);
        $this->assertEquals('completed', $response['status']);
    }

    public function testCancelRun()
    {
        $this->mockHandler->append(new Response(200, [], json_encode(['status' => 'cancelled'])));

        $response = $this->thread->cancelRun("thrd_12345", "run_98765");

        $this->assertArrayHasKey('status', $response);
        $this->assertEquals('cancelled', $response['status']);
    }

    public function testListThreadRuns()
    {
        $expectedResponse = [
            'data' => [
                ['id' => 'run_98765', 'status' => 'completed'],
                ['id' => 'run_54321', 'status' => 'in_progress']
            ]
        ];

        $this->mockHandler->append(new Response(200, [], json_encode($expectedResponse)));

        $response = $this->thread->listThreadRuns("thrd_12345");

        $this->assertArrayHasKey('data', $response);
        $this->assertCount(2, $response['data']);
        $this->assertEquals('completed', $response['data'][0]['status']);
    }

    public function testCreateThreadWithInvalidResponse()
    {
        $this->mockHandler->append(new Response(400, [], json_encode(['error' => 'Invalid request'])));

        $response = $this->thread->createThread();

        $this->assertArrayHasKey('error', $response);
        $errorMessage = json_decode($response['response'], true)['error'] ?? null;
        $this->assertEquals('Invalid request', $errorMessage);
    }
}
