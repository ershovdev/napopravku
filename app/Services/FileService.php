<?php


namespace App\Services;


use App\Models\File;
use App\Models\Storage;
use App\Models\User;
use App\Services\Helpers\FilesystemHelper;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Facades\Storage as StorageFacade;

class FileService
{
    public static function getRealPath(Storage $storage, $file)
    {
        $storageName = $storage->name;
        return storage_path('app/'.$storageName . '/' . $file->uniq_id . '.' . $file->extension);
    }

    public static function getPrivateFileResponse(Storage $storage, $file)
    {
        $path = self::getRealPath($storage, $file);
        if (!FileFacade::exists($path)) abort(404);

        $file = FileFacade::get($path);
        $type = FileFacade::mimeType($path);

        $response = response()->make($file, 200);
        $response->header('Content-Type', $type);

        return $response;
    }

    public static function store(Storage $storage, ?int $folder_id, $file)
    {
        $name = FilesystemHelper::generateRandomName();
        $clientName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        File::create([
            'folder_id' => $folder_id,
            'storage_id' => $storage->id,
            'uniq_id' => $name,
            'name' => $clientName,
            'extension' => $extension,
            'size' => $file->getSize(),
        ]);

        return StorageFacade::disk('local')->putFileAs($storage->name, $file, $name . '.' . $extension);
    }

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

    public static function delete(Storage $storage, $file)
    {
        $filename = $file->name;
        $result = $file->delete();

        if ($result) {
            return StorageFacade::disk('local')
                ->delete($storage->name . '/' . $file->uniq_id . '.' . $file->extension);
        } else {
            return false;
        }
    }
}
