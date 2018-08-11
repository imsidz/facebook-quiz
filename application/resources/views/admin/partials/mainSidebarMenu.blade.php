@foreach($items as $item)
    <?php
            $itemHash = md5($item->title);
    ?>
    <li@lm-attrs($item) @lm-endattrs>
        @if($item->link) <a@lm-attrs($item->link) @lm-endattrs href="{!! !empty($item->url()) ? $item->url() : 'javascript:;' !!}" @if($item->hasChildren()) data-toggle="collapse" data-target="#{{ $itemHash }}" @endif>
            @if ($item->attr('icon') || $item->data('icon'))
                <i class="@if($item->attr('icon')){{ $item->attr('icon') }} @else {{$item->data('icon')}} @endif" @if ($item->data('icon-color')) style="color: {{@$item->data('icon-color')}}" @endif></i>
            @else
                <i class="fa fa-fw fa-angle-double-right"></i>
            @endif
            <span @if ($item->data('color')) style="color: {{$item->data('color')}};" @endif>{!! $item->title !!}</span>
            @if($item->data('new'))
                <span class="label pull-right bg-red">New!</span>
            @endif
            @if($item->hasChildren()) <i class="fa fa-fw fa-caret-down"></i> @endif
        </a>
        @else
            @if ($item->attr('icon') || $item->data('icon'))
                <i class="@if($item->attr('icon')){{ $item->attr('icon') }} @else {{$item->data('icon')}} @endif"></i>
            @else
                <i class="fa fa-fw fa-angle-double-right"></i>
            @endif
            <span>{!! $item->title !!}</span>
        @endif
        @if($item->hasChildren())
            <ul id="{{ $itemHash }}" class="collapse">
                @include('admin.partials.mainSidebarMenu',
        array('items' => $item->children()))
            </ul>
        @endif
    </li>
    @if($item->divider)
        <li{{!! Lavary\Menu\Builder::attributes($item->divider) !!}}></li>
    @endif
@endforeach
