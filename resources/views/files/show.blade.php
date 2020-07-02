@extends('layouts.app')

@section('content')
    <div class="back">
        @include('breadcrumbs', ['breadcrumbs' => $breadcrumbs])
    </div>
    <hr>
    @if($file->public_url)
        <div class="alert-info p-3 d-flex justify-content-between align-items-center mb-2 flex-wrap">
            <p class="mb-0"><strong>Attention! </strong> Your file is visible now for everybody in the web</p>
            <button onclick="document.getElementById('switch-public-form').submit()" class="btn btn-sm btn-primary">
                Hide file
            </button>
        </div>
    @endif
    <div class="actions mb-2 p-3 bg-white d-flex align-items-center flex-wrap">
        <div class="d-flex flex-column mr-4 mb-sm-2">
            <p class="mb-0"><strong>File:</strong> {{ $file->name }}</p>
            <p class="mb-0"><strong>Size:</strong> {{ round($file->size / 1000) }}kb</p>
        </div>
        <div class="d-flex flex-wrap file-buttons">
            <a class="btn btn-sm btn-primary mr-2 flex-grow-1" href="{{ route('files.download', $file) }}"
               target="_blank">Download</a>
            <button class="btn btn-sm btn-primary mr-2 flex-grow-1" id="rename-button" data-toggle="modal"
                    data-target="#renameFileModal">
                Rename
            </button>
            @if(!$file->public_url)
                <button class="btn btn-sm btn-primary flex-grow-1"
                        onclick="document.getElementById('switch-public-form').submit()">
                    Generate public URL
                </button>
            @endif
            <form id="switch-public-form" action="{{ route('files.switchPublic', $file) }}"
                  method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
    <div class="info mb-2">
        @if($file->public_url)
            <strong>Public URL:</strong>
            @php
                $route = route('files.public.show', $file->public_url)
            @endphp
            <a href="{{ $route }}">{{ $route }}</a>
        @endif
    </div>
    <div class="preview">
        @if(in_array($file->extension, ['png', 'jpg', 'jpeg', 'gif']))
            <img src="{{ route('files.host', $file) }}" class="preview-image">
        @elseif(in_array($file->extension, ['pdf', 'txt']))
            <a href="{{ route('files.host', $file) }}" class="btn btn-outline-primary" target="_blank">
                Show document's content
            </a>
        @elseif($file->extension === 'docx')
            <a href="{{ route('files.word.host', $file) }}" class="btn btn-outline-primary">
                Show document's content
            </a>
        @elseif($file->extension === 'mp4')
            <video src="{{ route('files.host', $file) }}" controls="controls"></video>
        @else
            <hr>
            Sorry, we can't show content of that file in browser,
            but you can always download it for detailed reading
            <hr>
        @endif
    </div>
@endsection

@section('modals')
    <div class="modal fade" id="renameFileModal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="POST" action="{{ route('files.rename', $file) }}" class="modal-content">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Rename file</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" class="form-control"
                               name="name" placeholder="Enter new filename"
                               value="{{ pathinfo($file->name, PATHINFO_FILENAME) }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Rename</button>
                </div>
            </form>
        </div>
    </div>
@endsection
