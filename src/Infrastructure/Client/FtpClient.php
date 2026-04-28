<?php

namespace App\Infrastructure\Client;

/**
 * FtpClient abstracts low-level FTP operations using native PHP functions.
 * It centralizes connection and file handling logic, improving testability and encapsulation.
 * This avoids duplicating FTP-specific code in the adapter and simplifies future extensions.
 */
class FtpClient
{
    private $connection;

    public function __construct(string $host, string $user, string $password)
    {
        $this->connection = ftp_connect($host);
        if (!$this->connection || !ftp_login($this->connection, $user, $password)) {
            throw new \RuntimeException('Could not connect or login to FTP server.');
        }
        ftp_pasv($this->connection, true);
    }

    public function put(string $remotePath, string $localPath): bool
    {
        return ftp_put($this->connection, $remotePath, $localPath, \FTP_BINARY);
    }

    public function get(string $remotePath, string $localPath): bool
    {
        return ftp_get($this->connection, $localPath, $remotePath, \FTP_BINARY);
    }

    public function delete(string $path): bool
    {
        return ftp_delete($this->connection, $path);
    }

    public function close(): void
    {
        if ($this->connection) {
            ftp_close($this->connection);
        }
    }
}
