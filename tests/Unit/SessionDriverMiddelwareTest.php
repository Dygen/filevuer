<?php

namespace jwhulette\filevuer\Tests\Unit;

use Illuminate\Http\Request;
use jwhulette\filevuer\Tests\TestCase;
use jwhulette\filevuer\middleware\SessionDriver;
use jwhulette\filevuer\services\SessionInterface;

class SessionDriverMiddelwareTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testMiddleware()
    {
        $request = new Request();
        $middleware = new SessionDriver;
        session()->put(SessionInterface::FILEVUER_LOGGEDIN, true);
        session()->put($this->getSessionValues());
        
        $middleware->handle($request, function ($request) {
            $this->assertEquals('ftp', session()->get(SessionInterface::FILEVUER_DRIVER));
        });
    }
}
