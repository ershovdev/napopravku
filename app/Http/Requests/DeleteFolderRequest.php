<?php

namespace App\Http\Requests;

use App\Models\Folder;
use Illuminate\Foundation\Http\FormRequest;

class DeleteFolderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'folder' => [
                function ($attribute, $value, $fail) {
                    $folder = Folder::where('uniq_id', $value)->first();
                    if ($folder->subFolders || $folder->files) {
                        return $fail('Folder is not empty!');
                    }
                    return true;
                }
            ],
        ];
    }
}
