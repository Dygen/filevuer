<?php
declare(strict_types=1);

namespace jwhulette\filevuer\services;

use Carbon\Carbon;
use ZipStream\ZipStream;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Facades\Log;
use jwhulette\filevuer\services\SessionInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;
use jwhulette\filevuer\services\DirectoryServiceInterface;

/**
  * Download Service Class
  */
class DownloadService implements DownloadServiceInterface
{
    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var DirectoryServiceInterface
     */
    private $directoryService;

    /**
     * __construct
     *
     * @param FilesystemManager $fileSystem
     * @param DirectoryServiceInterface $directoryService
     */
    public function __construct(FilesystemManager $fileSystem, DirectoryServiceInterface $directoryService)
    {
        $this->fileSystem = $fileSystem;
        $this->directoryService = $directoryService;
    }

    /**
     * @param array $paths
     *
     * @return string
     */
    public function setHash(array $paths): string
    {
        $hash = Carbon::now()->getTimestamp();
        session()->put(SessionInterface::FILEVUER_DOWNLOAD.$hash, $paths);
        return (string) $hash;
    }

    /**
     * @param string $hash
     *
     * @return Collection
     */
    public function getHash(string $hash): Collection
    {
        $paths = session(SessionInterface::FILEVUER_DOWNLOAD.$hash);
        session()->forget(SessionInterface::FILEVUER_DOWNLOAD.$hash);
        
        return  collect($paths);
    }

    /**
     * @param array $paths
     *
     * @return StreamedResponse
     */
    public function getDownload(string $hash): StreamedResponse
    {
        $downloads = $this->getHash($hash);

        if ($downloads->count() == 1 && $downloads->pluck('type')->first() == 'file') {
            return $this->downloadSingleFile($downloads->first());
        }
      
        return $this->downloadZipFile($downloads);
    }

    /**
     * @param Collection $downloads
     *
     * @return StreamedResponse
     */
    public function downloadZipFile(Collection $downloads): StreamedResponse
    {
        $zipFilename = $this->getZipFilename();
        $zipStream = new ZipStream($zipFilename);

        return response()->stream(function () use ($zipStream, $downloads) {
            $downloads->each(function ($downloadFile) use ($zipStream) {
                $this->addFilesToZip($downloadFile, $zipStream);
            });
    
            $zipStream->finish();
        }, 200, [
            "Content-Type" => 'application/octet-stream;',
            'Content-Disposition' => 'attachment; filename='.$zipFilename,
        ]);
    }

    /**
     * @param array $file
     * @param ZipStream $zipStream
     *
     * @return void
     */
    public function addFilesToZip(array $file, ZipStream $zipStream): void
    {
        if ($file['type'] == 'dir') {
            $listing = $this->fileSystem->cloud()->listContents($file['path'], true);
            foreach ($listing as $item) {
                $itemInfo = $this->directoryService->formatStorageAttribute($item);
                $this->addFileToZip($zipStream, $itemInfo);
            }
        }

        $this->addFileToZip($zipStream, $file);
    }

    /**
     * @param ZipStream $zipStream
     * @param array $file
     *
     * @return void
     */
    public function addFileToZip(ZipStream $zipStream, array $file): void
    {
        if ($file['type'] == 'dir') {
            return;
        }
        $stream   = $this->fileSystem->cloud()->readStream($file['path']);
        $zipStream->addFileFromStream($file['path'], $stream);
    }

    /**
     * @return string
     */
    public function getZipFilename(): string
    {
        $connectionName = session(SessionInterface::FILEVUER_CONNECTION_NAME);
        return strtolower($connectionName). '_' . Carbon::now()->getTimestamp() . '.zip';
    }

    /**
     * @param array $downloadFile
     *
     * @return StreamedResponse
     */
    public function downloadSingleFile(array $downloadFile): StreamedResponse
    {
        return response()->stream(function () use ($downloadFile) {
            $stream = $this->fileSystem->cloud()->readStream($downloadFile['path']);
            fpassthru($stream);
        }, 200, [
            "Content-Type" => 'application/octet-stream;',
            'Content-Disposition' => 'attachment; filename="'.$downloadFile['basename'].'"',
        ]);
    }
}
