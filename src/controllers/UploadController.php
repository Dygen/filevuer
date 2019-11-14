<?php

namespace Jwhulette\Filevuer\controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use jwhulette\filevuer\services\UploadServiceInterface;

/**
 * UploadController Class
 */
class UploadController extends Controller
{
    /**
     * @var UploadServiceInterface
     */
    protected $uploadService;

    /**
     * __construct
     *
     * @param UploadServiceInterface $uploadService
     */
    public function __construct(UploadServiceInterface $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * Upload a file or folder
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function upload(Request $request)
    {
        $files = $request->hasFile('files') ? $request->allFiles()['files'] : [];
        if (count($files) < 1) {
            return response('No files received', 500);
        }

        try {
            $this->uploadService->uploadFiles($request->path, $request->allFiles()['files'], (bool) $request->extract);
        } catch (\Exception $e) {
            \Log::error($e);
            return response($e->getMessage(), 500);
        }

        return response('OK', 200);
    }
}
