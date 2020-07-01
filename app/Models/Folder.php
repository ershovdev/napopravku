<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    protected $fillable = ['storage_id', 'name', 'uniq_id', 'parent_id'];

    public function getRouteKeyName()
    {
        return 'uniq_id';
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function storage()
    {
        return $this->belongsTo(Storage::class);
    }

    public function subFolders()
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }
}
