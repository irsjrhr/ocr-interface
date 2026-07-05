<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Exception;

class MinioS3Service
{
    /**
     * Get the underlying S3 Client instance for advanced operations like Bucket CRUD.
     */
    protected function getClient()
    {
        return Storage::disk('minios3')->getClient();
    }

    /**
     * Get the current bucket configured in filesystems.php
     */
    protected function getDefaultBucket(): string
    {
        return config('filesystems.disks.minios3.bucket');
    }

    // ==========================================
    // BUCKET CRUD OPERATIONS
    // ==========================================

    /**
     * List all buckets in the MinIO server.
     *
     * @return array
     */
    public function listBuckets(): array
    {
        try {
            $result = $this->getClient()->listBuckets();
            return $result['Buckets'] ?? [];
        } catch (Exception $e) {
            throw new Exception("Error listing buckets: " . $e->getMessage());
        }
    }

    /**
     * Create a new bucket.
     *
     * @param string $bucketName
     * @return bool
     */
    public function createBucket(string $bucketName): bool
    {
        try {
            $this->getClient()->createBucket([
                'Bucket' => $bucketName,
            ]);
            return true;
        } catch (Exception $e) {
            throw new Exception("Error creating bucket '{$bucketName}': " . $e->getMessage());
        }
    }

    /**
     * Delete an empty bucket.
     *
     * @param string $bucketName
     * @return bool
     */
    public function deleteBucket(string $bucketName): bool
    {
        try {
            $this->getClient()->deleteBucket([
                'Bucket' => $bucketName,
            ]);
            return true;
        } catch (Exception $e) {
            throw new Exception("Error deleting bucket '{$bucketName}': " . $e->getMessage());
        }
    }


    // ==========================================
    // FILE CRUD OPERATIONS
    // ==========================================

    /**
     * Upload a file to the S3 bucket.
     *
     * @param string $path The destination path/filename (e.g., 'images/photo.jpg')
     * @param mixed $contents The file contents, resource, or File object
     * @param string|null $bucket Override default bucket (optional)
     * @return string|false The URL of the uploaded file on success, false on failure
     */
    public function uploadFile(string $path, $contents, ?string $bucket = null)
    {
        // Temporarily change the default bucket if a different one is requested
        if ($bucket && $bucket !== $this->getDefaultBucket()) {
            config(['filesystems.disks.minios3.bucket' => $bucket]);
        }

        $success = Storage::disk('minios3')->put($path, $contents);

        if ($success) {
            return Storage::disk('minios3')->url($path);
        }

        return false;
    }

    /**
     * Upload a document specifically for the OCR interface structure.
     *
     * @param mixed $file The uploaded file object
     * @param string $directoryBusinessUnit
     * @param string $directoryDocument
     * @param string|null $bucket Override default bucket (optional)
     * @return string|false The URL of the uploaded file on success, false on failure
     */
    public function uploadDocumentInterface($file, string $directoryBusinessUnit, string $directoryDocument, ?string $bucket = null)
    {
        $path = $directoryBusinessUnit . '/' . $directoryDocument . '/' . $file->getClientOriginalName();
        $contents = file_get_contents($file->getRealPath());

        return $this->uploadFile($path, $contents, $bucket);
    }

    /**
     * Download a file from the S3 bucket.
     *
     * @param string $path The file path
     * @param string|null $bucket Override default bucket (optional)
     * @return string|null The file contents
     */
    public function downloadFile(string $path, ?string $bucket = null): ?string
    {
        if ($bucket && $bucket !== $this->getDefaultBucket()) {
            config(['filesystems.disks.minios3.bucket' => $bucket]);
        }

        if (!Storage::disk('minios3')->exists($path)) {
            return null;
        }

        return Storage::disk('minios3')->get($path);
    }

    /**
     * Delete a file from the S3 bucket.
     *
     * @param string $path The file path
     * @param string|null $bucket Override default bucket (optional)
     * @return bool
     */
    public function deleteFile(string $path, ?string $bucket = null): bool
    {
        if ($bucket && $bucket !== $this->getDefaultBucket()) {
            config(['filesystems.disks.minios3.bucket' => $bucket]);
        }

        return Storage::disk('minios3')->delete($path);
    }

    /**
     * List all files in a specific directory with optional filters.
     *
     * @param string $directory The directory to list files from (e.g., 'images/')
     * @param string|null $bucket Override default bucket (optional)
     * @param string|null $search Keyword search (optional)
     * @param string|null $startDate Filter by start date YYYY-MM-DD (optional)
     * @param string|null $endDate Filter by end date YYYY-MM-DD (optional)
     * @return array
     */
    public function listFiles_byDirectories(
        string $directory = '/', 
        ?string $bucket = null,
        ?string $search = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        if ($bucket && $bucket !== $this->getDefaultBucket()) {
            config(['filesystems.disks.minios3.bucket' => $bucket]);
        }

        $files = Storage::disk('minios3')->files($directory);
        $result = [];

        $startTs = $startDate ? strtotime($startDate . ' 00:00:00') : null;
        $endTs = $endDate ? strtotime($endDate . ' 23:59:59') : null;

        foreach ($files as $file) {
            $filename = basename($file);

            // Filter by search keyword
            if ($search && stripos($filename, $search) === false) {
                continue;
            }

            $lastModified = Storage::disk('minios3')->lastModified($file);

            // Filter by date
            if ($startTs && $lastModified < $startTs) continue;
            if ($endTs && $lastModified > $endTs) continue;

            $result[] = [
                'path' => $file,
                'name' => $filename,
                'last_modified' => date('Y-m-d H:i:s', $lastModified),
                'url' => Storage::disk('minios3')->url($file),
            ];
        }

        return $result;
    }

    /**
     * List all directories in a specific directory with optional search filter.
     *
     * @param string $directory The directory to list subdirectories from (e.g., '/')
     * @param string|null $bucket Override default bucket (optional)
     * @param string|null $search Keyword search (optional)
     * @param string|null $startDate Filter by start date YYYY-MM-DD (optional)
     * @param string|null $endDate Filter by end date YYYY-MM-DD (optional)
     * @return array
     */
    public function listDirectories_byBucket(
        string $directory = '/', 
        ?string $bucket = null,
        ?string $search = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        if ($bucket && $bucket !== $this->getDefaultBucket()) {
            config(['filesystems.disks.minios3.bucket' => $bucket]);
        }

        //mengambil semua directori
        $directories = Storage::disk('minios3')->directories($directory);
        $result = [];

        $startTs = $startDate ? strtotime($startDate . ' 00:00:00') : null;
        $endTs = $endDate ? strtotime($endDate . ' 23:59:59') : null;

        //Melakukan iterasi pada setiap directori dengan menyesuaikan filter ataupun search
        foreach ($directories as $dir) {
            $dirname = basename($dir);

            if ($search && stripos($dirname, $search) === false) {
                continue;
            }

            // Optional date filtering for directories (if they are objects with dates)
            // Note: Normal inferred directories in S3 don't have lastModified dates.
            // This try/catch attempts to fetch the metadata if the dir is an actual 0-byte object.
            $lastModified = null;
            try {
                $lastModified = Storage::disk('minios3')->lastModified($dir . '/');
            } catch (\Exception $e) {
                // Ignore missing lastModified for inferred directories
            }

            if ($startTs && $lastModified && $lastModified < $startTs) continue;
            if ($endTs && $lastModified && $lastModified > $endTs) continue;

            $result[] = [
                'path' => $dir,
                'name' => $dirname,
                'last_modified' => $lastModified ? date('Y-m-d H:i:s', $lastModified) : null,
            ];
        }

        return $result;
    }

    /**
     * Create a new directory in the bucket.
     *
     * @param string $directoryName
     * @param string|null $bucket
     * @return bool
     */
    public function createDirectory(string $directoryName, ?string $bucket = null): bool
    {
        if ($bucket && $bucket !== $this->getDefaultBucket()) {
            config(['filesystems.disks.minios3.bucket' => $bucket]);
        }

        $path = rtrim($directoryName, '/') . '/';
        return Storage::disk('minios3')->put($path, '');
    }
}
