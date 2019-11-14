<?php

namespace Jwhulette\Filevuer\controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Redirect;
use jwhulette\filevuer\services\SessionInterface;
use jwhulette\filevuer\services\ConnectionServiceInterface;
use jwhulette\filevuer\services\ConfigurationServiceInterface;

class FilevuerController extends Controller implements SessionInterface
{
    /**
     * @var ConfigurationServiceInterface
     */
    protected $configurationService;

    /**
     * @var ConnectionServiceInterface
     */
    protected $connectionService;

    /**
     * __construct
     *
     * @param ConfigurationServiceInterface $configurationService
     * @param ConnectionServiceInterface $connectionService
     */
    public function __construct(ConfigurationServiceInterface $configurationService, ConnectionServiceInterface $connectionService)
    {
        $this->configurationService = $configurationService;
        $this->connectionService    = $connectionService;
    }

    /**
     * index
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $this->development();
        return view('filevuer::index', [
            'connections' => $this->configurationService->getConnectionDisplayList()->toJson(),
            'loggedIn'    => session()->get(SessionInterface::FILEVUER_LOGGEDIN, false)  ? 'true' : 'false',
            'selected'    => session()->get(SessionInterface::FILEVUER_CONNECTION_NAME, '')
        ]);
    }

    /**
     * When developing the applicaton
     * copy the updated files over on page refresh
     *
     * @return void
     */
    private function development()
    {
        if (\App::environment('local')) {
            \Artisan::call('vendor:publish', [
                '--tag' => 'filevuer',
                '--force' => 1
            ]);
        }
    }

    /**
     * Set the remote directory connection
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function connect(Request $request)
    {
        $config = $this->configurationService->getSelectedConnection($request->connection);
        $result = $this->connectionService->connectToService($config);
        if ($result) {
            session()->put(SessionInterface::FILEVUER_LOGGEDIN, true);
        }
        
        return response()->json($result);
    }

    /**
     * Undocumented function
     *
     * @return \Illuminate\Support\Facades\Redirect
     */
    public function logout()
    {
        $this->connectionService->logout();
        return redirect()->route('filevuer.index');
    }

    /**
     * Check to see if the user is logged in
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function poll()
    {
        return response()->json([
            'active' => session()->get(SessionInterface::FILEVUER_LOGGEDIN, false)
        ]);
    }
}
