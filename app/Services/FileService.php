<?php

namespace App\Services;

use App\Models\File;
use App\Models\Folder;
use App\Models\Storage;
use App\Services\Helpers\FilesystemHelper;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Facades\Storage as StorageFacade;
use PhpOffice\PhpWord\IOFactory;

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
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function getPrivateFileResponse(Storage $storage, File $file)
    {
        $path = self::getRealPath($storage, $file);
        if (!FileFacade::exists($path)) abort(404);

        return response()->file(self::getRealPath($storage, $file));
    }

    public static function getPrivateWordResponse(Storage $storage, File $file)
    {
        $path = self::getRealPath($storage, $file);
        if (!FileFacade::exists($path)) abort(404);

        $phpWord = IOFactory::load($path);
        $objWriter = IOFactory::createWriter($phpWord, 'HTML');

        return $objWriter;
    }

    /**
     * Find all file neighbors in the root or in the folder (if folder provided)
     *
     * @param Folder|null $folder
     * @return mixed
     */
    public static function isDublicate(?Folder $folder, string $name, ?string $extension)
    {
        $extension = $extension ? '.'.$extension : '';

        if ($folder) {
            $neighbors = $folder->files;
        } else {
            $neighbors = File::where('folder_id', null)->get();
        }

        foreach ($neighbors as $n) {
            if ($n->name === $name . $extension) return true;
        }

        return false;
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

        $folder = $folder_id ? Folder::find($folder_id) : null;
        $result = self::isDublicate($folder, $clientName, null);

        if ($result) {
            $i = 2;
            while ($result) {
                $newName = FilesystemHelper::addNumberToFile($clientName, $i);
                $result = self::isDublicate($folder, $newName, null);
                if ($result) $i++;
            }

            $clientName = FilesystemHelper::addNumberToFile($clientName, $i);
        }

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
