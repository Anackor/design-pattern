<?php

namespace App\Infrastructure\Client;

use Aws\Result;

/**
 * Infrastructure-facing wrapper around the final AWS SDK client.
 */
interface AwsS3ClientInterface
{
    public function putObject(array $args): void;

    public function getObject(array $args): Result;

    public function deleteObject(array $args): void;
}
