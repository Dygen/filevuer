<?php

namespace jwhulette\filevuer\Tests\Unit;

use ZipStream\ZipStream;
use Illuminate\Http\UploadedFile;
use jwhulette\filevuer\Tests\TestCase;
use Illuminate\Filesystem\FilesystemManager;
use jwhulette\filevuer\services\SessionInterface;
use jwhulette\filevuer\services\DownloadServiceInterface;
use jwhulette\filevuer\services\DirectoryServiceInterface;

class DownloadServiceTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testAddFilesToZip()
    {
        $file = $this->dummyListingNewVersion()[0];
        $files = $this->dummyListingNewVersion();

        $filesystem = $this->getMockBuilder(FilesystemManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['cloud', 'listContents','readStream'])
            ->getMock();
        $filesystem->method('cloud')
            ->will($this->returnSelf());
        $filesystem->method('listContents')
            ->willReturn($files);
        $filesystem->method('readStream')
            ->willReturn('xyz');
        $this->app->instance(FilesystemManager::class, $filesystem);

        $zipStream = $this->createMock(ZipStream::class);
        $zipStream->method('addFileFromStream');
        $this->app->instance(ZipStream::class, $filesystem);

        $service = app()->make(DownloadServiceInterface::class);
        $directoryService = app()->make(DirectoryServiceInterface::class);
        session()->put(SessionInterface::FILEVUER_CONNECTION_NAME, 'testZip');
        $service->addFilesToZip($directoryService->formatStorageAttribute($file), $zipStream);

        $this->assertTrue(true);
    }
}
