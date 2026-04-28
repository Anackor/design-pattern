<?php

namespace {
    if (!defined('FTP_BINARY')) {
        define('FTP_BINARY', 2);
    }
}

namespace App\Infrastructure\Client {

    final class FtpTestDoubleRegistry
    {
        public static mixed $connectResult = 'ftp-connection';
        public static bool $loginResult = true;
        public static bool $pasvResult = true;
        public static bool $putResult = true;
        public static bool $getResult = true;
        public static bool $deleteResult = true;
        public static bool $closeResult = true;

        /** @var array<string, list<array<int, mixed>>> */
        public static array $calls = [];

        public static function reset(): void
        {
            self::$connectResult = 'ftp-connection';
            self::$loginResult = true;
            self::$pasvResult = true;
            self::$putResult = true;
            self::$getResult = true;
            self::$deleteResult = true;
            self::$closeResult = true;
            self::$calls = [
                'connect' => [],
                'login' => [],
                'pasv' => [],
                'put' => [],
                'get' => [],
                'delete' => [],
                'close' => [],
            ];
        }
    }

    function ftp_connect(string $host): mixed
    {
        FtpTestDoubleRegistry::$calls['connect'][] = [$host];

        return FtpTestDoubleRegistry::$connectResult;
    }

    function ftp_login(mixed $connection, string $user, string $password): bool
    {
        FtpTestDoubleRegistry::$calls['login'][] = [$connection, $user, $password];

        return FtpTestDoubleRegistry::$loginResult;
    }

    function ftp_pasv(mixed $connection, bool $enable): bool
    {
        FtpTestDoubleRegistry::$calls['pasv'][] = [$connection, $enable];

        return FtpTestDoubleRegistry::$pasvResult;
    }

    function ftp_put(mixed $connection, string $remotePath, string $localPath, int $mode): bool
    {
        FtpTestDoubleRegistry::$calls['put'][] = [$connection, $remotePath, $localPath, $mode];

        return FtpTestDoubleRegistry::$putResult;
    }

    function ftp_get(mixed $connection, string $localPath, string $remotePath, int $mode): bool
    {
        FtpTestDoubleRegistry::$calls['get'][] = [$connection, $localPath, $remotePath, $mode];

        return FtpTestDoubleRegistry::$getResult;
    }

    function ftp_delete(mixed $connection, string $path): bool
    {
        FtpTestDoubleRegistry::$calls['delete'][] = [$connection, $path];

        return FtpTestDoubleRegistry::$deleteResult;
    }

    function ftp_close(mixed $connection): bool
    {
        FtpTestDoubleRegistry::$calls['close'][] = [$connection];

        return FtpTestDoubleRegistry::$closeResult;
    }
}

namespace App\Tests\Unit\Infrastructure\Client {

    use App\Infrastructure\Client\FtpClient;
    use App\Infrastructure\Client\FtpTestDoubleRegistry;
    use PHPUnit\Framework\TestCase;

    class FtpClientTest extends TestCase
    {
        protected function setUp(): void
        {
            FtpTestDoubleRegistry::reset();
        }

        public function testConstructorConnectsLogsInAndEnablesPassiveMode(): void
        {
            $client = new FtpClient('ftp.example.com', 'demo', 'secret');

            $this->assertInstanceOf(FtpClient::class, $client);
            $this->assertSame([['ftp.example.com']], FtpTestDoubleRegistry::$calls['connect']);
            $this->assertSame([['ftp-connection', 'demo', 'secret']], FtpTestDoubleRegistry::$calls['login']);
            $this->assertSame([['ftp-connection', true]], FtpTestDoubleRegistry::$calls['pasv']);
        }

        public function testConstructorThrowsWhenConnectionFails(): void
        {
            FtpTestDoubleRegistry::$connectResult = false;

            $this->expectException(\RuntimeException::class);
            $this->expectExceptionMessage('Could not connect or login to FTP server.');

            new FtpClient('ftp.example.com', 'demo', 'secret');
        }

        public function testConstructorThrowsWhenLoginFails(): void
        {
            FtpTestDoubleRegistry::$loginResult = false;

            try {
                new FtpClient('ftp.example.com', 'demo', 'secret');
                self::fail('Expected constructor to fail when FTP login fails.');
            } catch (\RuntimeException $exception) {
                $this->assertSame('Could not connect or login to FTP server.', $exception->getMessage());
            }
            $this->assertSame([], FtpTestDoubleRegistry::$calls['pasv']);
        }

        public function testPutDelegatesToNativeFtpClient(): void
        {
            FtpTestDoubleRegistry::$putResult = false;

            $client = new FtpClient('ftp.example.com', 'demo', 'secret');

            $this->assertFalse($client->put('/remote.txt', '/tmp/local.txt'));
            $this->assertSame([
                ['ftp-connection', '/remote.txt', '/tmp/local.txt', FTP_BINARY],
            ], FtpTestDoubleRegistry::$calls['put']);
        }

        public function testGetDelegatesToNativeFtpClient(): void
        {
            $client = new FtpClient('ftp.example.com', 'demo', 'secret');

            $this->assertTrue($client->get('/remote.txt', '/tmp/local.txt'));
            $this->assertSame([
                ['ftp-connection', '/tmp/local.txt', '/remote.txt', FTP_BINARY],
            ], FtpTestDoubleRegistry::$calls['get']);
        }

        public function testDeleteDelegatesToNativeFtpClient(): void
        {
            $client = new FtpClient('ftp.example.com', 'demo', 'secret');

            $this->assertTrue($client->delete('/remote.txt'));
            $this->assertSame([
                ['ftp-connection', '/remote.txt'],
            ], FtpTestDoubleRegistry::$calls['delete']);
        }

        public function testCloseDelegatesToNativeFtpClient(): void
        {
            $client = new FtpClient('ftp.example.com', 'demo', 'secret');
            $client->close();

            $this->assertSame([['ftp-connection']], FtpTestDoubleRegistry::$calls['close']);
        }
    }
}
