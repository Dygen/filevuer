<?php
declare(strict_types=1);

namespace jwhulette\filevuer\services;

use Illuminate\Filesystem\FilesystemManager;
use jwhulette\filevuer\traits\SessionDriverTrait;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;

/**
 * Directory Service Class
 */
class DirectoryService implements DirectoryServiceInterface
{
    use SessionDriverTrait;

    protected FilesystemManager $fileSystem;

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
     * List the directory contents
     *
     * @param string|null $path
     *
     * @return array
     * @throws FilesystemException
     */
    public function listing(?string $path = '/'): array
    {
        $path = $this->getFullPath($path);
        $contents = $this->fileSystem->cloud()->listContents($path)->toArray();
        $contents = $this->sortForListing($contents);
        
        return $this->formatDirectoryListingAttributes($contents);
    }

    /**
     * Delete a all files in a folder
     *
     * @param array|null $path
     *
     * @return bool
     */
    public function delete(?array $path): bool
    {
        foreach ($path as $dir) {
            $this->fileSystem->cloud()->deleteDirectory($dir);
        }

        return true;
    }

    /**
     * Creates an empty directory.
     *
     * @param string $path
     *
     * @return bool
     */
    public function create(string $path): bool
    {
        $path = $this->getFullPath($path);

        return $this->fileSystem->cloud()->makeDirectory($path);
    }

    /**
     * Sort the listing by type and filename.
     *
     * @param array $contents
     *
     * @return array
     */
    protected function sortForListing(array $contents): array
    {
        usort($contents, function ($typeA, $typeB) {
            // Sort by type
            $comparison = strcmp($typeA['type'], $typeB['type']);
            if (0 !== $comparison) {
                return $comparison;
            }

            // Sort by name
            return strcmp($typeA['path'], $typeB['path']);
        });

        return $contents;
    }

    /**
     * Add basename to match v1 and format filesize human-readable.
     *
     * @param array $contents
     *
     * @return array
     */
    protected function formatDirectoryListingAttributes(array $contents): array
    {
        return array_map(function ($item) {
            return $this->formatStorageAttribute($item);
        }, $contents);
    }

    /**
     * Add basename to match v1 and format filesize human-readable.
     *
     * @param StorageAttributes $item
     *
     * @return object
     */
    protected function formatStorageAttribute(StorageAttributes $item): object
    {
        return (object) [
            'basename' => basename($item->path()), 
            'path' => $item->path(), 
            'size' => $item->isFile() ? $this->formatBytes((int) $item->fileSize()) : null,
            'visibility' => $item->visibility(), 
            'type' => $item->type(),
        ];
    }

    /**
     * Format bytes as human-readable filesize.
     *
     * @param int $size
     * @param int $precision
     *
     * @return string
     */
    public function formatBytes(int $size, int $precision = 2): string
    {
        if ($size > 0) {
            $size = (int) $size;
            $base = log($size) / log(1024);
            $suffixes = array(' bytes', ' KB', ' MB', ' GB', ' TB');

            return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
        }

        return $size . ' bytes';
    }
}
