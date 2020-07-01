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
            $breadcrumbs->pushToStart(self::ROOT_NAME, null);
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
}
