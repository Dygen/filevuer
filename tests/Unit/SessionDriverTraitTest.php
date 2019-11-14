<?php

namespace jwhulette\filevuer\Tests\Unit;

use jwhulette\filevuer\Tests\TestCase;
use jwhulette\filevuer\services\SessionInterface;
use jwhulette\filevuer\traits\SessionDriverTrait;

class SessionDriverTraitTest extends TestCase
{
    use SessionDriverTrait;

    public function setUp()
    {
        parent::setUp();
    }

    public function testSetSessionDataFtp()
    {
        $data = $this->dummyConnections()['FTP'][0];
        $this->setSessionData($data);

        $this->assertEquals('FTP1', session(SessionInterface::FILEVUER_CONNECTION_NAME, $data['name']));
        $this->assertEquals($data, decrypt(session(SessionInterface::FILEVUER_DATA)));
    }

    public function testSetSessionDataS3()
    {
        $data = $this->dummyConnections()['S3'][0];
        $this->setSessionData($data);

        $this->assertEquals('AWSS3', session(SessionInterface::FILEVUER_CONNECTION_NAME, $data['name']));
        $this->assertEquals($data, decrypt(session(SessionInterface::FILEVUER_DATA)));
    }

    public function testApplyFtp()
    {
        $data = $this->dummyConnections()['FTP'][0];
        $this->setSessionData($data);
        session()->put(SessionInterface::FILEVUER_HOME_DIR, $data['home_dir']);
        
        $this->assertEquals('public_html', session(SessionInterface::FILEVUER_HOME_DIR));
    }

    public function testApplyS3()
    {
        $data = $this->dummyConnections()['S3'][0];
        $this->setSessionData($data);
        session()->put(SessionInterface::FILEVUER_HOME_DIR, $data['home_dir']);
        
        $this->assertEquals('/test', session(SessionInterface::FILEVUER_HOME_DIR));
    }
}
