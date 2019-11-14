<?php

namespace jwhulette\filevuer\middleware;

use Closure;
use jwhulette\filevuer\services\SessionInterface;
use jwhulette\filevuer\traits\SessionDriverTrait;
use jwhulette\filevuer\services\ConnectionService;

class SessionDriver
{
    use SessionDriverTrait;

    /**
     * Apply the login data from the session storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $loggedIn = session()->get(SessionInterface::FILEVUER_LOGGEDIN, false)  ? 'true' : 'false';
        if ('true' === $loggedIn) {
            $this->applyConfiguration();
        }

        return $next($request);
    }
}
