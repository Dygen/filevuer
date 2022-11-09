<?php

namespace jwhulette\filevuer\Tests\Feature;

use RuntimeException;
use Illuminate\Http\UploadedFile;
use jwhulette\filevuer\Tests\TestCase;
use jwhulette\filevuer\services\UploadServiceInterface;

class UploadFileControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testUploadNoFiles()
    {
        $response = $this->withSession($this->getSessionValues())->post(route('filevuer.upload'), [
            'path' => '/test',
            'files' => []
            ]);

        $response->assertStatus(500);
    }

    public function testUploadFailed()
    {
        $upload = $this->createMock(UploadServiceInterface::class);
        $upload->method('uploadFiles');
        $this->app->instance(UploadServiceInterface::class, $upload);

        $response = $this->withSession($this->getSessionValues())->post(route('filevuer.upload'), [
            'path'  => '/test',
            'files' => null
            ]);

        $response->assertStatus(500);
    }

    public function testUpload()
    {
        $files = [
            UploadedFile::fake()->create('document.pdf', 20000),
        ];
        $upload = $this->createMock(UploadServiceInterface::class);
        $upload->method('uploadFiles');
        $this->app->instance(UploadServiceInterface::class, $upload);

        $response = $this->withSession($this->getSessionValues())->post(route('filevuer.upload'), [
            'path'  => '/test',
            'files' => $files
            ]);

        $response->assertStatus(200);
    }

    public function testUploadFailedZip()
    {
        $files = [
            UploadedFile::fake()->create('document.pdf', 20000),
        ];
        $upload = $this->createMock(UploadServiceInterface::class);
        $upload->method('uploadFiles')
            ->will($this->throwException(new RuntimeException()));
        $this->app->instance(UploadServiceInterface::class, $upload);

        $response = $this->withSession($this->getSessionValues())->post(route('filevuer.upload'), [
            'path'    => '/test',
            'files'   => $files,
            'extract' => true,
            ]);

        $response->assertStatus(500);
    }
}
