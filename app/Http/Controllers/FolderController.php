<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShowFolderRequest;
use App\Http\Requests\StoreFolderRequest;
use App\Models\File;
use App\Models\Folder;
use App\Services\FolderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FolderController extends Controller
{
    /**
     * Shows root folder of user's storage
     *
     * @param Request $request
     * @return View
     */
    public function root(Request $request)
    {
        $folders = Folder::getRootFolders($request->user()->storage);
        $files = File::getRootFiles($request->user()->storage);
        $breadcrumbs = FolderService::breadcrumbs();

        return view('folders.show', compact('folders', 'files', 'breadcrumbs'));
    }

    /**
     * Shows folder's content (except root folder)
     *
     * @param ShowFolderRequest $request
     * @param Folder $folder
     * @return View
     */
    public function show(ShowFolderRequest $request, Folder $folder)
    {
        $folders = $folder->subFolders;
        $files = $folder->files;

        $parent = $folder;
        $breadcrumbs = FolderService::breadcrumbs($parent);

        return view('folders.show', compact('parent', 'folders', 'breadcrumbs', 'files'));
    }

    /**
     * Create new folder
     *
     * @param StoreFolderRequest $request
     * @return RedirectResponse
     */
    public function store(StoreFolderRequest $request)
    {
        $storage = $request->user()->storage;
        $name = $request->name;
        $parent_id = $request->parent;

        FolderService::create($storage, $name, $parent_id);

        return redirect()->back();
    }
}
