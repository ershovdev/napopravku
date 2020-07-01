<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteFileRequest;
use App\Http\Requests\RenameFileRequest;
use App\Http\Requests\ShowFileRequest;
use App\Http\Requests\StoreFileRequest;
use App\Models\File;
use App\Services\FolderService;
use Illuminate\Support\Facades\Storage as StorageFacade;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File as FileFacade;

class FileController extends Controller
{
    public function hostFile(Request $request, File $file)
    {
        return FileService::getPrivateFileResponse($request->user()->storage, $file);
    }

    public function show(ShowFileRequest $request, File $file)
    {
        if (!$file->folder) $left_part = '/';
        else $left_part = FolderService::getPath($file->folder);

        $path = $left_part . $file->name;
        return view('files.show', compact('file', 'path'));
    }

    public function download(Request $request, File $file)
    {
        $storage = $request->user()->storage;
        $path = $storage->name . '/' . $file->uniq_id . '.' . $file->extension;

        return StorageFacade::disk('local')->download($path, $file->name);
    }

    public function store(StoreFileRequest $request)
    {
        $storage = $request->user()->storage;
        $result = FileService::store($storage, $request->folder, $request->file);
        return redirect()->back()->with($result ? 'success' : 'error', $result ? 'Created!' : 'Something went wrong');
    }

    public function makePublic()
    {
        //
    }

    public function rename(RenameFileRequest $request, File $file)
    {
        $result = FileService::rename($file, $request->name);
        $message = $result ? 'Renamed!' : 'File with the same name already exists';
        return redirect()->back()->with($result ? 'success' : 'error', $message);
    }

    public function delete(DeleteFileRequest $request, File $file)
    {
        $result = FileService::delete($request->user()->storage, $file);
        return redirect()->back()->with($result ? 'success' : 'error', $result ? 'Deleted!' : 'Something went wrong');
    }
}
