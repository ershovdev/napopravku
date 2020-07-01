<?php


namespace App\Services;


use App\Models\File;
use App\Models\Storage;
use App\Models\User;
use App\Services\Helpers\FilesystemHelper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Facades\Storage as StorageFacade;

class FileService
{
    /**
     * Get real path of the image in application
     *
     * @param Storage $storage
     * @param File $file
     * @return string
     */
    public static function getRealPath(Storage $storage, File $file)
    {
        $storageName = $storage->name;
        return storage_path('app/'.$storageName . '/' . $file->uniq_id . '.' . $file->extension);
    }

    /**
     * Get response for private file hosting
     *
     * @param Storage $storage
     * @param File $file
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function getPrivateFileResponse(Storage $storage, File $file)
    {
        $path = self::getRealPath($storage, $file);
        if (!FileFacade::exists($path)) abort(404);

        $file = FileFacade::get($path);
        $type = FileFacade::mimeType($path);

        $response = response()->make($file, 200);
        $response->header('Content-Type', $type);

        return $response;
    }

    public static function addFileToBreadcrumbs(array $breadcrumbs)
    {

    }

    /**
     * Add file to the root or to the folder (if folder_id provided)
     *
     * @param Storage $storage
     * @param int|null $folder_id
     * @param UploadedFile $file
     * @return bool
     */
    public static function store(Storage $storage, ?int $folder_id, UploadedFile $uploadedFile)
    {
        $name = FilesystemHelper::generateRandomName();
        $clientName = $uploadedFile->getClientOriginalName();
        $extension = $uploadedFile->getClientOriginalExtension();

        $file = File::create([
            'folder_id' => $folder_id,
            'storage_id' => $storage->id,
            'uniq_id' => $name,
            'name' => $clientName,
            'extension' => $extension,
            'size' => $uploadedFile->getSize(),
        ]);

        if ($file) {
            return StorageFacade::disk('local')
                ->putFileAs($storage->name, $uploadedFile, $name . '.' . $extension);
        } else {
            return false;
        }
    }

    /**
     * Rename existing file in the cloud
     *
     * @param File $file
     * @param string $newName
     * @return bool
     */
    public static function rename(File $file, string $newName)
    {
        if ($file->folder) {
            $neighbors = $file->folder->files;
        } else {
            $neighbors = File::where('folder_id', null)->get();
        }

        foreach ($neighbors as $n) {
            if ($n->name === $newName . '.' . $n->extension) return false;
        }

        $file->update([
            'name' => $newName . '.' . $file->extension,
        ]);

        return true;
    }

    /**
     * Delete file from the cloud
     *
     * @param Storage $storage
     * @param File $file
     * @return bool
     * @throws \Exception
     */
    public static function delete(Storage $storage, File $file)
    {
        $result = $file->delete();

        if ($result) {
            $path = $storage->name . '/' . $file->uniq_id . '.' . $file->extension;
            return StorageFacade::disk('local')->delete($path);
        } else {
            return false;
        }
    }
}
