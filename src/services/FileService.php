<?php
declare(strict_types=1);

namespace jwhulette\filevuer\services;

use InvalidArgumentException;
use Illuminate\Filesystem\FilesystemManager;

class FileService implements FileServiceInterface
{
    
    /**
     * Filesystem
     *
     * @var object
     */
    protected $fileSystem;

    /**
     * __construct
     *
     * @param FilesystemManager $fileSystem
     */
    public function __construct(FilesystemManager $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }
    
    /**
     * Get file contents from server.
     *
     * @param string|null $path
     *
     * @throws InvalidArgumentException
     *
     * @return mixed
     */
    public function contents(?string $path = ''): ?string
    {
        $this->checkPath($path);

        return $this->fileSystem->cloud()->read($path);
    }

    /**
     * Updates a file's contents.
     *
     * @param string|null $path
     * @param string|null $contents
     *
     * @throws InvalidArgumentException
     *
     * @return bool
     */
    public function update(?string $path = '', ?string $contents = ''): bool
    {
        $this->checkPath($path);

        return $this->fileSystem->cloud()->put($path, $contents);
    }

    /**
     * Deletes one or multiple files.
     *
     * @param array $path
     *
     * @return bool
     */
    public function delete(array $path): bool
    {
        foreach ($path as $file) {
            $this->fileSystem->cloud()->delete($file);
        }

        return true;
    }

    /**
     * Creates an empty file.
     *
     * @param string $path
     *
     * @return bool
     */
    public function create(string $path): bool
    {
        $this->checkPath($path);
        return $this->fileSystem->cloud()->put($path, '');
    }

    /**
     * Validates a path.
     *
     * @param string $path
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    protected function checkPath(string $path): void
    {
        if ('' == $path) {
            throw new InvalidArgumentException('Please specify a file path.');
        }
    }
}
