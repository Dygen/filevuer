<?php
declare(strict_types=1);

namespace jwhulette\filevuer\services;

use jwhulette\filevuer\services\ConnectionService;

interface ConnectionServiceInterface
{
    public function connectToService(array $connection): bool;
}
