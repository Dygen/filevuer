<?php

namespace Jwhulette\Filevuer\controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use jwhulette\filevuer\services\DirectoryServiceInterface;

/**
 * DirectoryController Class
 */
class DirectoryController extends Controller
{
    /**
     * @var DirectoryServiceInterface
     */
    private $directory;

    /**
     * __construct.
     *
     * @param DirectoryServiceInterface $directory
     */
    public function __construct(DirectoryServiceInterface $directory)
    {
        $this->directory = $directory;
    }

    /**
     * List directorys.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $path = $request->get('path', '/');

        return response([
            'listing' => $this->directory->listing($path),
        ], 200);
    }

    /**
     * Create directroy.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $path = $request->get('path', '');

        return response([
            'success' => $this->directory->create($path),
        ], 201);
    }

    /**
     * Delete directory.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $path = $request->input('path', null);

        $this->directory->delete($path);

        return response([
            'success' => 'Directory deleted',
        ], 200);
    }
}
