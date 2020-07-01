<?php

namespace App\Services;

use App\Models\Folder;
use App\Models\Storage;
use App\Services\Helpers\FilesystemHelper;
use Illuminate\Support\Facades\Storage as StorageFacade;

class FolderService
{
    public static function getPath(Folder $folder)
    {
        $result = '';
        $curr = $folder;

        while($curr) {
            $result = $curr->name . '/' . $result;
            $curr = $curr->parent;
        }

        return $result;
    }

    public static function store(Storage $storage, string $name, ?int $parent_id)
    {
        $uniq_id = FilesystemHelper::generateRandomName();

        Folder::create([
            'storage_id' => $storage->id,
            'uniq_id' => $uniq_id,
            'name' => $name,
            'parent_id' => $parent_id ?? null,
        ]);

        StorageFacade::disk('local')->makeDirectory($storage->name . '/' . $uniq_id);
    }
}
