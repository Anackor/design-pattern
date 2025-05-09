<?php

namespace App\Domain\Adapter;

/**
 * This interface defines a contract for file storage operations (upload, download, delete).
 * It plays a crucial role in the Adapter design pattern, allowing us to abstract and unify
 * access to multiple storage systems (e.g., AWS S3, FTP, Local).
 *
 * By enforcing this interface, we ensure that all storage adapters adhere to the same structure,
 * making them interchangeable and enabling polymorphism. This is key for decoupling the application
 * logic from infrastructure-specific implementations. 
 */
interface FileStorageInterface
{
    public function upload(string $path, string $contents): bool;
    public function download(string $path): string;
    public function delete(string $path): bool;
}
