<?php

namespace jwhulette\filevuer\services;

interface SessionInterface
{
    const FILEVUER_DRIVER          = 'filevuerDriver';
    const FILEVUER_LOGGEDIN        = 'filevuerLoggedIn';
    const FILEVUER_DATA            = 'filevuerData';
    const FILEVUER_HOME_DIR        = 'filevuerHomeDirectory';
    const FILEVUER_CONNECTION_NAME = 'filevuerConnectionName';
    const FILEVUER_DOWNLOAD        = 'filevuerDownload';
}
