<?php

namespace jwhulette\filevuer\Tests\Feature;

use ZipStream\ZipStream;
use jwhulette\filevuer\Tests\TestCase;
use Illuminate\Filesystem\FilesystemManager;
use jwhulette\filevuer\services\DownloadService;
use jwhulette\filevuer\services\SessionInterface;
use jwhulette\filevuer\services\DownloadServiceInterface;

class DownloadControllerTest extends TestCase
{
    public function testGenerate()
    {
        $response = $this->withSession($this->getSessionValues())->post(route('filevuer.generate'), [ 'path' => ['/test', '/test2']]);

        $response->assertSessionHas(SessionInterface::FILEVUER_DOWNLOAD.$response->getContent());
        $response->assertStatus(200);
    }

    public function testDownloadSingleFile()
    {
        session()->put(SessionInterface::FILEVUER_DOWNLOAD.'123456', [ [
            'type'       => 'file',
            'path'       => 'fileA.txt',
            'visibility' => 'public',
            'size'       => '30 bytes',
            'dirname'    => '',
            'basename'   => 'fileA.txt',
            'extension'  => 'txt',
            'filename'   => 'fileA',
        ]]);
        $response = $this->withSession($this->getSessionValues())->get(route('filevuer.download', ['hash' => '123456']));

        $response->assertStatus(200);
        $this->assertEquals('application/octet-stream;', $response->headers->get('Content-Type'));
        $this->assertContains('attachment; filename=', $response->headers->get('Content-Disposition'));
    }

    public function testDownloadMulitFile()
    {
        $files = $this->dummyListing();

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
        $zipStream->method('addFileFromStream')
            ->willReturn(true);
        $this->app->instance(ZipStream::class, $filesystem);

        $service = app()->make(DownloadServiceInterface::class);
        session()->put(SessionInterface::FILEVUER_DOWNLOAD.'123456', $files);
        
        $response = $this->withSession($this->getSessionValues())->get(route('filevuer.download', ['hash' => '123456']));
        $response->assertStatus(200);
        $this->assertEquals('application/octet-stream;', $response->headers->get('Content-Type'));
        $this->assertContains('attachment; filename=', $response->headers->get('Content-Disposition'));
    }
}
