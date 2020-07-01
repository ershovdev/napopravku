<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteFileRequest;
use App\Http\Requests\DownloadFileRequest;
use App\Http\Requests\HostFileRequest;
use App\Http\Requests\RenameFileRequest;
use App\Http\Requests\ShowFileRequest;
use App\Http\Requests\StoreFileRequest;
use App\Models\File;
use App\Services\BreadcrumbsService;
use App\Services\FolderService;
use App\Services\Helpers\FilesystemHelper;
use Illuminate\Support\Facades\Storage as StorageFacade;
use App\Services\FileService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller
{
    /**
     * Get response for the private file hosting
     *
     * @param Request $request
     * @param File $file
     * @return BinaryFileResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function hostPrivateFile(HostFileRequest $request, File $file)
    {
        $storageName = $file->public_url ? $file->storage->name : null;
        return FileService::getFileResponse($storageName, $file);
    }

    /**
     * Show private word file
     *
     * @param HostFileRequest $request
     * @param File $file
     * @return \Illuminate\View\View
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    public function hostPrivateWordFile(HostFileRequest $request, File $file)
    {
        $storageName = $file->public_url ? $file->storage->name : null;

        $objWriter = FileService::getWordResponse($storageName, $file);
        return view('files.word', compact('objWriter', 'file'));
    }

    /**
     * Get response for the public file hosting (when public_url of the file is generated)
     *
     * @param Request $request
     * @param string $publicUrl
     * @return BinaryFileResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function hostPublicFile(Request $request, string $publicUrl)
    {
        $file = File::where('public_url', $publicUrl)->firstOrFail();
        $storageName = $file->public_url ? $file->storage->name : null;

        return FileService::getFileResponse($storageName, $file);
    }

    /**
     * Show public word file (when public_url of the file is generated)
     *
     * @param Request $request
     * @param string $publicUrl
     * @return \Illuminate\View\View
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    public function hostPublicWordFile(Request $request, string $publicUrl)
    {
        $file = File::where('public_url', $publicUrl)->firstOrFail();
        $storageName = $file->public_url ? $file->storage->name : null;

        $objWriter = FileService::getWordResponse($storageName, $file);
        return view('files.public.word', compact('objWriter', 'file'));
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
     * Show file (public page)
     *
     * @param Request $request
     * @param string $publicUrl
     * @return \Illuminate\View\View
     */
    public function showPublic(Request $request, string $publicUrl)
    {
        $file = File::where('public_url', $publicUrl)->firstOrFail();
        $url = route('files.public.show', ['public_url' => $publicUrl]);

        return view('files.public.show', compact('file', 'url'));
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
     * Download file (public possibility, any user can do this)
     *
     * @param string $publicUrl
     * @return mixed
     */
    public function downloadPublic(string $publicUrl)
    {
        $file = File::where('public_url', $publicUrl)->firstOrFail();
        $path = $file->storage->name . '/' . $file->uniq_id . '.' . $file->extension;
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

    /**
     * Switch public visibility of the file
     *
     * @param Request $request
     * @param File $file
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchPublic(Request $request, File $file)
    {
        $hadPublicUrl = $file->public_url;
        $result = $file->update([
            'public_url' => $hadPublicUrl ? null : FilesystemHelper::generateRandomName(),
        ]);

        if ($hadPublicUrl) {
            $message = $result ? "Your file hidden now" : 'Something went wrong';
        } else {
            $route = route('files.public.show', $file->public_url);
            $message = $result ? "Now your file visible by link (you can find it below)" : 'Something went wrong';
        }

        return redirect()->back()->with($result ? 'success' : 'error', $message);
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
