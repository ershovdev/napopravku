<?php

namespace App\Services\Helpers;

class FilesystemHelper
{
    /**
     * Generate unique random name for folder or file
     *
     * @return string
     */
    public static function generateRandomName()
    {
        return date('mdYHis') . uniqid();
    }

    /**
     * If filename have dublicates - we need to add it's index number to the end of the filename,
     * so that function adding number at the end in brackets
     *
     * @param string $filename
     * @param int $i
     * @return string
     */
    public static function addNumberToFile(string $filename, int $i)
    {
        return pathinfo($filename, PATHINFO_FILENAME) .
            ' ('.$i.')' . '.' .
            pathinfo($filename, PATHINFO_EXTENSION);
    }
}
