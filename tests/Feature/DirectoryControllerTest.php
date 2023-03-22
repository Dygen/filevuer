<?php

namespace jwhulette\filevuer\Tests\Feature;

use Carbon\Carbon;
use jwhulette\filevuer\Tests\TestCase;
use Illuminate\Filesystem\FilesystemManager;

class DirectoryControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $filesystem = $this->getMockBuilder(FilesystemManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['cloud', 'listContents', 'makeDirectory', 'deleteDirectory'])
            ->getMock();
        $filesystem->method('cloud')
            ->will($this->returnSelf());
        $filesystem->method('listContents')
            ->willReturn($this->dummyListing());
        $filesystem->method('makeDirectory')
            ->willReturn(true);
        $filesystem->method('deleteDirectory')
            ->willReturn(true);
        $this->app->instance(FilesystemManager::class, $filesystem);
    }

    public function testIndex()
    {
        Carbon::setTestNow(Carbon::create(2020, 1, 1));
        $response = $this->withSession($this->getSessionValues())->get(route('filevuer.directory'), ['path' => '/']);

        $response->assertStatus(200);
        $expectedItems = [
            [
                'basename' => "Directory A",
                'path' => "Directory A",
                'visibility' => "public",
                'type' => "dir",
            ],
            [
                'basename' => "fileA.txt",
                'path' => "fileA.txt",
                'visibility' => "public",
                'type' => "file",
                'size' => "30 bytes",
                'extension' => 'txt'
            ],
            [
                'basename' => "fileB.txt",
                'path' => "fileB.txt",
                'visibility' => "public",
                'type' => "file",
                'size' => "10 bytes",
                'extension' => 'txt'
            ],
            [
                'basename' => "fileC.txt",
                'path' => "fileC.txt",
                'visibility' => "public",
                'type' => "file",
                'size' => "0 bytes",
                'extension' => 'txt'
            ],
        ];
        $this->assertEquals(json_encode(['listing' => $expectedItems]), $response->getContent());
    }

    public function testCreate()
    {
        $response = $this->withSession($this->getSessionValues())->post(route('filevuer.directory'), ['path' => 'dir/subdir']);

        $response->assertStatus(201);
        $this->assertEquals('{"success":true}', $response->getContent());
    }

    public function testDelete()
    {
        $response = $this->withSession($this->getSessionValues())->delete(route('filevuer.directory'), ['path' => ['dir/subdir']]);

        $response->assertStatus(200);
        $this->assertEquals('{"success":"Directory deleted"}', $response->getContent());
    }
}
