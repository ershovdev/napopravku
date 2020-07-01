@extends('layouts.app')

@section('content')
    <a href="{{ route('files.public.show', $file->public_url) }}">Вернуться назад</a>
    <hr>
    <div class="word bg-white p-4">
        {{ $objWriter->save('php://output') }}
    </div>
@endsection
