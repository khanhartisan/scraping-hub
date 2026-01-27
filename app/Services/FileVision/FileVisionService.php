<?php

namespace App\Services\FileVision;

use App\Contracts\FileVision\FileInformation;
use App\Contracts\FileVision\FileVision as FileVisionContract;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToCheckFileExistence;

abstract class FileVisionService implements FileVisionContract
{
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Describe a file and return file information.
     */
    public function describe(string $filePath): FileInformation
    {
        // Check if file exists using Storage facade
        try {
            if (! Storage::exists($filePath)) {
                throw UnableToCheckFileExistence::forLocation($filePath);
            }
        } catch (UnableToCheckFileExistence $e) {
            // Re-throw UnableToCheckFileExistence as-is
            throw $e;
        } catch (\League\Flysystem\FilesystemException $e) {
            // Wrap Flysystem exceptions
            throw UnableToCheckFileExistence::forLocation($filePath, $e);
        } catch (\Exception $e) {
            // Wrap any other exceptions
            throw UnableToCheckFileExistence::forLocation($filePath, $e);
        }

        // Get file info
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeType = $this->detectMimeType($filePath);

        // Create FileInformation object
        $fileInfo = new FileInformation();
        $fileInfo->setFilePath($filePath)
            ->setExtension($extension)
            ->setMimeType($mimeType);

        // Generate description - this is implemented by child classes
        $description = $this->generateDescription($filePath, $extension, $mimeType);
        $fileInfo->setDescription($description);
        $fileInfo->setConfidence($this->getConfidence($mimeType));

        return $fileInfo;
    }

    /**
     * Generate a description for the file.
     * This method must be implemented by child classes.
     */
    abstract protected function generateDescription(string $filePath, string $extension, string $mimeType): string;

    /**
     * Get the confidence score for the file description.
     * Child classes can override this to provide different confidence levels.
     */
    protected function getConfidence(string $mimeType): float
    {
        return 1.0;
    }

    /**
     * Detect MIME type of a file using Storage facade.
     */
    protected function detectMimeType(string $filePath): string
    {
        // Try to get MIME type from Storage
        try {
            $mimeType = Storage::mimeType($filePath);
            if ($mimeType) {
                return $mimeType;
            }
        } catch (\Exception $e) {
            // Fall through to extension-based detection
        }

        // Fallback to extension-based detection
        return $this->getMimeTypeFromExtension(pathinfo($filePath, PATHINFO_EXTENSION));
    }

    /**
     * Get MIME type from file extension.
     */
    protected function getMimeTypeFromExtension(string $extension): string
    {
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'txt' => 'text/plain',
            'html' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'zip' => 'application/zip',
            'mp4' => 'video/mp4',
            'mp3' => 'audio/mpeg',
        ];

        return $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
    }

    /**
     * Check if the file is an image.
     */
    protected function isImage(string $mimeType): bool
    {
        return str_starts_with($mimeType, 'image/');
    }
}
