<?php

namespace App\Models;

use App\Models\User;
use App\Services\Helpers\FilesystemHelper;
use App\Models\File;
use Illuminate\Support\Facades\Storage as StorageFacade;
use Illuminate\Database\Eloquent\Model;

class Storage extends Model
{
    protected $fillable = ['user_id', 'name'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function folders()
    {
        return $this->hasMany(Folder::class);
    }
}
