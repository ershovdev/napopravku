<?php

namespace App\Services;

use App\Models\Folder;
use App\Models\Storage;
use App\Services\Helpers\FilesystemHelper;
use Illuminate\Support\Facades\Storage as StorageFacade;

class FolderService
{
    private const ROOT_NAME = 'My disk';

    /**
     * Generate breadcrumbs for current folder
     *
     * @param Folder|null $folder
     * @param bool $lastWithLink - if true, last folder will be with link too
     * @return array
     */
    public static function breadcrumbs(Folder $folder = null, bool $lastWithLink = false)
    {
        $breadcrumbs = new BreadcrumbsService();

        if (!$folder) {
            $link = $lastWithLink ? route('folders.root.show') : null;
            $breadcrumbs->pushToStart(self::ROOT_NAME, $link);
            return $breadcrumbs->get();
        }

        $curr = $folder;

        while ($curr) {
            $breadcrumbs->pushToStart($curr->name, route('folders.show', $curr));
            $curr = $curr->parent;
        }

        $breadcrumbs->pushToStart(self::ROOT_NAME, route('folders.root.show'));

        if (!$lastWithLink) {
            $key = $breadcrumbs->getLastKey();
            $breadcrumbs->modify($key, $breadcrumbs->getName($key), null);
        }

        return $breadcrumbs->get();
    }

    /**
     * Find all folder neighbors in the root or in the folder (if folder provided)
     *
     * @param Folder|null $folder
     * @param string $name
     * @return bool
     */
    public static function isDublicate(?Folder $folder, string $name)
    {
        if ($folder) {
            $neighbors = $folder->subFolders;
        } else {
            $neighbors = Folder::where([
                ['parent_id', null],
                ['storage_id', request()->user()->storage->id],
            ])->get();
        }

        foreach ($neighbors as $n) {
            if ($n->name === $name) return true;
        }

        return false;
    }

    /**
     * Create new folder in root or in another folder (if parent_id provided)
     *
     * @param Storage $storage
     * @param string $name
     * @param int|null $parent_id - null if root
     *
     * @return bool
     */
    public static function create(Storage $storage, string $name, ?int $parent_id)
    {
        $uniq_id = FilesystemHelper::generateRandomName();

        $folder = null;
        if ($parent_id) {
            $folder = Folder::where('id', $parent_id)->first();
        }

        $result = self::isDublicate($folder, $name);

        if ($result) {
            $i = 2;
            while ($result) {
                $newName = FilesystemHelper::addNumberToFolder($name, $i);
                $result = self::isDublicate($folder, $newName);
                if ($result) $i++;
            }

            $name = FilesystemHelper::addNumberToFolder($name, $i);
        }


        $folder = Folder::create([
            'storage_id' => $storage->id,
            'uniq_id' => $uniq_id,
            'name' => $name,
            'parent_id' => $parent_id ?? null,
        ]);

        if ($folder) {
            return StorageFacade::disk('local')->makeDirectory($storage->name . '/' . $uniq_id);
        } else {
            return false;
        }
    }

    public static function delete(Storage $storage, Folder $folder)
    {
        $result = $folder->delete();

        if ($result) {
            return StorageFacade::disk('local')->deleteDirectory($storage->name . '/' . $folder->uniq_id);
        } else {
            return false;
        }
    }
}
