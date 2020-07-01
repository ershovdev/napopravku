@extends('layouts.app')

@section('content')
    <div class="alert-info p-3 mb-2 d-flex justify-content-between align-items-center">
        @if(request()->user() && request()->user()->storage->id == $file->storage_id)
            It's how any users will see public page of your file
            <a href="{{ route('files.show', $file) }}" class="btn btn-primary">Go back to owner view</a>
        @else
            It's a public file, you can view it, but can't edit or delete
        @endif
    </div>

    <div class="actions mb-2 p-3 bg-white d-flex align-items-center">
        <div class="d-flex flex-column mr-4">
            <p class="mb-0"><strong>File:</strong> {{ $file->name }}</p>
            <p class="mb-0"><strong>Size:</strong> {{ round($file->size / 1000) }}kb</p>
        </div>
        <a class="btn btn-sm btn-primary mr-2" href="{{ route('files.public.download', $file->public_url) }}"
           target="_blank">
            Download
        </a>
    </div>

    <div class="preview">
        @if(in_array($file->extension, ['png', 'jpg', 'jpeg', 'gif']))
            <img src="{{ route('files.public.host', $file->public_url) }}" width="400px">
        @elseif($file->extension === 'pdf')
            <a href="{{ route('files.public.host', $file->public_url) }}" class="btn btn-outline-primary"
               target="_blank">
                Show document's content
            </a>
        @elseif(in_array($file->extension, ['doc', 'docx']))
            <a href="{{ route('files.public.word.host', $file->public_url) }}" class="btn btn-outline-primary">
                Show document's content
            </a>
        @else
            <hr>
            Sorry, we can't show content of that file in browser,
            but you can always download it for detailed reading
            <hr>
        @endif
    </div>
@endsection