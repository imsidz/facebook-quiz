@extends('admin/layout')

@section('content')
    <h1>Short codes</h1>
    <p>Use these shortcodes in widgets to display dynamic content. Its is similar to wordpress shortcodes.</p>
    <p>
        <b>Usage:</b>
        You can use these shortcodes like you use Wordpress shortcodes.
    </p>
    <h3>Example</h3>
    <ul>
        <li>
            <h4>Basic usage</h4>
            If the short code is 'leaderboard', you can use:
            <br/>
            <code>
                [leaderboard]
            </code>
            <br/>
        </li>
        <li>
            <h4>Adding attributes</h4>
            To add an attribute 'limit' with value '10' to the shortcode, you can use the code below:
            <br/>
            <code>
                [leaderboard limit="10"]
            </code>
            <br/>
        </li>
    </ul>

    <br/>
    <h3>Available shortcodes</h3>
        <ul class="list-group">
            @foreach($shortCodes as $shortCodeKey => $shortCode)
                <li class="list-group-item">
                    <h4 class="text-primary">{{$shortCode['name']}}</h4>
                    <p><code>[{{$shortCodeKey}}]</code></p>
                    <p>{{$shortCode['description']}}</p>
                    @if(!empty($shortCode['attributes']) && is_array($shortCode['attributes']))
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Attributes</h5>
                                <table class="table table-bordered">
                                    @foreach($shortCode['attributes'] as $attribute)
                                        <tr>
                                            <td>
                                                <b>{{$attribute['attribute']}}</b>
                                            </td>
                                            <td>
                                                {{$attribute['description']}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>
@stop