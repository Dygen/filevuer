<?php

namespace jwhulette\filevuer\Tests\Unit;

use Exception;
use RuntimeException;
use Illuminate\Http\UploadedFile;
use jwhulette\filevuer\Tests\TestCase;
use Illuminate\Filesystem\FilesystemManager;
use jwhulette\filevuer\services\UploadServiceInterface;

class UploadServiceTest extends TestCase
{
    protected $tmpPath;

    public function setUp(): void
    {
        parent::setUp();
        $this->tmpPath = sys_get_temp_dir();
    }

    public function testSingleFileUpload()
    {
        $filesystem = $this->getMockBuilder(FilesystemManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['cloud', 'put'])
            ->getMock();
        $filesystem->method('cloud')
            ->will($this->returnSelf());
        $filesystem->method('put')
            ->willReturn(true);
        $this->app->instance(FilesystemManager::class, $filesystem);
        $service = app()->make(UploadServiceInterface::class);
        $files = [
            UploadedFile::fake()->create('document.pdf', 20000),
        ];
        
        $service->uploadFiles('/test', $files);
        $this->assertTrue(true);
    }

    public function testSingleFileUploadFaild()
    {
        $this->expectException(Exception::class, 'Error uploading file');

        $filesystem = $this->getMockBuilder(FilesystemManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['cloud','put'])
            ->getMock();
        $filesystem->method('cloud')
            ->will($this->returnSelf());
        $filesystem->method('put')
            ->willReturn(false);
        $this->app->instance(FilesystemManager::class, $filesystem);
        $service = app()->make(UploadServiceInterface::class);
        $files = [
            UploadedFile::fake()->create('document.pdf', 20000),
        ];
        
        $service->uploadFiles('/test', $files);
    }

    public function testSingleFileUploadZip()
    {
        $filesystem = $this->getMockBuilder(FilesystemManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['cloud','put','makeDirectory'])
            ->getMock();
        $filesystem->method('cloud')
            ->will($this->returnSelf());
        $filesystem->method('put')
            ->willReturn(true);
        $filesystem->method('makeDirectory')
            ->willReturn(true);
        $this->app->instance(FilesystemManager::class, $filesystem);
        $service = app()->make(UploadServiceInterface::class);
        $testZip = $this->createTestZip();
        $service->uploadFiles('/test', [$testZip], false);
        
        $this->assertTrue(true);
    }

    public function testSingleFileUploadZipExtracted()
    {
        $filesystem = $this->getMockBuilder(FilesystemManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['cloud','put','makeDirectory'])
            ->getMock();
        $filesystem->method('cloud')
            ->will($this->returnSelf());
        $filesystem->method('put')
            ->willReturn(true);
        $filesystem->method('makeDirectory')
            ->willReturn(true);
        $this->app->instance(FilesystemManager::class, $filesystem);
        $service = app()->make(UploadServiceInterface::class);
        $testZip = $this->createTestZip();
        $service->uploadFiles('/test', [$testZip], true);
        
        $this->assertTrue(true);
    }

    public function testSingleFileUploadZipCreateRemoteFileFailed()
    {
        $this->expectException(RuntimeException::class, 'Error creating file on server');
        $filesystem = $this->getMockBuilder(FilesystemManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['cloud','put','makeDirectory'])
            ->getMock();
        $filesystem->method('cloud')
            ->will($this->returnSelf());
        $filesystem->method('put')
            ->willReturn(false);
        $filesystem->method('makeDirectory')
            ->willReturn(true);
        $this->app->instance(FilesystemManager::class, $filesystem);
        $service = app()->make(UploadServiceInterface::class);
        $testZip = $this->createTestZip();
        $service->uploadFiles('/test', [$testZip], true);
    }

    public function testSingleFileUploadCorruptZip()
    {
        $this->expectException(RuntimeException::class, 'Failed to extract zip archive.');
        $filesystem = $this->getMockBuilder(FilesystemManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['cloud','put','makeDirectory'])
            ->getMock();
        $filesystem->method('cloud')
            ->will($this->returnSelf());
        $filesystem->method('put')
            ->willReturn(true);
        $filesystem->method('makeDirectory')
            ->willReturn(true);
        $this->app->instance(FilesystemManager::class, $filesystem);
        $service = app()->make(UploadServiceInterface::class);
        $testZip = $this->createCorruptTestZip();
        $service->uploadFiles('/test', [$testZip], true);
    }

    private function copyZip($file)
    {
        copy(dirname(__DIR__) . '/Assets/'.$file, $this->tmpPath.$file);
        chmod($this->tmpPath.$file, 0777);
    }

    private function createTestZip()
    {
        $this->copyZip('/testArchive.zip');
        return new UploadedFile(
            $this->tmpPath.'/testArchive.zip',
            'testArchive.zip',
            'application/zip',
            3066,
            true
        );
    }

    private function createCorruptTestZip()
    {
        $this->copyZip('/testCorruptArchive.zip');
        return new UploadedFile(
            $this->tmpPath.'/testCorruptArchive.zip',
            'testCorruptArchive.zip',
            'application/zip',
            3066,
            true
        );
    }
}
