<div class="path">
    @foreach($breadcrumbs as $b)
        @if($b['url'])
            <a href="{{ $b['url'] }}">{{ $b['name'] }}</a>
        @else
            {{ $b['name'] }}
        @endif

        @if(!$loop->last)
            <i class="fa fa-angle-right"></i>
        @endif
    @endforeach
</div>
