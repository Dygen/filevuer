<?php

namespace jwhulette\filevuer\Tests\Feature;

use Illuminate\Filesystem\FilesystemManager;
use jwhulette\filevuer\services\SessionInterface;
use jwhulette\filevuer\Tests\TestCase;

class FileControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $filesystem = $this->getMockBuilder(FilesystemManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['cloud', 'read', 'put', 'delete'])
            ->getMock();
        $filesystem->method('cloud')
            ->will($this->returnSelf());
        $filesystem->method('read')
            ->willReturn('xyz');
        $filesystem->method('put')
            ->willReturn(true);
        $filesystem->method('delete')
            ->willReturn(true);
        $this->app->instance(FilesystemManager::class, $filesystem);
    }

    public function testShow()
    {
        $response = $this->withSession($this->getSessionValues())
            ->get(route('filevuer.file.show'), ['path' => '']);

        $response->assertStatus(200);
        $this->assertEquals(json_encode([
            'contents' => 'xyz',
            'download' => false
        ]), $response->getContent());
    }

    public function testShowFailed()
    {
        session()->forget(SessionInterface::FILEVUER_HOME_DIR);
        $response = $this->put(route('filevuer.file.show'), ['path' => null]);

        $response->assertStatus(500);
    }


    public function testCreate()
    {
        $response = $this->withSession($this->getSessionValues())->post(route('filevuer.file.create'), ['path' => '']);

        $response->assertStatus(201);
        $this->assertEquals('{"success":true}', $response->getContent());
    }

    public function testUpdate()
    {
        $response = $this->withSession($this->getSessionValues())->put(route('filevuer.file.update'), ['path' => '', 'contents' => 'new contents']);

        $response->assertStatus(200);
        $this->assertEquals('{"success":true}', $response->getContent());
    }

    public function testDelete()
    {
        $response = $this->withSession($this->getSessionValues())->delete(route('filevuer.file.destroy'), ['path' => ['test.txt']]);

        $response->assertStatus(200);
        $this->assertEquals('{"success":true}', $response->getContent());
    }
}
