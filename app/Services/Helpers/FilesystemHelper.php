<?php

namespace App\Services\Helpers;

class FilesystemHelper
{
    public static function generateRandomName()
    {
        return date('mdYHis') . uniqid();
    }
}
