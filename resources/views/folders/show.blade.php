@extends('layouts.app')

@section('content')

    @if(isset($parent->parent))
        <a class="mr-2" href="{{ route('folders.show', $parent->parent) }}">Back</a>
    @elseif(!isset($parent->parent) && Route::currentRouteName() !== 'folders.root.show')
        <a class="mr-2" href="{{ route('folders.root.show') }}">Back</a>
    @else
        <div class="mr-2">&nbsp;</div> <!-- Just as stub for fixation of the interface -->
    @endif

    <div class="path mb-2">
        Current path: {{ $path ?? '/' }}
    </div>

    <hr>

    <div class="actions mb-2">
        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#createFileModal">
            Upload file
        </button>
        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#createFolderModal">
            Create folder
        </button>
    </div>

    <hr>

    <div class="file-explorer">
        @if(count($folders) === 0 && count($files) === 0)
            Empty
        @endif

        @foreach($files as $file)
            <div class="file mb-2">
                <i class="fa fa-file mr-2"></i>
                <a href="{{ route('files.show', $file) }}">
                    {{ $file->name }}
                </a>
                <a href="#" class="text-danger ml-2">
                    <i class="fa fa-trash"
                       onclick="document.getElementById('delete_image_{{ $file->id }}').submit()"></i>
                </a>
                <form id="delete_image_{{ $file->id }}" action="{{ route('files.delete', $file) }}"
                      method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        @endforeach

        @foreach($folders as $folder)
            <div class="directory mb-2">
                <i class="fa fa-folder mr-2"></i>
                <a href="{{ route('folders.show', $folder) }}">
                    {{ $folder->name }}
                </a>
            </div>
        @endforeach
    </div>
@endsection

@section('modals')
    <div class="modal fade" id="createFileModal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Создать файл</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="file-form" method="POST" action="{{ route('files.store') }}"
                          class="" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="file">
                        <input type="hidden" name="folder" value="{{ isset($parent) ? $parent->id : '' }}">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    <button onclick="document.getElementById('file-form').submit();" class="btn btn-primary">
                        Загрузить
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createFolderModal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="POST" action="{{ route('folders.store') }}" class="modal-content">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Создать папку</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" class="form-control" name="name" placeholder="Введите название папки">
                        <input type="hidden" name="parent" value="{{ isset($parent) ? $parent->id : '' }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary">Создать</button>
                </div>
            </form>
        </div>
    </div>
@endsection
