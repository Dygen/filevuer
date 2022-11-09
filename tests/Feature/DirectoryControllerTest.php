<?php

namespace jwhulette\filevuer\Tests\Feature;

use jwhulette\filevuer\Tests\TestCase;
use Illuminate\Contracts\Filesystem\Cloud;
use Illuminate\Filesystem\FilesystemManager;

class DirectoryControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $filesystem = $this->getMockBuilder(FilesystemManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['cloud', 'listContents','createDir', 'deleteDir'])
            ->getMock();
        $filesystem->method('cloud')
            ->will($this->returnSelf());
        $filesystem->method('listContents')
            ->willReturn($this->dummyListingPreformat());
        $filesystem->method('createDir')
            ->willReturn(true);
        $filesystem->method('deleteDir')
            ->willReturn(true);
        $this->app->instance(FilesystemManager::class, $filesystem);
    }

    public function testIndex()
    {
        $response = $this->withSession($this->getSessionValues())->get(route('filevuer.directory'), [ 'path' => '/']);

        $response->assertStatus(200);
        $this->assertEquals($response->getContent(), json_encode(['listing' => $this->dummyListing()]));
    }

    public function testCreate()
    {
        $response = $this->withSession($this->getSessionValues())->post(route('filevuer.directory'), [ 'path' => 'dir/subdir']);

        $response->assertStatus(201);
        $this->assertEquals('{"success":true}', $response->getContent());
    }

    public function testDelete()
    {
        $response = $this->withSession($this->getSessionValues())->delete(route('filevuer.directory'), [ 'path' => ['dir/subdir']]);

        $response->assertStatus(200);
        $this->assertEquals('{"success":"Directory deleted"}', $response->getContent());
    }
}
