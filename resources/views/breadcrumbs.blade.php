<div class="path">
    @foreach($breadcrumbs as $b)
        @if($b['url'])
            <a href="{{ $b['url'] }}">{{ $b['name'] }}</a>
        @else
            {{ $b['name'] }}
        @endif

        @if(!$loop->last)
            <i class="fa fa-angle-right mx-1"></i>
        @endif
    @endforeach
</div>
