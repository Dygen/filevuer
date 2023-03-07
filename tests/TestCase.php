<?php

namespace jwhulette\filevuer\Tests;

use jwhulette\filevuer\FileVuerServiceProvider;
use jwhulette\filevuer\services\SessionInterface;
use League\Flysystem\DirectoryAttributes;
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
        $dummyData = [];

        $dummyDirectory = new DirectoryAttributes(
            'Directory A', // path to the directory in your storage system
            'public', //visibility
            now()->getTimestamp() // timestamp of when the directory was last modified
        );

        $dummyFileA = new FileAttributes(
            'fileA.txt', // path to the file in your storage system
            30, // size of the file in bytes
            'public', //visibility
            now()->getTimestamp(), // timestamp of when the file was last modified
            'text/plain' // type of file (either 'file' or 'dir')
        );

        $dummyFileB = new FileAttributes(
            'fileB.txt', // path to the file in your storage system
            10, // size of the file in bytes
            'public', //visibility
            now()->getTimestamp(), // timestamp of when the file was last modified
            'text/plain' // type of file (either 'file' or 'dir')
        );

        $dummyFileC = new FileAttributes(
            'fileC.txt', // path to the file in your storage system
            0, // size of the file in bytes
            'public', //visibility
            now()->getTimestamp(), // timestamp of when the file was last modified
            'text/plain' // type of file (either 'file' or 'dir')
        );

        array_push($dummyData, $dummyDirectory, $dummyFileA, $dummyFileB, $dummyFileC);
        return $dummyData;
    }
}
