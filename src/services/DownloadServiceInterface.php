<?php

namespace jwhulette\filevuer\services;

use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface DownloadServiceInterface
{
    public function setHash(array $paths): string;

    public function getHash(string $hash): Collection;

    public function getDownload(string $hash): StreamedResponse;
}
