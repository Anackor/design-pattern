<?php

namespace App\Tests\Unit\Infrastructure\FileStorage;

use App\Infrastructure\FileStorage\LocalFileStorageAdapter;
use PHPUnit\Framework\TestCase;

class LocalFileStorageAdapterTest extends TestCase
{
    private string $testPath = '/tmp/test_file.txt';

    public function tearDown(): void
    {
        if (file_exists($this->testPath)) {
            unlink($this->testPath);
        }
    }

    public function testUpload(): void
    {
        $adapter = new LocalFileStorageAdapter('/');
        $adapter->upload($this->testPath, 'Test content');

        $this->assertFileExists($this->testPath);
        $this->assertSame('Test content', file_get_contents($this->testPath));
    }

    public function testDownload(): void
    {
        file_put_contents($this->testPath, 'Read this!');
        $adapter = new LocalFileStorageAdapter('/');

        $content = $adapter->download($this->testPath);

        $this->assertSame('Read this!', $content);
    }

    public function testDelete(): void
    {
        file_put_contents($this->testPath, 'To be deleted');
        $adapter = new LocalFileStorageAdapter('/');

        $adapter->delete($this->testPath);

        $this->assertFileDoesNotExist($this->testPath);
    }
}
