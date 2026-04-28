<?php

namespace App\Tests\Unit\Infrastructure\Client;

use App\Infrastructure\Client\AwsS3Client;
use Aws\Result;
use Aws\S3\S3Client;
use PHPUnit\Framework\TestCase;

class AwsS3ClientTest extends TestCase
{
    public function testConstructorInitializesUnderlyingSdkClient(): void
    {
        $client = new AwsS3Client('key', 'secret', 'eu-west-1');

        $reflection = new \ReflectionProperty($client, 'client');
        $sdkClient = $reflection->getValue($client);

        $this->assertInstanceOf(S3Client::class, $sdkClient);
    }

    public function testPutObjectDelegatesToSdkClient(): void
    {
        $client = new AwsS3Client('key', 'secret', 'eu-west-1');
        $sdkClient = new class extends S3Client {
            public array $calls = [];

            public function __construct() {}

            public function putObject($args = [])
            {
                $this->calls['putObject'] = $args;
            }
        };

        $this->replaceSdkClient($client, $sdkClient);
        $client->putObject(['Bucket' => 'bucket']);

        $this->assertSame(['Bucket' => 'bucket'], $sdkClient->calls['putObject']);
    }

    public function testGetObjectReturnsSdkResult(): void
    {
        $client = new AwsS3Client('key', 'secret', 'eu-west-1');
        $expected = new Result(['Body' => 'content']);
        $sdkClient = new class ($expected) extends S3Client {
            public array $calls = [];

            public function __construct(private Result $result) {}

            public function getObject($args = []): Result
            {
                $this->calls['getObject'] = $args;

                return $this->result;
            }
        };

        $this->replaceSdkClient($client, $sdkClient);
        $this->assertSame($expected, $client->getObject(['Key' => 'file.txt']));
        $this->assertSame(['Key' => 'file.txt'], $sdkClient->calls['getObject']);
    }

    public function testDeleteObjectDelegatesToSdkClient(): void
    {
        $client = new AwsS3Client('key', 'secret', 'eu-west-1');
        $sdkClient = new class extends S3Client {
            public array $calls = [];

            public function __construct() {}

            public function deleteObject($args = [])
            {
                $this->calls['deleteObject'] = $args;
            }
        };

        $this->replaceSdkClient($client, $sdkClient);
        $client->deleteObject(['Key' => 'file.txt']);

        $this->assertSame(['Key' => 'file.txt'], $sdkClient->calls['deleteObject']);
    }

    private function replaceSdkClient(AwsS3Client $client, S3Client $sdkClient): void
    {
        $reflection = new \ReflectionProperty($client, 'client');
        $reflection->setValue($client, $sdkClient);
    }
}
