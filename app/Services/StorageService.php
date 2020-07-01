<?php

namespace App\Services;

use App\Services\Helpers\FilesystemHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Storage as StorageFacade;

class StorageService
{
    /**
     * Creating the personal user's storage of files
     *
     * @param string|null $directory_name - Name for personal user directory
     * @return string - Name of created directory
     */
    public static function createStorage(string $directory_name = null) : string
    {
        if (!$directory_name)
            $directory_name = FilesystemHelper::generateRandomName();

        StorageFacade::disk('local')->makeDirectory($directory_name);
        return $directory_name;
    }
}
