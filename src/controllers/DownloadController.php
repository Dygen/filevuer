<?php

namespace Jwhulette\Filevuer\controllers;

use App\Helpers\Zipper;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use jwhulette\filevuer\services\DownloadServiceInterface;

/**
 * DownloadController Class
 */
class DownloadController extends Controller
{
    private $downloadService;

    /**
     *  __construct
     *
     * @param DownloadServiceInterface $downloadService
     */
    public function __construct(DownloadServiceInterface $downloadService)
    {
        $this->downloadService = $downloadService;
    }

    /**
     * Return an encrypted path request to send to the get response for download
     *
     * @param Request $request
     *
     * @return Response
     */
    public function generate(Request $request)
    {
        $hash = $this->downloadService->setHash($request->path);
        return response($hash);
    }


    /**
     * Send the requested file(s) to download
     *
     * @param string $hash
     *
     * @return StreamedResponse
     */
    public function download($hash)
    {
        return $this->downloadService->getDownload($hash);
    }
}
