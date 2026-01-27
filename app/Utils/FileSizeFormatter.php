<?php

namespace App\Utils;

class FileSizeFormatter
{
    /**
     * Format file size in human-readable format.
     *
     * @param  int  $bytes  The file size in bytes
     * @return string  Formatted file size (e.g., "1.5 MB")
     */
    public static function format(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2).' '.$units[$pow];
    }
}
