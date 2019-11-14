<?php

namespace jwhulette\filevuer\services;

interface DirectoryServiceInterface
{
    public function listing(?string $path = '/'): array;

    public function delete(array $path): bool;

    public function create(string $path): bool;
    
    public function formatBytes(int $size, int $precision = 2): string;
}
