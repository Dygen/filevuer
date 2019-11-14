<?php

namespace jwhulette\filevuer\Traits;

use jwhulette\filevuer\services\SessionInterface;

trait SessionDriverTrait
{
    /**
     * @param array $data
     *
     * @return void
     */
    public function setSessionData(array $data): void
    {
        session()->put(SessionInterface::FILEVUER_CONNECTION_NAME, $data['name']);
        $data = encrypt($data);
        session()->put(SessionInterface::FILEVUER_DATA, $data);
    }

    /**
     * @throws RuntimeException
     *
     * @return void
     */
    public function applyConfiguration(): void
    {
        $driver = session()->get(SessionInterface::FILEVUER_DRIVER);
        $data   = decrypt(session()->get(SessionInterface::FILEVUER_DATA));
        $this->setHomeDirectory($data['home_dir']);

        switch (strtolower($driver)) {
            case 's3':
                config([
                    'filesystems.cloud'     => 's3',
                    'filesystems.disks.s3' => [
                        'driver' => 's3',
                        'key'    => $data['key'],
                        'secret' => $data['secret'],
                        'region' => $data['region'],
                        'bucket' => $data['bucket'],
                    ]
                ]);
            break;

            case 'ftp':
                config([
                    'filesystems.cloud'     => 'ftp',
                    'filesystems.disks.ftp' => [
                        'driver'   => 'ftp',
                        'host'     => $data['host'],
                        'username' => $data['username'],
                        'password' => $data['password'],
                        'port'     => $data['port'],
                    ]
                ]);
            break;
        }
    }

    /**
     * @param string $homeDirectory
     *
     * @return void
     */
    public function setHomeDirectory(?string $homeDirectory): void
    {
        $homeDir = '';
        if (!is_null($homeDirectory)) {
            $homeDir = sprintf('/%s/', trim($homeDirectory, '/'));
        }

        session()->put(SessionInterface::FILEVUER_HOME_DIR, $homeDir);
    }

    /**
     * @param string|null $path
     *
     * @return string|null
     */
    public function getFullPath(?string $path): ?string
    {
        return session(SessionInterface::FILEVUER_HOME_DIR) . $path;
    }
}
