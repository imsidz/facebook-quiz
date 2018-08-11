{{nl2br($msg)}}
<br/>
@if(!@empty($config['email']['commonFooterMessage']))
    {{ nl2br($config['email']['commonFooterMessage']) }}
@endif