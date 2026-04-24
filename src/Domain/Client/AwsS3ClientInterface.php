<?php

namespace App\Domain\Client;

use Aws\Result;

/**
 * We introduce AwsS3ClientInterface to decouple our application logic from the AWS SDK,
 * particularly because the S3Client class provided by the SDK is marked as 'final'
 * and therefore cannot be mocked directly in unit tests using PHPUnit.
 *
 * This interface allows us to inject a mockable dependency, improve testability,
 * and isolate external SDK usage from our core infrastructure logic.
 *
 * In contrast, FtpClient is a custom-built class that wraps native FTP functions.
 * Since it is under our control and not final, we can easily mock or test it directly
 * without requiring an interface at this stage.
 *
 * Should FtpClient evolve in complexity or support multiple protocols (e.g., SFTP),
 * introducing a corresponding FtpClientInterface would be advisable for consistency.
 */
interface AwsS3ClientInterface
{
    public function putObject(array $args): void;
    public function getObject(array $args): Result;
    public function deleteObject(array $args): void;
}
