<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteFileRequest;
use App\Http\Requests\DownloadFileRequest;
use App\Http\Requests\RenameFileRequest;
use App\Http\Requests\ShowFileRequest;
use App\Http\Requests\StoreFileRequest;
use App\Models\File;
use App\Services\BreadcrumbsService;
use App\Services\FolderService;
use Illuminate\Support\Facades\Storage as StorageFacade;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File as FileFacade;

class FileController extends Controller
{
    /**
     * Get response for the private file hosting
     *
     * @param Request $request
     * @param File $file
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function hostFile(Request $request, File $file)
    {
        return FileService::getPrivateFileResponse($request->user()->storage, $file);
    }

    /**
     * Show file
     *
     * @param ShowFileRequest $request
     * @param File $file
     * @return \Illuminate\View\View
     */
    public function show(ShowFileRequest $request, File $file)
    {
        $generatedBreadcrumbs = FolderService::breadcrumbs($file->folder, true);

        $breadcrumbs = new BreadcrumbsService($generatedBreadcrumbs);
        $breadcrumbs->pushToEnd($file->name, null);
        $breadcrumbs = $breadcrumbs->get();

        return view('files.show', compact('file', 'breadcrumbs'));
    }

    /**
     * Download file
     *
     * @param DownloadFileRequest $request
     * @param File $file
     * @return mixed
     */
    public function download(DownloadFileRequest $request, File $file)
    {
        $storage = $request->user()->storage;
        $path = $storage->name . '/' . $file->uniq_id . '.' . $file->extension;

        return StorageFacade::disk('local')->download($path, $file->name);
    }

    /**
     * Add file to the cloud
     *
     * @param StoreFileRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Rename existing file in the cloud
     *
     * @param RenameFileRequest $request
     * @param File $file
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rename(RenameFileRequest $request, File $file)
    {
        $result = FileService::rename($file, $request->name);
        $message = $result ? 'Renamed!' : 'File with the same name already exists';
        return redirect()->back()->with($result ? 'success' : 'error', $message);
    }

    /**
     * Delete file from the cloud
     *
     * @param DeleteFileRequest $request
     * @param File $file
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function delete(DeleteFileRequest $request, File $file)
    {
        $result = FileService::delete($request->user()->storage, $file);
        return redirect()->back()->with($result ? 'success' : 'error', $result ? 'Deleted!' : 'Something went wrong');
    }
}
