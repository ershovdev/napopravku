<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShowFolderRequest;
use App\Models\File;
use App\Models\Folder;
use App\Services\FolderService;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    public function root(Request $request)
    {
        $folders = Folder::where([
            ['parent_id', null],
            ['storage_id', $request->user()->storage->id],
        ])->get();

        $files = File::where([
            ['folder_id', null],
            ['storage_id', $request->user()->storage->id],
        ])->get();

//        dd($files);

        return view('folders.show', compact('folders', 'files'));
    }

    public function show(ShowFolderRequest $request, Folder $folder)
    {
        $folders = $folder->subFolders;
        $files = $folder->files;

        $parent = $folder;
        $path = FolderService::getPath($parent);

        return view('folders.show', compact('parent', 'folders', 'path', 'files'));
    }

    public function store(Request $request)
    {
        $storage = $request->user()->storage;
        $name = $request->name;
        $parent_id = $request->parent;

        FolderService::store($storage, $name, $parent_id);

        return redirect()->back();
    }
}
