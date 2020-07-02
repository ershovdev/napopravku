@extends('layouts.app')

@section('content')
    <div class="actions mb-4">
        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#createFileModal">
            Upload file here
        </button>
        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#createFolderModal">
            Create folder here
        </button>
    </div>
    <hr class="mb-1">
    @include('breadcrumbs', ['breadcrumbs' => $breadcrumbs])
    <hr class="mt-1">
    <div class="file-explorer">
        @if(Route::currentRouteName() === 'folders.show')
            <div class="mb-2">
                @if(isset($parent) && $parent->parent)
                    <a href="{{ route('folders.show', $parent->parent) }}">
                        <strong><i class="fa fa-angle-left mr-1"></i> Go back</strong>
                    </a>
                @else
                    <a href="{{ route('folders.root.show') }}">
                        <strong><i class="fa fa-angle-left mr-1"></i> Go back</strong>
                    </a>
                @endif
            </div>
        @endif

        @if(count($folders) === 0 && count($files) === 0)
            <strong>Empty.</strong>
        @endif

        @foreach($files as $file)
            <div class="file mb-2">
                <i class="fa fa-file mr-2"></i>
                <a href="{{ route('files.show', $file) }}">
                    {{ $file->name }}
                </a>
                <a class="text-danger ml-2">
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
                @if(count($folder->files) === 0 && count($folder->subFolders) === 0)
                    <a class="text-danger ml-2">
                        <i class="fa fa-trash"
                           onclick="document.getElementById('delete_folder_{{ $folder->id }}').submit()"></i>
                    </a>
                    <form id="delete_folder_{{ $folder->id }}" action="{{ route('folders.delete', $folder) }}"
                          method="POST" style="display: none;">
                        @csrf
                    </form>
                @endif
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
                    <h5 class="modal-title" id="exampleModalLabel">Upload file</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="file-alert" class="alert alert-danger" style="display: none;"></div>
                    <form id="file-form" method="POST" action="{{ route('files.store') }}"
                          class="" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="file" id="file">
                        <input type="hidden" name="folder" value="{{ isset($parent) ? $parent->id : '' }}">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button onclick="return Validate();" class="btn btn-primary">
                        Upload
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
                    <h5 class="modal-title" id="exampleModalLabel">Create folder</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" class="form-control" name="name"
                               placeholder="Type folder name here" required>
                        <input type="hidden" name="parent" value="{{ isset($parent) ? $parent->id : '' }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        window.onload = function () {
            const validExtensions = ['png', 'gif', 'jpeg', 'jpg', 'txt', 'pdf', 'doc', 'docx', 'mp4', 'zip', 'rtf'];
            let fileAlert = document.getElementById('file-alert');
            let fileObject = document.getElementById('file');

            window.Validate = function Validate() {
                let file = fileObject.files[0];

                if (file.size / 1024 / 1024 >= 10) {
                    fileAlert.innerHTML = 'Sorry, file is too big for our server';
                    fileAlert.style.display = 'block';
                    return false;
                }

                if (file.type.includes('zip') || file.type.includes('image') || file.type.includes('pdf') ||
                    file.type.includes('text') || file.type.includes('word') || file.type.includes('mp4')) {
                    let filename = fileObject.value;
                    console.log(file.value);
                    if (filename.length > 0) {
                        let valid = false;
                        console.log(filename);
                        for (let i = 0; i < validExtensions.length; i++) {
                            let currEx = validExtensions[i];
                            console.log(filename.substr(filename.length - currEx.length, currEx.length).toLowerCase());
                            if (filename.substr(filename.length - currEx.length, currEx.length).toLowerCase() ===
                                currEx.toLowerCase()) {
                                valid = true;
                                document.getElementById('file-form').submit();
                                break;
                            }
                        }

                        if (!valid) {
                            fileAlert.innerHTML = 'Sorry, file is invalid';
                            fileAlert.style.display = 'block';
                            return false;
                        }
                    }
                } else {
                    fileAlert.innerHTML = 'Sorry, you can\'t load that file on our server';
                    fileAlert.style.display = 'block';
                    return false;
                }

                return true;
            }
        }
    </script>
@endsection
