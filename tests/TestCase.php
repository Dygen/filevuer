<?php

namespace jwhulette\filevuer\Tests;

use jwhulette\filevuer\FileVuerServiceProvider;
use jwhulette\filevuer\services\SessionInterface;
use Orchestra\Testbench\TestCase as BaseTestCase;
use \League\Flysystem\FileAttributes;

abstract class TestCase extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    
    protected function getPackageProviders($app)
    {
        return [
            FileVuerServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app->config->set('filevuer.connections', $this->dummyConnections());
        $app->config->set('view.paths', [__DIR__ . '/../src/resources/views']);
        $app->config->set('routes', \jwhulette\filevuer\Filevuer::routes());
    }

    protected function getSessionValues()
    {
        return [
            SessionInterface::FILEVUER_DRIVER => 'ftp',
            SessionInterface::FILEVUER_LOGGEDIN => 'true',
            SessionInterface::FILEVUER_DATA => encrypt([
                'name'     => 'FTP1',
                'host'     => 'ftp.host1.com',
                'username' => 'ftp1',
                'password' => 'ftp',
                'port'     => 21,
                'home_dir' => "public_html",
            ]),
            SessionInterface::FILEVUER_HOME_DIR => 'public_html',
            SessionInterface::FILEVUER_CONNECTION_NAME => 'FTP1'
        ];
    }

    protected function getSessionValuesS3()
    {
        return [
            SessionInterface::FILEVUER_DRIVER => 's3',
            SessionInterface::FILEVUER_LOGGEDIN => 'true',
            SessionInterface::FILEVUER_DATA =>                 [
                'name'     => 'AWSS3',
                'key'      => 'aul;kjaer',
                'secret'   => 'alkdfjiei',
                'bucket'   => 'my-bucket',
                'region'   => 'us-east-1',
                'home_dir' => '/test',
            ],
            SessionInterface::FILEVUER_HOME_DIR => '/test',
            SessionInterface::FILEVUER_CONNECTION_NAME => 'AWSS3'
        ];
    }

    protected function dummyConnections()
    {
        return [
            'FTP' => [
                [
                    'name'     => 'FTP1',
                    'host'     => 'ftp.host1.com',
                    'username' => 'ftp1',
                    'password' => 'ftp',
                    'port'     => 21,
                    'home_dir' => "public_html",
                ],
            ],
    
            'S3' => [
                [
                    'name'     => 'AWSS3',
                    'key'      => 'aul;kjaer',
                    'secret'   => 'alkdfjiei',
                    'bucket'   => 'my-bucket',
                    'region'   => 'us-east-1',
                    'home_dir' => '/test',
                ],
            ]
        ];
    }


    protected function dummyListing()
    {
        return [
            [
                'type'     => 'dir',
                'path'     => 'Directory A',
                'dirname'  => '',
                'visibility' => null,
            ],
            [
                'type'       => 'file',
                'path'       => 'fileA.txt',
                'visibility' => 'public',
                'dirname'  => '',
                'file_size'  => '30 bytes',
            ],
            [
                'type'       => 'file',
                'path'       => 'fileB.txt',
                'visibility' => 'public',
                'dirname'  => '',
                'file_size'  => '30 bytes',
            ],
            [
                'type'       => 'file',
                'path'       => 'fileC.txt',
                'visibility' => 'public',
                'dirname'  => '',
                'file_size'  => '0 bytes',
            ],
        ];
    }


    protected function dummyListingPreformat()
    {
        return [
            [
                'type'     => 'dir',
                'path'     => 'Directory A',
                'dirname'  => '',
            ],
            [
                'type'       => 'file',
                'path'       => 'fileA.txt',
                'visibility' => 'public',
                'dirname'  => '',
                'file_size'  => 30,
            ],
            [
                'type'       => 'file',
                'path'       => 'fileB.txt',
                'visibility' => 'public',
                'dirname'  => '',
                'file_size'  => 30,
            ],
            [
                'type'       => 'file',
                'path'       => 'fileC.txt',
                'visibility' => 'public',
                'dirname'  => '',
                'file_size'  => 0,
            ],
        ];
    }

    protected function dummyListingNewVersion()
    {
        $dummyData = [];

        $dummyFile = new FileAttributes(
            'fileA.txt', // path to the file in your storage system
            30, // size of the file in bytes
            'public', //visibility
            now()->getTimestamp(), // timestamp of when the file was last modified
            'text/plain' // type of file (either 'file' or 'dir')
        );

        $dummyData[] = $dummyFile;
        return $dummyData;
    }
}
