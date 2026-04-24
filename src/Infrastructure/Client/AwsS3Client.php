<?php

namespace App\Infrastructure\Client;

use Aws\S3\S3Client;
use Aws\Result;

class AwsS3Client implements AwsS3ClientInterface
{
    private S3Client $client;

    public function __construct(string $key, string $secret, string $region)
    {
        $this->client = new S3Client([
            'region' => $region,
            'version' => 'latest',
            'credentials' => [
                'key' => $key,
                'secret' => $secret,
            ],
        ]);
    }

    public function putObject(array $args): void
    {
        $this->client->putObject($args);
    }

    public function getObject(array $args): Result
    {
        return $this->client->getObject($args);
    }

    public function deleteObject(array $args): void
    {
        $this->client->deleteObject($args);
    }
}
