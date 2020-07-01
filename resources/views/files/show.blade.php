@extends('layouts.app')

@section('content')
    <div class="back">
        @if($file->folder)
            <a href="{{ route('folders.show', $file->folder) }}">Back to folder</a>
        @else
            <a href="{{ route('folders.root.show') }}">Back to the root</a>
        @endif
        <br>
        Current path: {{ $path }}
    </div>
    <hr>
    <div class="info mb-2">
        <strong>File:</strong> {{ $file->name }}
        <br>
        <strong>Size:</strong> {{ round($file->size / 1000) }}кб
    </div>
    <div class="actions mb-2">
        <a class="btn btn-sm btn-primary" href="{{ route('files.download', $file) }}" target="_blank">Download</a>
        <button class="btn btn-sm btn-primary" id="rename-button" data-toggle="modal" data-target="#renameFileModal">
            Rename
        </button>
    </div>
    <div class="preview">
        <iframe src="{{ route('files.host', $file) }}" frameborder="0"
                class="w-100 m-0 p-0" scrolling="no" height="400px"></iframe>
    </div>
@endsection

@section('modals')
    <div class="modal fade" id="renameFileModal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="POST" action="{{ route('files.rename', $file) }}" class="modal-content">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Переименовать файл</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" class="form-control"
                               name="name" placeholder="Введите новое имя"
                               value="{{ pathinfo($file->name, PATHINFO_FILENAME) }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary">Переименовать</button>
                </div>
            </form>
        </div>
    </div>
@endsection
