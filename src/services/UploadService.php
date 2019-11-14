<?php
declare(strict_types=1);

namespace jwhulette\filevuer\services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Filesystem\FilesystemManager;
use jwhulette\filevuer\traits\SessionDriverTrait;

class UploadService implements UploadServiceInterface
{
    use SessionDriverTrait;

    /**
     * @var FilesystemManager
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
     * Process the uploaded files
     *
     * @param string $path
     * @param array $files
     * @param bool|null $extract
     *
     * @return void
     *
     * @throws Exception
     */
    public function uploadFiles(string $path, array $files, ?bool $extract = false): void
    {
        foreach ($files as $file) {
            if (!$extract) {
                $this->uploadFile($path, $file);
            } else {
                if ($file->getClientOriginalExtension() == 'zip') {
                    $this->unzipArchive($path, $file);
                } else {
                    $this->uploadFile($path, $file);
                }
            }
        }
    }

    /**
     * @param string $path
     * @param UploadedFile $file
     *
     * @return void
     *
     * @throws RuntimeException
     */
    public function unzipArchive(string $path, UploadedFile $file): void
    {
        $zipArchive  = new \ZipArchive();
        $resource = $zipArchive->open($file->getRealPath());

        if (true === $resource) {
            $fileCount   = $zipArchive->numFiles;
            $this->createDirectories($zipArchive, $fileCount, $path);

            for ($i = 0; $i < $fileCount; $i++) {
                $filename   = $zipArchive->getNameIndex($i);
                $filestream = $zipArchive->getStream($filename);
                if (!$filestream) {
                    unlink($file->getRealPath());
                    throw new \RuntimeException('Failed to get zipped file - '.$filename);
                }

                if (pathinfo($filename, PATHINFO_EXTENSION)) {
                    $uploadPath = $this->getUploadPath($path, $filename);
                    $response   = $this->fileSystem->cloud()->put($uploadPath, $filestream);

                    if (!$response) {
                        unlink($file->getRealPath());
                        throw new \RuntimeException("Error creating file on server");
                    }
                }
            }

            $zipArchive->close();
            unlink($file->getRealPath());
        } else {
            unlink($file->getRealPath());
            throw new \RuntimeException('Failed to extract zip archive.');
        }
    }

    /**
     * Create the directories before attampting to copy the files
     *
     * @param \ZipArchive $zipArchive
     * @param int $fileCount
     * @param string $path
     *
     * @return void
     */
    public function createDirectories(\ZipArchive $zipArchive, int $fileCount, string $path): void
    {
        for ($i = 0; $i < $fileCount; $i++) {
            $filename   = $zipArchive->getNameIndex($i);
            $this->createDirectory($path, $filename);
        }
    }

    /**
     * Crate a directory
     *
     * @param string $filename
     *
     * @return void
     */
    public function createDirectory(string $path, string $filename): void
    {
        $directory = dirname($filename);
        if ('.' !== $directory) {
            $directoryPath = $this->getUploadPath($path, $directory) . '/';
            $this->fileSystem->cloud()->makeDirectory($directoryPath);
        }
    }

    /**
     * Uplad the file
     *
     * @param string $path
     * @param UploadedFile $file
     *
     * @return void
     *
     * @throws Exception
     */
    public function uploadFile(string $path, UploadedFile $file): void
    {
        $uploadPath = $this->getUploadPath($path, $file->getClientOriginalName());
        $response   = $this->fileSystem->cloud()->put($uploadPath, file_get_contents($file->getRealPath()));
        unlink($file->getRealPath());
        if (!$response) {
            throw new Exception("Error uploading file", 1);
        }
    }

    /**
     * Get the path to upload the file
     *
     * @param string $path
     * @param string $filename
     *
     * @return string
     */
    public function getUploadPath(string $path, string $filename): string
    {
        $path =  ltrim($path, '/');
        return $this->getFullPath($path . $filename);
    }
}
