<?php

namespace App\Contracts\FileVision;

interface FileVision
{
    public function describe(string $filePath): FileInformation;
}