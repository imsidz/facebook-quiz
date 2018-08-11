@foreach($step->getErrors() as $error)
    <div class="alert alert-danger">
        @if(is_array($error))
            <p><b>{{$error['title']}}</b></p>
            <p><small>{!! $error['message'] !!}</small></p>
        @else
        {{{$error}}}
        @endif
    </div>
@endforeach