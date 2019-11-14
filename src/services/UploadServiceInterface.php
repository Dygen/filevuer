<?php

namespace jwhulette\filevuer\services;

use Illuminate\Http\UploadedFile;

interface UploadServiceInterface
{
    public function uploadFiles(string $path, array $files, ?bool $extract = false): void;

    public function unzipArchive(string $path, UploadedFile $file): void;

    public function uploadFile(string $path, UploadedFile $file): void;

    public function getUploadPath(string $path, string $filename): string;
}
