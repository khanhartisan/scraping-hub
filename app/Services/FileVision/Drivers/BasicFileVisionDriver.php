<?php

namespace App\Services\FileVision\Drivers;

use App\Services\FileVision\FileVisionService;
use App\Utils\FileSizeFormatter;
use Illuminate\Support\Facades\Storage;

class BasicFileVisionDriver extends FileVisionService
{
    /**
     * Generate a description for the file.
     */
    protected function generateDescription(string $filePath, string $extension, string $mimeType): string
    {
        return $this->generateBasicDescription($filePath, $extension, $mimeType);
    }

    /**
     * Generate a description for the file (public method for use by other drivers).
     */
    public function generateDescriptionForFile(string $filePath, string $extension, string $mimeType): string
    {
        return $this->generateDescription($filePath, $extension, $mimeType);
    }

    /**
     * Generate a basic description for the file.
     */
    protected function generateBasicDescription(string $filePath, string $extension, string $mimeType): string
    {
        $fileSize = Storage::size($filePath);
        $sizeFormatted = FileSizeFormatter::format($fileSize);

        $typeDescription = $this->getTypeDescription($mimeType, $extension);

        return sprintf(
            '%s file (%s) with size %s',
            ucfirst($typeDescription),
            strtoupper($extension),
            $sizeFormatted
        );
    }

    /**
     * Get a human-readable type description.
     */
    protected function getTypeDescription(string $mimeType, string $extension): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }

        if (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        }

        if ($mimeType === 'application/pdf') {
            return 'PDF document';
        }

        if (str_contains($mimeType, 'wordprocessingml') || in_array($extension, ['doc', 'docx'])) {
            return 'Word document';
        }

        if (str_contains($mimeType, 'spreadsheetml') || in_array($extension, ['xls', 'xlsx'])) {
            return 'Excel spreadsheet';
        }

        if (str_starts_with($mimeType, 'text/')) {
            return 'text';
        }

        if ($mimeType === 'application/json') {
            return 'JSON data';
        }

        if ($mimeType === 'application/zip') {
            return 'ZIP archive';
        }

        return 'file';
    }
}
