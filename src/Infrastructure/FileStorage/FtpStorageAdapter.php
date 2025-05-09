<?php

namespace App\Infrastructure\FileStorage;

use App\Domain\Adapter\FileStorageInterface;
use App\Infrastructure\Client\FtpClient;

class FtpStorageAdapter implements FileStorageInterface
{
    public function __construct(
        private FtpClient $client
    ) {}

    public function upload(string $path, string $contents): bool
    {
        $tmpFile = tmpfile();
        fwrite($tmpFile, $contents);
        $meta = stream_get_meta_data($tmpFile);
        $tmpPath = $meta['uri'];

        $result = $this->client->put($path, $tmpPath);
        fclose($tmpFile);
        return $result;
    }

    public function download(string $path): string
    {
        $tmpFile = tmpfile();
        $meta = stream_get_meta_data($tmpFile);
        $tmpPath = $meta['uri'];

        if (!$this->client->get($path, $tmpPath)) {
            fclose($tmpFile);
            throw new \RuntimeException("Download failed: $path");
        }

        $content = file_get_contents($tmpPath);
        fclose($tmpFile);
        return $content;
    }

    public function delete(string $path): bool
    {
        return $this->client->delete($path);
    }
}
