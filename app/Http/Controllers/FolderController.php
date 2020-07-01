<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShowFolderRequest;
use App\Http\Requests\StoreFolderRequest;
use App\Models\File;
use App\Models\Folder;
use App\Services\FolderService;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    /**
     * Shows root folder of user's storage
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function root(Request $request)
    {
        $folders = Folder::getRootFolders($request->user()->storage);
        $files = File::getRootFiles($request->user()->storage);

        return view('folders.show', compact('folders', 'files'));
    }

    /**
     * Shows folder's content (except root folder)
     *
     * @param ShowFolderRequest $request
     * @param Folder $folder
     * @return \Illuminate\View\View
     */
    public function show(ShowFolderRequest $request, Folder $folder)
    {
        $folders = $folder->subFolders;
        $files = $folder->files;

        $parent = $folder;
        $path = FolderService::getPath($parent);

        return view('folders.show', compact('parent', 'folders', 'path', 'files'));
    }

    /**
     * Create new folder
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreFolderRequest $request)
    {
        $storage = $request->user()->storage;
        $name = $request->name;
        $parent_id = $request->parent;

        FolderService::store($storage, $name, $parent_id);

        return redirect()->back();
    }
}
