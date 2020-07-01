<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HostFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (request()->file->public_url) return true;
        return request()->file->storage_id == request()->user()->storage->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
