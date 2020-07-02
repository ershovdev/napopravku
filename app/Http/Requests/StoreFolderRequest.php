<?php

namespace App\Http\Requests;

use App\Models\Folder;
use Illuminate\Foundation\Http\FormRequest;

class StoreFolderRequest extends FormRequest
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
            'name' => 'required|string|max:40',
            'parent' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $folder = Folder::find($value);

                        if (!$folder) {
                            $fail('Something went wrong');
                            return;
                        }

                        if ($folder->storage->id != request()->user()->storage->id) {
                            $fail('Unauthorized');
                            return;
                        }
                    }
                }
            ],
        ];
    }
}
