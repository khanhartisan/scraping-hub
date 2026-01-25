<?php

namespace App\Contracts\Downloader;

interface Downloader
{
    public function download(string $url, string $filePath): bool;
}