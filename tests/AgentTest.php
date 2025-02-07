<?php

namespace Edgaras\AzureLLM\Tests;

use Edgaras\AzureLLM\Agents\Agent;
use Edgaras\AzureLLM\LLM;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class AgentTest extends TestCase
{
    private $agent;
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
            'deployment' => 'gpt-4o',
            'apiVersion' => '2024-05-01-preview'
        ]);

        $this->agent = new Agent($config);
         
        $reflection = new \ReflectionClass($this->agent);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->agent, $client);
    }

    public function testCreateAgent()
    {
        $this->mockHandler->append(new Response(201, [], json_encode(['id' => 'asst_12345'])));

        $response = $this->agent->createAgent(
            "TestAgent",
            "You are a helpful assistant.",
            "A test AI agent",
            [],
            "vs_67890"
        );

        $this->assertArrayHasKey('id', $response);
        $this->assertEquals('asst_12345', $response['id']);
    }

    public function testUpdateAgent()
    {
        $this->mockHandler->append(new Response(200, [], json_encode(['id' => 'asst_12345', 'instructions' => 'Updated instructions'])));

        $response = $this->agent->updateAgent(
            "asst_12345",
            ["instructions" => "Updated instructions"],
            "vs_67890"
        );

        $this->assertArrayHasKey('id', $response);
        $this->assertEquals('Updated instructions', $response['instructions']);
    }

    public function testDeleteAgent()
    {
        $this->mockHandler->append(new Response(204));

        $response = $this->agent->deleteAgent("asst_12345");

        $this->assertEmpty($response);
    }

    public function testGetAgent()
    {
        $expectedResponse = [
            'id' => 'asst_12345',
            'name' => 'TestAgent'
        ];

        $this->mockHandler->append(new Response(200, [], json_encode($expectedResponse)));

        $response = $this->agent->getAgent("asst_12345");

        $this->assertArrayHasKey('id', $response);
        $this->assertEquals('TestAgent', $response['name']);
    }

    public function testListAgents()
    {
        $expectedResponse = [
            'data' => [
                ['id' => 'asst_12345', 'name' => 'TestAgent'],
                ['id' => 'asst_67890', 'name' => 'AnotherAgent']
            ]
        ];

        $this->mockHandler->append(new Response(200, [], json_encode($expectedResponse)));

        $response = $this->agent->listAgents();

        $this->assertArrayHasKey('data', $response);
        $this->assertCount(2, $response['data']);
        $this->assertEquals('TestAgent', $response['data'][0]['name']);
        $this->assertEquals('AnotherAgent', $response['data'][1]['name']);
    }

    public function testCreateAgentWithInvalidResponse()
    {
        $this->mockHandler->append(new Response(400, [], json_encode(['error' => 'Invalid request'])));

        $response = $this->agent->createAgent(
            "InvalidAgent",
            "This request should fail.",
            "Invalid test",
            [],
            "vs_invalid"
        );

        $this->assertArrayHasKey('error', $response);
         
        $errorMessage = json_decode($response['response'], true)['error'] ?? null;
        
        $this->assertEquals('Invalid request', $errorMessage);
    }

    public function testCreateAgentWithoutVectorStore()
    {
        $this->mockHandler->append(new Response(201, [], json_encode(['id' => 'asst_98765'])));

        $response = $this->agent->createAgent(
            "SimpleAgent",
            "You are a basic assistant.",
            "An agent without a vector store"
        );

        $this->assertArrayHasKey('id', $response);
        $this->assertEquals('asst_98765', $response['id']);
    }

}
