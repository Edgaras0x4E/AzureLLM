<?php

use PHPUnit\Framework\TestCase;
use Edgaras\AzureLLM\LLM;
use Edgaras\AzureLLM\AzureOpenAI;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class AzureOpenAITest extends TestCase
{
    public function testLLMValidConfiguration()
    {
        $config = [
            'apiKey' => 'validApiKey',
            'endpoint' => 'https://example.com',
            'deployment' => 'testDeployment',
            'apiVersion' => '2023-01-01',
        ];

        $llm = new LLM($config);

        $this->assertSame('validApiKey', $llm->getApiKey());
        $this->assertSame('https://example.com', $llm->getEndpoint());
        $this->assertSame('testDeployment', $llm->getDeployment());
        $this->assertSame('2023-01-01', $llm->getApiVersion());
    }

    public function testLLMInvalidConfiguration()
    {
        $this->expectException(InvalidArgumentException::class);

        $config = [
            'apiKey' => '',
            'endpoint' => 'invalid-url',
            'deployment' => '',
        ];

        new LLM($config);
    }

    public function testAzureOpenAIValidMessages()
    {
        $llmConfig = new LLM([
            'apiKey' => 'validApiKey',
            'endpoint' => 'https://example.com',
            'deployment' => 'testDeployment',
            'apiVersion' => '2023-01-01',
        ]);

        $azureOpenAI = new AzureOpenAI($llmConfig);

        $messages = [
            ['role' => 'user', 'content' => 'Hello, AI!'],
            ['role' => 'assistant', 'content' => 'Hello! How can I help you?'],
        ];

        $this->assertIsArray($azureOpenAI->chatCompletions($messages));
    }

    public function testAzureOpenAIInvalidMessages()
    {
        $llmConfig = new LLM([
            'apiKey' => 'validApiKey',
            'endpoint' => 'https://example.com',
            'deployment' => 'testDeployment',
            'apiVersion' => '2023-01-01',
        ]);

        $azureOpenAI = new AzureOpenAI($llmConfig);

        $this->expectException(InvalidArgumentException::class);

        $messages = [
            ['role' => 'invalidRole', 'content' => ''],
        ];

        $azureOpenAI->chatCompletions($messages);
    }

    public function testAzureOpenAIWithInvalidDataSources()
    {
        $llmConfig = new LLM([
            'apiKey' => 'validApiKey',
            'endpoint' => 'https://example.com',
            'deployment' => 'testDeployment',
            'apiVersion' => '2023-01-01',
        ]);

        $azureOpenAI = new AzureOpenAI($llmConfig);

        $this->expectException(InvalidArgumentException::class);

        $dataSources = [
            ['type' => 'unknown', 'parameters' => ['endpoint' => 'invalid-url']],
        ];

        $azureOpenAI->chatCompletions([], [], $dataSources);
    }

    public function testAzureOpenAIHandlesRequestException()
    {
        $mockLLM = $this->createMock(LLM::class);
        $mockLLM->method('getApiKey')->willReturn('validApiKey');
        $mockLLM->method('getEndpoint')->willReturn('https://example.com');
        $mockLLM->method('getDeployment')->willReturn('testDeployment');
        $mockLLM->method('getApiVersion')->willReturn('2023-01-01');

        $mockClient = $this->createMock(Client::class);
        $mockClient->method('post')->willThrowException(new RequestException('Error', new \GuzzleHttp\Psr7\Request('POST', 'test')));

        $azureOpenAI = new AzureOpenAI($mockLLM);
        $reflection = new \ReflectionProperty(AzureOpenAI::class, 'client');
        $reflection->setAccessible(true);
        $reflection->setValue($azureOpenAI, $mockClient);

        $messages = [
            ['role' => 'user', 'content' => 'Hello, AI!'],
        ];

        $response = $azureOpenAI->chatCompletions($messages);

        $this->assertArrayHasKey('error', $response);
        $this->assertArrayHasKey('response', $response);
    }
}
