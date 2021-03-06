<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = ['folder_id', 'storage_id', 'uniq_id', 'name', 'extension', 'size', 'public_url'];

    public function getRouteKeyName()
    {
        return 'uniq_id';
    }

    public function storage()
    {
        return $this->belongsTo(Storage::class);
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public static function getRootFiles(Storage $storage)
    {
        return File::where([
            ['folder_id', null],
            ['storage_id', $storage->id],
        ])->get();
    }
}
