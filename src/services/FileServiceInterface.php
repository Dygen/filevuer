<?php

namespace jwhulette\filevuer\services;

interface FileServiceInterface
{
    public function contents(?string $path = ''): ?string;

    public function update(?string $path = '', ?string $contents = ''): bool;

    public function delete(array $path): bool;

    public function create(string $path): bool;
}
